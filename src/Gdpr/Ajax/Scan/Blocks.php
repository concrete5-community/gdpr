<?php

namespace A3020\Gdpr\Ajax\Scan;

use A3020\Gdpr\Controller\AjaxController;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Page\Page;

class Blocks extends AjaxController
{
    public function view()
    {
        $json['data'] = $this->getRecords();

        return $this->app->make(ResponseFactory::class)->json($json);
    }

    /**
     * Return a list of pages with blocks that might contain user data
     *
     * @return array
     */
    private function getRecords()
    {
        $records = [];

        $ignoreCoreBlocks = (bool) $this->config->get('gdpr.scan.block_types.ignore_core_blocks', false);

        foreach ($this->getBlockTypes() as $handle => $why) {
            /** @var \Concrete\Core\Entity\Block\BlockType\BlockType $blockType */
            $blockType = BlockType::getByHandle($handle);
            if (!$blockType) {
                continue;
            }

            $isCoreBlock = $this->isCoreBlock($blockType);
            if ($ignoreCoreBlocks && $isCoreBlock) {
                continue;
            }

            foreach ($this->getPagesWhereBlockIsUsed($blockType->getBlockTypeID()) as $page) {
                $records[] = [
                    'page_name' => $page->getCollectionName(),
                    'page_url' => $page->getCollectionLink(),
                    'block_type_id' => $blockType->getBlockTypeID(),
                    'block_type' => $blockType->getBlockTypeName(),
                    'block_type_handle' => $handle,
                    'block_icon' => $this->getBlockTypeIcon($blockType),
                    'is_core_block' => $isCoreBlock,
                    'why' => $why,
                ];
            }
        }

        return $records;
    }

    /**
     * @param int $blockTypeId
     *
     * @return Page[]
     */
    private function getPagesWhereBlockIsUsed($blockTypeId)
    {
        /** @var Connection $db */
        $db = $this->app['database']->connection();

        $records = $db->fetchAll('
            SELECT cv.cID FROM CollectionVersionBlocks cvb
            INNER JOIN (
                SELECT cID, MAX(cvID) as cvID FROM CollectionVersions cv
                WHERE cv.cvIsApproved = 1
                GROUP BY cID
            ) as cv
            ON cv.cID = cvb.cID
            WHERE cvb.cvID = cv.cvID AND bID IN (
                SELECT bID FROM Blocks WHERE btID = ?
            )
            GROUP BY cv.cID
        ', [ $blockTypeId]);

        $pageIds = array_column($records, 'cID');

        $pages = [];
        foreach ($pageIds as $pageId) {
            $page = Page::getByID($pageId);
            if (!$page || $page->isError()) {
                continue;
            }

            if ($page->isAdminArea()) {
                continue;
            }

            if ($page->isSystemPage()) {
                continue;
            }

            // Skip e.g. "Page Forbidden"
            if (empty($page->getCollectionName())) {
                continue;
            }

            $pages[] = $page;
        }

        return $pages;
    }

    /**
     * @param \Concrete\Core\Entity\Block\BlockType\BlockType $blockType
     *
     * @return bool
     */
    private function isCoreBlock($blockType)
    {
        if ($blockType->getPackageID() > 0) {
            return false;
        }

        if (!is_dir(DIRNAME_CORE.'/'.DIRNAME_BLOCKS.'/'.$blockType->getBlockTypeHandle())) {
            return false;
        }

        return true;
    }

    private function getBlockTypes()
    {
        $blockTypes = [
            'core_conversation' => t('Because the %s and %s are stored.', 'commentRatingIP', 'commentRatingUserID'),
            'express_form' => t('Because an email address is stored and because certain form fields might store personal data.'),
            'form' => t('Because the %s and %s are stored and because certain form fields might store personal data.', 'recipientEmail', 'uID'),
            'd3_mailchimp' => t('Because the email address is sent to MailChimp.'),
            'mailchimp' => t('Because the email address is sent to MailChimp.'),
            'survey' => t('Because the %s and %s are stored.', 'uID', 'ipAddress'),
        ];

        $config = $this->app->make(Repository::class);

        foreach ($config->get('gdpr.scan.block_types.custom', []) as $handle) {
            $blockTypes[$handle] = '-';
        }

        return $blockTypes;
    }

    /**
     * @param $blockType
     *
     * @return string
     */
    private function getBlockTypeIcon($blockType)
    {
        /** @var \Concrete\Core\Application\Service\Urls $service */
        $service = $this->app->make('helper/concrete/urls');

        return $service->getBlockTypeIconURL($blockType);
    }
}
