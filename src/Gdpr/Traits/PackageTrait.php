<?php

namespace A3020\Gdpr\Traits;

use Concrete\Core\Package\PackageService;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Application as ApplicationFacade;

trait PackageTrait
{
    /** @var string */
    protected $version;

    protected function getCoreVersion()
    {
        if ($this->version === null) {
            $app = Application::getFacadeApplication();
            $this->version = $app['config']->get('concrete.version_installed');
        }

        return $this->version;
    }

    protected function isVersion9()
    {
        return version_compare($this->getCoreVersion(), '9.0.0', '>=');
    }

    protected function getPackage()
    {
        $app = ApplicationFacade::getFacadeApplication();

        return $app->make(PackageService::class)->getByHandle('gdpr');
    }
}