<?php

namespace Concrete\Package\Gdpr\Controller\SinglePage\Dashboard\Gdpr\Cleanup;

use A3020\Gdpr\Controller\DashboardController;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Logging\LogEntry;

final class Logs extends DashboardController
{
    protected $maxResults = 100;

    public function view()
    {
        $this->set('blockExpressFormSubmissions', $this->shouldInclude('block_express_form_submissions'));
        $this->set('sentEmails', $this->shouldInclude('sent_emails'));
        $this->set('deletedUsers', $this->shouldInclude('deleted_users'));
        $this->set('atSymbol', $this->shouldInclude('at_symbol', false));

        $this->set('maxResults', $this->maxResults);
        $this->set('logs', $this->getLogs());
    }

    public function save()
    {
        if (!$this->token->validate('a3020.gdpr.cleanup.logs')) {
            $this->flash('error', $this->token->getErrorMessage());

            return $this->action('/dashboard/gdpr/cleanup/logs');
        }

        $this->config->save('gdpr.cleanup.logs.block_express_form_submissions', (bool ) $this->post('blockExpressFormSubmissions'));
        $this->config->save('gdpr.cleanup.logs.sent_emails', (bool ) $this->post('sentEmails'));
        $this->config->save('gdpr.cleanup.logs.deleted_users', (bool ) $this->post('deletedUsers'));
        $this->config->save('gdpr.cleanup.logs.at_symbol', (bool ) $this->post('atSymbol'));

        return $this->action('/dashboard/gdpr/cleanup/logs');
    }

    public function bulk()
    {
        if (!$this->token->validate('gdpr.cleanup.logs.bulk')) {
            $this->flash('error', $this->token->getErrorMessage());

            return $this->action('/dashboard/gdpr/cleanup/logs');
        }

        if ($this->post('action') === 'delete' && !empty($this->post('logs', []))) {
            $this->flash('success', t('The selected log entries have been deleted.'));

            $this->deleteLogs($this->post('logs', []));
        }

        return $this->action('/dashboard/gdpr/cleanup/logs');
    }

    /**
     * @return LogEntry[]
     */
    private function getLogs()
    {
        /** @var Connection $db */
        $db = $this->app['database']->connection();

        $entries = [];
        $filtered = false;

        $query = $db->createQueryBuilder();
        $query->select('l.logID')
            ->from('Logs', 'l');

        if ($this->shouldInclude('block_express_form_submissions')) {
            $filtered = true;

            // The exact phrase would be something like: "Template Used: block_express_form_submission"
            $query->orWhere('l.message like "%'.$db->quote('%: block_express_form_submission%').'%"');
        }

        if ($this->shouldInclude('sent_emails')) {
            $filtered = true;

            $query->orWhere("l.channel = '".LOG_TYPE_EMAILS."'");
        }

        if ($this->shouldInclude('deleted_users')) {
            $filtered = true;

            //SELECT u.uID FROM Logs l
            //LEFT JOIN Users u
            //ON u.uID = l.uID
            //WHERE u.uID IS NULL AND l.uID != 0
            $query->leftJoin('l', 'Users', 'u', 'u.uID = l.uID');
            $query->orWhere("u.uID IS NULL AND l.uID != 0");
        }

        if ($this->shouldInclude('at_symbol', false)) {
            $filtered = true;

            $query->orWhere('l.message like "%@%"');
        }

        $query->setMaxResults($this->maxResults);

        if (!$filtered) {
            return $entries;
        }

        $query = $query->execute();

        while ($id = $query->fetchColumn()) {
            $e = LogEntry::getByID($id);
            if (is_object($e)) {
                $entries[] = $e;
            }
        }

        return $entries;
    }

    private function shouldInclude($setting, $default = true)
    {
        return (bool) $this->config->get('gdpr.cleanup.logs.'.$setting, $default);
    }

    private function deleteLogs($logIds)
    {
        $logIds = array_map('intval', $logIds);

        /** @var Connection $db */
        $db = $this->app['database']->connection();

        $qMarks = str_repeat('?,', count($logIds) - 1) . '?';
        $db->executeQuery("DELETE FROM Logs WHERE logID IN (".$qMarks.")", $logIds);
    }
}
