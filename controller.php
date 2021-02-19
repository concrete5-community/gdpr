<?php

namespace Concrete\Package\Gdpr;

use A3020\Gdpr\Installer\Installer;
use A3020\Gdpr\Installer\Uninstaller;
use A3020\Gdpr\Provider\GdprServiceProvider;
use Concrete\Core\Package\Package;
use Concrete\Core\Support\Facade\Package as PackageFacade;

final class Controller extends Package
{
    protected $pkgHandle = 'gdpr';
    protected $appVersionRequired = '8.2.1';
    protected $pkgVersion = '1.0';
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
}
