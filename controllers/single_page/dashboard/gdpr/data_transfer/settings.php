<?php

namespace Concrete\Package\Gdpr\Controller\SinglePage\Dashboard\Gdpr\DataTransfer;

use A3020\Gdpr\BlockType\BlockTypeInstallService;
use A3020\Gdpr\Controller\DashboardController;
use A3020\Gdpr\Installer\TaskInstaller;
use A3020\Gdpr\Job\JobInstallService;
use A3020\Gdpr\Traits\PackageTrait;

final class Settings extends DashboardController
{
    use PackageTrait;

    /** @var JobInstallService */
    protected $jobInstallService;

    /** @var TaskInstaller */
    protected $taskInstaller;

    /** @var BlockTypeInstallService */
    protected $blockTypeInstallService;

    public function on_start()
    {
        parent::on_start();

        $this->jobInstallService = $this->app->make(JobInstallService::class);
        $this->taskInstaller = $this->app->make(TaskInstaller::class, ['package' => $this->getPackage()]);
        $this->blockTypeInstallService = $this->app->make(BlockTypeInstallService::class);
        $this->set('isVersion9', $this->isVersion9());
    }

    public function view()
    {
        $this->set('dataTransferDownloadDaysValid', (int) $this->config->get('gdpr.settings.data_transfer.days_valid', 10));
        $this->set('enableSubmittingDefaultData', (bool) $this->config->get('gdpr.settings.data_transfer.submit_data', true));

        if ($this->isVersion9()) {
            $this->set('enableJobToProcessDataTransferRequests', $this->taskInstaller->isInstalled('gdpr_process_data_transfer_requests'));
        } else {
            $this->set('enableJobToProcessDataTransferRequests', $this->jobInstallService->isInstalled('gdpr_process_data_transfer_requests'));
        }
        $this->set('enableInstallBlock', $this->blockTypeInstallService->isInstalled('gdpr_data_transfer_request'));
    }

    public function save()
    {
        if (!$this->token->validate('a3020.gdpr.data_transfer.settings')) {
            $this->flash('error', $this->token->getErrorMessage());

            return $this->action('/dashboard/gdpr/data_transfer/settings');
        }

        $this->config->save('gdpr.settings.data_transfer.days_valid', (int) $this->post('dataTransferDownloadDaysValid'));
        $this->config->save('gdpr.settings.data_transfer.submit_data', (bool) $this->post('enableSubmittingDefaultData'));

        if ($this->isVersion9()) {
            if ($this->post('enableJobToProcessDataTransferRequests')) {
                $this->taskInstaller->install('gdpr_process_data_transfer_requests');
            } else {
                $this->taskInstaller->uninstall('gdpr_process_data_transfer_requests');
            }
        } else {
            $this->jobInstallService->installOrDeinstall('gdpr_process_data_transfer_requests', $this->post('enableJobToProcessDataTransferRequests'));
        }
        $this->blockTypeInstallService->installOrDeinstall('gdpr_data_transfer_request', $this->post('enableInstallBlock'));


        $this->flash('success', t('Your settings have been saved.'));

        return $this->action('/dashboard/gdpr/data_transfer/settings');
    }
}
