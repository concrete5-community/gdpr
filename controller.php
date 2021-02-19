<?php

namespace Concrete\Package\Gdpr;

use A3020\Gdpr\Installer\Installer;
use A3020\Gdpr\Installer\Uninstaller;
use A3020\Gdpr\Provider\GdprServiceProvider;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Package\Package;
use Concrete\Core\Support\Facade\Package as PackageFacade;
use Gettext\Translations;

final class Controller extends Package
{
    protected $pkgHandle = 'gdpr';
    protected $appVersionRequired = '8.2.1';
    protected $pkgVersion = '1.1.2';
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
        $provider = $this->app->make(GdprServiceProvider::class);
        $provider->register();
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
