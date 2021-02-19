<?php

namespace A3020\Gdpr\Listener\OnUserDelete;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Database\Connection\Connection;
use Psr\Log\LoggerInterface;
use Exception;

class DeleteLogs
{
    /**
     * @var \Concrete\Core\Database\Connection\Connection
     */
    private $db;

    /**
     * @var \Concrete\Core\Config\Repository\Repository
     */
    private $config;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    public function __construct(Connection $db, Repository $config, LoggerInterface $logger)
    {
        $this->db = $db;
        $this->config = $config;
        $this->logger = $logger;
    }

    /**
     * Deletes logs that relate to a user if a user is deleted
     *
     * @param \Concrete\Core\User\Event\DeleteUser $event
     */
    public function handle($event)
    {
        try {
            $ui = $event->getUserInfoObject();

            if ($this->config->get('gdpr.settings.logs.remove_based_on_user_id', true) === false) {
                $this->deleteBasedOnUserId($ui->getUserID());
            }

            if ($this->config->get('gdpr.settings.logs.remove_based_on_username', true) === false) {
                $this->deleteBasedOnUsername($ui->getUserName());
            }

            if ($this->config->get('gdpr.settings.logs.remove_based_on_email_address', true) === false) {
                $this->deleteBasedOnEmailAddress($ui->getUserEmail());
            }
        } catch (Exception $e) {
            $this->logger->debug($e->getMessage());
        }
    }

    private function deleteBasedOnUserId($userId)
    {
        $this->db->executeQuery("DELETE FROM Logs WHERE uID = ?", [$userId]);
    }

    private function deleteBasedOnUsername($username)
    {
        $this->db->executeQuery("DELETE FROM Logs WHERE message LIKE '%?%'", [$username]);
    }

    private function deleteBasedOnEmailAddress($emailAddress)
    {
        $this->db->executeQuery("DELETE FROM Logs WHERE message LIKE '%?%'", [$emailAddress]);
    }
}
