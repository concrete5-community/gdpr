<?php

namespace Concrete\Package\Gdpr\Controller\SinglePage\Dashboard\Gdpr;

use A3020\Gdpr\Controller\DashboardController;
use A3020\Gdpr\Job\JobInstallService;
use A3020\Gdpr\Tracking\Code;
use Concrete\Core\Routing\Redirect;

final class Settings extends DashboardController
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
        // User logs
        $this->set('removeBasedOnUserId', (bool) $this->config->get('gdpr.settings.logs.remove_based_on_user_id', true));
        $this->set('removeBasedOnUsername', (bool) $this->config->get('gdpr.settings.logs.remove_based_on_username', false));
        $this->set('removeBasedOnEmailAddress', (bool) $this->config->get('gdpr.settings.logs.remove_based_on_email_address', false));

        // Tracking
        $this->set('disableConcreteBackground', (bool) $this->config->get('concrete.white_label.background_url'));
        $this->set('trackingCodeFound', $this->hasTrackingCode());
        $this->set('disableTrackingCode', (bool) $this->config->get('gdpr.settings.tracking.disabled', false));

        // Data Transfer
        $this->set('dataTransferDownloadDaysValid', (int) $this->config->get('gdpr.settings.data_transfer.days_valid', 10));
        $this->set('enableSubmittingDefaultData', (bool) $this->config->get('gdpr.settings.data_transfer.submit_data', true));

        $this->set('enableJobToRemoveFormSubmissions', $this->jobInstallService->isInstalled('gdpr_remove_form_submissions'));
        $this->set('enableJobToProcessDataTransferRequests', $this->jobInstallService->isInstalled('gdpr_process_data_transfer_requests'));
    }

    public function save()
    {
        if (!$this->token->validate('a3020.gdpr.settings')) {
            $this->flash('error', $this->token->getErrorMessage());

            return Redirect::to('/dashboard/gdpr/settings');
        }

        // User logs
        $this->config->save('gdpr.settings.logs.remove_based_on_user_id', (bool) $this->post('removeBasedOnUserId'));
        $this->config->save('gdpr.settings.logs.remove_based_on_username', (bool) $this->post('removeBasedOnUsername'));
        $this->config->save('gdpr.settings.logs.remove_based_on_email_address', (bool) $this->post('removeBasedOnEmailAddress'));

        // Tracking
        if ((bool) $this->post('disableConcreteBackground')) {
            $this->config->save('concrete.white_label.background_url', 'none');
        } else {
            $this->config->save('concrete.white_label.background_url', false);
        }

        $this->config->save('gdpr.settings.tracking.disabled', (bool) $this->post('disableTrackingCode'));

        // Data Transfer
        $this->config->save('gdpr.settings.data_transfer.days_valid', (int) $this->post('dataTransferDownloadDaysValid'));
        $this->config->save('gdpr.settings.data_transfer.submit_data', (bool) $this->post('enableSubmittingDefaultData'));

        // Automated Jobs
        $this->jobInstallService->installOrDeinstall('gdpr_remove_form_submissions', $this->post('enableJobToRemoveFormSubmissions'));
        $this->jobInstallService->installOrDeinstall('gdpr_process_data_transfer_requests', $this->post('enableJobToProcessDataTransferRequests'));

        $this->flash('success', t('Your settings have been saved.'));

        return Redirect::to('/dashboard/gdpr/settings');
    }

    private function hasTrackingCode()
    {
        /** @var Code $code */
        $code = $this->app->make(Code::class);

        return $code->has();
    }
}
