<?php

namespace Concrete\Package\Gdpr\Controller\SinglePage\Dashboard\Gdpr\Cleanup;

use A3020\Gdpr\Controller\DashboardController;
use A3020\Gdpr\Installer\TaskInstaller;
use A3020\Gdpr\Job\JobInstallService;
use A3020\Gdpr\Traits\PackageTrait;
use Concrete\Core\Database\Connection\Connection;

final class LegacyForms extends DashboardController
{
    use PackageTrait;

    /** @var JobInstallService */
    protected $jobInstallService;

    /** @var TaskInstaller */
    protected $taskInstaller;

    public function on_start()
    {
        parent::on_start();

        $this->jobInstallService = $this->app->make(JobInstallService::class);
        $this->taskInstaller = $this->app->make(TaskInstaller::class, ['package' => $this->getPackage()]);
        $this->set('isVersion9', $this->isVersion9());
    }

    public function view()
    {
        $this->set('totalFormSubmissions', $this->getTotalFormSubmissions());
        if ($this->isVersion9()) {
            $this->set('enableJobToRemoveLegacyFormSubmissions', $this->taskInstaller->isInstalled('gdpr_remove_legacy_form_submissions'));
        } else {
            $this->set('enableJobToRemoveLegacyFormSubmissions', $this->jobInstallService->isInstalled('gdpr_remove_legacy_form_submissions'));
        }
        $this->set('legacyFormsKeepDays', $this->config->get('gdpr.settings.legacy_forms.keep_days'));
    }

    public function save()
    {
        if (!$this->token->validate('a3020.gdpr.cleanup.legacy_forms.settings')) {
            $this->flash('error', $this->token->getErrorMessage());

            return $this->action('/dashboard/gdpr/cleanup/legacy_forms');
        }

        if ($this->isVersion9()) {
            if ($this->post('enableJobToRemoveLegacyFormSubmissions')) {
                $this->taskInstaller->install('gdpr_remove_legacy_form_submissions');
            } else {
                $this->taskInstaller->uninstall('gdpr_remove_legacy_form_submissions');
            }
        } else {
            $this->jobInstallService->installOrDeinstall('gdpr_remove_legacy_form_submissions', $this->post('enableJobToRemoveLegacyFormSubmissions'));
        }

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
