<?php

namespace A3020\Gdpr\Installer;

use A3020\Gdpr\Check\CheckRepository;
use A3020\Gdpr\Entity\Check;
use A3020\Gdpr\Traits\PackageTrait;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Database\DatabaseStructureManager;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Single;
use Doctrine\ORM\EntityManager;

class Installer
{
    /** @var Repository */
    private $config;

    /** @var CheckRepository */
    private $checkRepository;
    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(Repository $config, EntityManager $entityManager, CheckRepository $checkRepository)
    {
        $this->config = $config;
        $this->entityManager = $entityManager;
        $this->checkRepository = $checkRepository;
    }

    /**
     * @param \Concrete\Core\Package\Package $pkg
     */
    public function install($pkg)
    {
        $this->refreshEntities();
        $this->dashboardPages($pkg);
        $this->installChecks();
    }

    private function dashboardPages($pkg)
    {
        $pages = [
            '/dashboard/gdpr' => t('GDPR'),
            '/dashboard/gdpr/scan' => t('Scan'),
            '/dashboard/gdpr/scan/overall' => t('Overall'),
            '/dashboard/gdpr/scan/blocks' => t('Blocks'),
            '/dashboard/gdpr/scan/tables' => t('Tables'),
            '/dashboard/gdpr/scan/packages' => t('Add-ons'),
            '/dashboard/gdpr/cleanup' => t('Cleanup'),
            '/dashboard/gdpr/cleanup/express_forms' => t('Express Forms'),
            '/dashboard/gdpr/cleanup/legacy_forms' => t('Legacy Forms'),
            '/dashboard/gdpr/cleanup/orphaned_files' => t('Orphaned Files'),
            '/dashboard/gdpr/cleanup/logs' => t('Logs'),
            '/dashboard/gdpr/data_transfer' => t('Data Transfer'),
            '/dashboard/gdpr/data_transfer/requests' => t('Requests'),
            '/dashboard/gdpr/data_transfer/settings' => t('Settings'),
            '/dashboard/gdpr/data_breach' => t('Data Breach'),
            '/dashboard/gdpr/data_breach/notify_users' => t('Notify Users'),
            '/dashboard/gdpr/cookies' => t('Cookies'),
            '/dashboard/gdpr/cookies/consent' => t('Consent'),
            '/dashboard/gdpr/checklist' => t('Checklist'),
            '/dashboard/gdpr/settings' => t('Settings'),
        ];

        foreach ($pages as $path => $name) {
            /** @var Page $page */
            $page = Page::getByPath($path);
            if (!$page || $page->isError()) {
                $page = Single::add($path, $pkg);
            }

            if ($page->getCollectionName() !== $name) {
                $page->update([
                    'cName' => $name
                ]);
            }
        }
    }

    private function installChecks()
    {
        $json = json_decode(file_get_contents(__DIR__.'/checks.json'), true);

        foreach ($json as $aCheck) {
            $check = new Check();
            $check->setHandle($aCheck['handle']);
            $check->setName($aCheck['name']);
            $check->setDescription($aCheck['description']);
            $check->setCategory($aCheck['category']);

            $this->checkRepository->updateOrCreate($check);
        }

        $this->checkRepository->flush();
    }

    private function refreshEntities()
    {
        $manager = new DatabaseStructureManager($this->entityManager);
        $manager->clearCacheAndProxies();
    }
}
