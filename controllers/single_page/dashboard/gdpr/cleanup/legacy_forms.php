<?php

namespace Concrete\Package\Gdpr\Controller\SinglePage\Dashboard\Gdpr\Cleanup;

use A3020\Gdpr\Controller\DashboardController;
use A3020\Gdpr\Job\JobInstallService;
use Concrete\Core\Database\Connection\Connection;

final class LegacyForms extends DashboardController
{
    /** @var JobInstallService */
    protected $jobInstallService;

    public function on_start()
    {
        parent::on_start();

        $this->jobInstallService = $this->app->make(JobInstallService::class);
    }

    public function view()
    {
        $this->set('totalFormSubmissions', $this->getTotalFormSubmissions());
        $this->set('enableJobToRemoveLegacyFormSubmissions', $this->jobInstallService->isInstalled('gdpr_remove_legacy_form_submissions'));
        $this->set('legacyFormsKeepDays', $this->config->get('gdpr.settings.legacy_forms.keep_days'));
    }

    public function save()
    {
        if (!$this->token->validate('a3020.gdpr.cleanup.legacy_forms.settings')) {
            $this->flash('error', $this->token->getErrorMessage());

            return $this->action('/dashboard/gdpr/cleanup/legacy_forms');
        }

        $this->jobInstallService->installOrDeinstall('gdpr_remove_legacy_form_submissions', $this->post('enableJobToRemoveLegacyFormSubmissions'));

        $keepDays = $this->post('legacyFormsKeepDays');
        $this->config->save('gdpr.settings.legacy_forms.keep_days', $keepDays !== '' ? (int) $keepDays : null);

        $this->flash('success', t('Your settings have been saved.'));

        return $this->action('/dashboard/gdpr/cleanup/legacy_forms');
    }

    /**
     * Returns the number of form submissions
     *
     * Each submissions is stored as a set.
     * A set consists of form answers which are stored in the btFormAnswers table.
     *
     * @return int
     */
    private function getTotalFormSubmissions()
    {
        /** @var Connection $db */
        $db = $this->app->make(Connection::class);

        return (int) $db->fetchColumn('SELECT COUNT(1) FROM btFormAnswerSet');
    }
}
