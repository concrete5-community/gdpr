<?php

namespace Concrete\Package\Gdpr\Controller\SinglePage\Dashboard\Gdpr\DataTransfer;

use A3020\Gdpr\BlockType\BlockTypeInstallService;
use A3020\Gdpr\Controller\DashboardController;
use A3020\Gdpr\Job\JobInstallService;
use Concrete\Core\Routing\Redirect;

final class Settings extends DashboardController
{
    /** @var JobInstallService */
    protected $jobInstallService;

    /** @var BlockTypeInstallService */
    protected $blockTypeInstallService;

    public function on_start()
    {
        parent::on_start();

        $this->jobInstallService = $this->app->make(JobInstallService::class);
        $this->blockTypeInstallService = $this->app->make(BlockTypeInstallService::class);
    }

    public function view()
    {
        $this->set('dataTransferDownloadDaysValid', (int) $this->config->get('gdpr.settings.data_transfer.days_valid', 10));
        $this->set('enableSubmittingDefaultData', (bool) $this->config->get('gdpr.settings.data_transfer.submit_data', true));

        $this->set('enableJobToProcessDataTransferRequests', $this->jobInstallService->isInstalled('gdpr_process_data_transfer_requests'));
        $this->set('enableInstallBlock', $this->blockTypeInstallService->isInstalled('gdpr_data_transfer_request'));
    }

    public function save()
    {
        if (!$this->token->validate('a3020.gdpr.data_transfer.settings')) {
            $this->flash('error', $this->token->getErrorMessage());

            return Redirect::to('/dashboard/gdpr/data_transfer/settings');
        }

        $this->config->save('gdpr.settings.data_transfer.days_valid', (int) $this->post('dataTransferDownloadDaysValid'));
        $this->config->save('gdpr.settings.data_transfer.submit_data', (bool) $this->post('enableSubmittingDefaultData'));

        $this->jobInstallService->installOrDeinstall('gdpr_process_data_transfer_requests', $this->post('enableJobToProcessDataTransferRequests'));
        $this->blockTypeInstallService->installOrDeinstall('gdpr_data_transfer_request', $this->post('enableInstallBlock'));


        $this->flash('success', t('Your settings have been saved.'));

        return Redirect::to('/dashboard/gdpr/data_transfer/settings');
    }
}
