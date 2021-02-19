<?php

namespace A3020\Gdpr\Ajax\Scan;

use A3020\Gdpr\Controller\AjaxController;
use A3020\Gdpr\Entity\TableScanStatus;
use A3020\Gdpr\Scan\Table\StatusRepository;
use Concrete\Core\Block\BlockType\BlockTypeList;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Database\DatabaseStructureManager;
use Concrete\Core\Http\Response;
use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\View\View;
use Doctrine\ORM\EntityManager;
use Exception;

class Tables extends AjaxController
{
    /** @var array */
    protected $coreTables;
    protected $maxPreviewResults = 20;

    public function view()
    {
        $json['data'] = $this->getRecords();

        return $this->app->make(ResponseFactory::class)->json($json);
    }

    public function peek($tableName = null)
    {
        $view = new View('scan/tables/preview');
        $view->setPackageHandle('gdpr');
        $view->addScopeItems([
            'maxResults' => $this->maxPreviewResults,
            'columns' => $this->getColumns($tableName),
            'rows' => $this->getRows($tableName, $this->maxPreviewResults),
        ]);

        return Response::create($view->render());
    }

    public function status($tableName = null)
    {
        /** @var StatusRepository $statusRepository */
        $statusRepository = $this->app->make(StatusRepository::class);

        $status = $statusRepository->findByTableName($tableName);
        $status = $status ? $status : $this->getSystemStatus($tableName, $this->config->get('gdpr::database_tables.taken_care_of.default'));

        $view = new View('scan/tables/status');
        $view->setPackageHandle('gdpr');
        $view->addScopeItems([
            'action' => Url::to('/ccm/system/gdpr/scan/table/status/save'),
            'token' => $this->app->make('token'),
            'form' => $this->app->make('helper/form'),
            'tableName' => $tableName,
            'status' => $status,
        ]);

        return Response::create($view->render());
    }

    public function saveStatus()
    {
        if (!$this->app->make('token')->validate('gdpr.scan.tables.status')) {
            throw new Exception(t('Invalid form token'));
        }

        /** @var StatusRepository $statusRepository */
        $statusRepository = $this->app->make(StatusRepository::class);

        if ($this->post('id')) {
            $status = $statusRepository->find($this->post('id'));
            if (!$status) {
                throw new Exception('Invalid request');
            }
        } else {
            $status = new TableScanStatus();
            $status->setTableName($this->post('tableName'));
        }

        $status->setFixed($this->post('fixed'));
        $status->setNotes($this->post('notes'));

        $statusRepository->save($status);

        return $this->app->make(ResponseFactory::class)->json([
            'success' => true,
        ]);
    }

    /**
     * @param $tableName
     * @return \Doctrine\DBAL\Schema\Column[]
     */
    private function getColumns($tableName)
    {
        return $this->getSchemaManager()->listTableColumns($tableName);
    }

    private function getRows($tableName, $maxPreviewResults)
    {
        /** @var Connection $connection */
        $connection = $this->app['database']->connection();

        return $connection->fetchAll('SELECT * FROM '.$tableName.' LIMIT 0, '.$maxPreviewResults);
    }

    /**
     * Return a list of tables that might contain user data
     *
     * @return array
     */
    private function getRecords()
    {
        $this->loadCoreTables();

        $searchFor = $this->getSearchFor();
        $searchForRegex = implode('|', $searchFor);

        $takenCareOf = $this->config->get('gdpr::database_tables.taken_care_of.default');
        $ignoreFixedTables = (bool) $this->config->get('gdpr.scan.tables.ignore_fixed_tables', false);
        $ignoreEmptyTables = (bool) $this->config->get('gdpr.scan.tables.ignore_empty_tables', true);
        $ignoreCoreTables = (bool) $this->config->get('gdpr.scan.tables.ignore_core_tables', false);
        $whitelist = $this->config->get('gdpr::database_tables.whitelist');

        /** @var StatusRepository $statusRepository */
        $statusRepository = $this->app->make(StatusRepository::class);

        $records = [];
        foreach ($this->getSchemaManager()->listTables() as $table) {
            $totalRows = $this->getNumberOfRows($table->getName());
            if ($ignoreEmptyTables && $totalRows === 0) {
                continue;
            }

            $isCoreTable = $this->isCoreTable($table->getName());
            if ($ignoreCoreTables && $isCoreTable) {
                continue;
            }

            $status = $this->getStatus($table->getName(), $statusRepository, $takenCareOf);
            if ($ignoreFixedTables && $status->isFixed()) {
                continue;
            }

            if (in_array($table->getName(), $whitelist)) {
                continue;
            }

            $record = [
                'table_name' => $table->getName(),
                'table_row_total' => t2('%s row', '%s rows', $totalRows),
                'is_core_table' => $isCoreTable,
                'status' => $status,
            ];

            $columns = [];

            foreach ($table->getColumns() as $column) {
                $name = strtolower($column->getName());

                $matched = preg_match("/('.$searchForRegex.')/", $name);

                if ($matched) {
                    $columns[] = $column->getName();
                }
            }

            if (!empty($columns)) {
                $record['columns'] = $columns;
                $records[] = $record;
            }
        }

        return $records;
    }

    /**
     * Get a list of column names to search for
     *
     * We use preg_match so regexes may be used.
     *
     * @return array
     */
    private function getSearchFor()
    {
        $config = $this->app->make(Repository::class);

        $columns = array_merge($config->get('gdpr::database_columns.default'), $config->get('gdpr.scan.tables.custom', []));
        $columns = array_map('strtolower', $columns);

        return array_unique($columns);
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    private function isCoreTable($name)
    {
        return in_array($name, $this->coreTables, $name);
    }

    private function loadCoreTables()
    {
        $xml = simplexml_load_file(DIR_BASE_CORE . '/config/db.xml');
        foreach ($xml->table as $t) {
            $name = (string) $t['name'];
            $this->coreTables[] = $name;
        }

        $entityManager = $this->app->make(EntityManager::class);
        $structureManager = new DatabaseStructureManager($entityManager);
        $entities = $structureManager->getMetadatas();

        $coreEntities = array_filter($entities, function($entity) {
            return strpos($entity->getName(), 'Concrete\Core\Entity') !== false;
        });
        $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($entityManager);

        foreach ($schemaTool->getSchemaFromMetadata($coreEntities)->getTables() as $table) {
            $this->coreTables[] = $table->getName();
        }

        $list = new BlockTypeList();
        $list->includeInternalBlockTypes();
        foreach ($list->get() as $bt) {
            if ($bt->getPackageID()) {
                continue;
            }

            $filename = DIR_BASE_CORE . '/'.DIRNAME_BLOCKS . '/' . $bt->getBlockTypeHandle() . '/' . FILENAME_BLOCK_DB;
            if (!file_exists($filename)) {
                continue;
            }

            $xml = simplexml_load_file($filename);
            foreach ($xml->table as $t) {
                $name = (string)$t['name'];
                $this->coreTables[] = $name;
            }
        }

        $this->coreTables[] = 'FileSearchIndexAttributes';
        $this->coreTables[] = 'CollectionSearchIndexAttributes';
        $this->coreTables[] = 'UserSearchIndexAttributes';
        $this->coreTables[] = 'OauthUserMap';
        $this->coreTables[] = 'authTypeConcreteCookieMap';
    }

    /**
     * @param string $tableName
     *
     * @return int
     */
    private function getNumberOfRows($tableName)
    {
        /** @var Connection $connection */
        return (int) $this->app['database']->connection()->fetchColumn('SELECT COUNT(1) FROM '.$tableName);
    }

    private function getSchemaManager()
    {
        /** @var Connection $connection */
        $connection = $this->app['database']->connection();

        return $connection->getSchemaManager();
    }

    private function getSystemStatus($tableName, $takenCareOf)
    {
        $status = new TableScanStatus();
        $status->isSystemStatus(true);

        if (!isset($takenCareOf[$tableName])) {
            return $status;
        }

        $key = $takenCareOf[$tableName];
        $status->setFixed(true);

        if (isset($key['info'])) {
            $status->setNotes($key['info']);
        }

        if (isset($key['fixed']) && !$key['fixed']) {
            $status->setFixed(false);
        }

        if (isset($key['c5_version'])) {
            // If currently installed version is lower than the version needed
            // then it's not fixed. The user should upgrade the core.
            $status->setFixed(!(version_compare($this->config->get('concrete.version_installed'), $key['c5_version']) === -1));

            return $status;
        }

        return $status;
    }

    /**
     * @param string $tableName
     * @param StatusRepository $statusRepository
     * @param array $takenCareOf
     *
     * @return TableScanStatus
     */
    private function getStatus($tableName, $statusRepository, $takenCareOf)
    {
        $userStatus = $statusRepository->findByTableName($tableName);
        if ($userStatus) {
            return $userStatus;
        }

        return $this->getSystemStatus($tableName, $takenCareOf);
    }
}
