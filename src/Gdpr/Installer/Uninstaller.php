<?php

namespace A3020\Gdpr\Installer;

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Support\Facade\Log;
use Exception;

class Uninstaller
{
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function uninstall($pkg)
    {
        try {
            $this->connection->executeQuery("DROP TABLE IF EXISTS GdprChecks");
            $this->connection->executeQuery("DROP TABLE IF EXISTS GdprBlockScanStatus");
            $this->connection->executeQuery("DROP TABLE IF EXISTS GdprTableScanStatus");
            $this->connection->executeQuery("DROP TABLE IF EXISTS GdprDataTransferFiles");
            $this->connection->executeQuery("DROP TABLE IF EXISTS GdprDataTransferRequests");
        } catch (Exception $e) {
            Log::addDebug($e->getMessage());
        }
    }
}
