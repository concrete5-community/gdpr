<?php

namespace Concrete\Package\Gdpr;

use A3020\Gdpr\DataTransfer\Task\ProcessDataTransferRequestsController;
use A3020\Gdpr\Form\Task\DeleteExpressFormEntriesController;
use A3020\Gdpr\Form\Task\DeleteLegacyFormEntriesController;
use A3020\Gdpr\Help\HelpServiceProvider;
use A3020\Gdpr\Installer\Installer;
use A3020\Gdpr\Installer\TaskInstaller;
use A3020\Gdpr\Installer\Uninstaller;
use A3020\Gdpr\Job\JobInstallService;
use Concrete\Core\Command\Task\Manager as TaskManager;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Package\Package;
use Concrete\Core\Support\Facade\Package as PackageFacade;
use Gettext\Translations;

final class Controller extends Package
{
    protected $pkgHandle = 'gdpr';
    protected $appVersionRequired = '8.4.4';
    protected $pkgVersion = '1.9.0';
    protected $pkgAutoloaderRegistries = [
        'src/Gdpr' => '\A3020\Gdpr',
    ];

    public function getPackageName()
    {
        return t('GDPR');
    }

    public function getPackageDescription()
    {
        return t('Helps you to comply with the GDPR regulation.');
    }

    public function on_start()
    {
        $app = $this->app;

        /** @var @var \A3020\Gdpr\Provider\GdprServiceProvider $provider */
        $provider = $app->make(\A3020\Gdpr\Provider\GdprServiceProvider::class);
        $provider->register();

        /** @var @var \A3020\Gdpr\Provider\CookieServiceProvider $provider */
        $provider = $app->make(\A3020\Gdpr\Provider\CookieServiceProvider::class);
        $provider->register();

        /** @var HelpServiceProvider $helpServiceProvider */
        $helpServiceProvider = $app->make(HelpServiceProvider::class);
        $helpServiceProvider->register();

        /** @var TaskManager $taskManager */
        $taskManager = $app->make(TaskManager::class);
        /** @var TaskInstaller $taskInstaller */
        $taskInstaller = $app->make(TaskInstaller::class, ['package' => $this->getPackageEntity()]);
        if ($taskInstaller->isInstalled('gdpr_process_data_transfer_requests')) {
            $taskManager->extend('gdpr_process_data_transfer_requests', static function() use ($app) {
                return $app->make(ProcessDataTransferRequestsController::class);
            });
        }
        if ($taskInstaller->isInstalled('gdpr_remove_form_submissions')) {
            $taskManager->extend('gdpr_remove_form_submissions', static function() use ($app) {
                return $app->make(DeleteExpressFormEntriesController::class);
            });
        }
        if ($taskInstaller->isInstalled('gdpr_remove_legacy_form_submissions')) {
            $taskManager->extend('gdpr_remove_legacy_form_submissions', static function() use ($app) {
                return $app->make(DeleteLegacyFormEntriesController::class);
            });
        }
    }

    public function install()
    {
        $pkg = parent::install();

        $installer = $this->app->make(Installer::class);
        $installer->install($pkg);
    }

    public function upgrade()
    {
        parent::upgrade();

        /** @see \Concrete\Core\Package\PackageService */
        $pkg = PackageFacade::getByHandle($this->pkgHandle);

        $installer = $this->app->make(Installer::class);
        $installer->install($pkg);

        if (class_exists(TaskManager::class)) {
            /** @var JobInstallService $jobInstaller */
            $jobInstaller = $this->app->make(JobInstallService::class);
            /** @var TaskInstaller $taskInstaller */
            $taskInstaller = $this->app->make(TaskInstaller::class, ['package' => $this->getPackageEntity()]);
            if ($jobInstaller->isInstalled('gdpr_process_data_transfer_requests')) {
                $jobInstaller->installOrDeinstall('gdpr_process_data_transfer_requests', false);
                $taskInstaller->install('gdpr_process_data_transfer_requests');
            }
            if ($jobInstaller->isInstalled('gdpr_remove_form_submissions')) {
                $jobInstaller->installOrDeinstall('gdpr_remove_form_submissions', false);
                $taskInstaller->install('gdpr_remove_form_submissions');
            }
            if ($jobInstaller->isInstalled('gdpr_remove_legacy_form_submissions')) {
                $jobInstaller->installOrDeinstall('gdpr_remove_legacy_form_submissions', false);
                $taskInstaller->install('gdpr_remove_legacy_form_submissions');
            }
        }
    }

    public function uninstall()
    {
        /** @see \Concrete\Core\Package\PackageService */
        $pkg = PackageFacade::getByHandle($this->pkgHandle);

        $uninstaller = $this->app->make(Uninstaller::class);
        $uninstaller->uninstall($pkg);

        parent::uninstall();
    }
    
    public function getTranslatableStrings(Translations $translations)
    {
        $config = $this->app->make(Repository::class);

        // See also src/Gdpr/Cookie/Configuration.php
        $message = $config->get('gdpr.cookies.consent.message');
        $dismissButtonText = $config->get('gdpr.cookies.consent.dismiss_button_text');
        $allowButtonText = $config->get('gdpr.cookies.consent.allow_button_text');
        $denyButtonText = $config->get('gdpr.cookies.consent.deny_button_text');
        $policyLinkText = $config->get('gdpr.cookies.consent.policy_link_text');

        $translations->insert('CookieBar', $message ? $message : 'This website uses cookies to ensure you get the best experience on our website.');
        $translations->insert('CookieBar', $dismissButtonText ? $dismissButtonText : 'Got it!');
        $translations->insert('CookieBar', $allowButtonText ? $allowButtonText : 'Allow cookies');
        $translations->insert('CookieBar', $denyButtonText ? $denyButtonText : 'Decline');
        $translations->insert('CookieBar', $policyLinkText ? $policyLinkText :'Learn more');
    }
}
