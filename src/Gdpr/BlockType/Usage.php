<?php

namespace A3020\Gdpr\BlockType;

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Page\Page;

class Usage
{
    /**
     * @var Connection
     */
    private $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    /**
     * @param int $blockTypeId
     *
     * @return Page[]
     */
    public function getPages($blockTypeId)
    {
        $records = $this->db->fetchAll('
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

            // The case e.g. for desktop_* blocks
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
}
