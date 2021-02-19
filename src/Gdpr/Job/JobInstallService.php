<?php

namespace A3020\Gdpr\Job;

use Concrete\Core\Job\Job;
use Concrete\Core\Support\Facade\Package as PackageFacade;

class JobInstallService
{
    const PACKAGE_HANDLE = 'gdpr';

    /**
     * @param string $handle
     *
     * @return bool
     */
    public function isInstalled($handle)
    {
        return (bool) Job::getByHandle($handle);
    }

    /**
     * @param string $handle
     * @param bool $enable
     *
     * @return bool
     */
    public function installOrDeinstall($handle, $enable)
    {
        if ($enable) {
            return $this->install($handle);
        }

        return $this->deinstall($handle);
    }

    /**
     * @param string $handle
     *
     * @return bool
     */
    protected function install($handle)
    {
        if ($this->isInstalled($handle)) {
            return true;
        }

        /** @see \Concrete\Core\Package\PackageService */
        $pkg = PackageFacade::getByHandle(self::PACKAGE_HANDLE);

        Job::installByPackage($handle, $pkg);

        return true;
    }

    /**
     * @param string $handle
     *
     * @return bool
     */
    protected function deinstall($handle)
    {
        $job = Job::getByHandle($handle);
        if ($job) {
            $job->uninstall($handle);
        }

        return true;
    }
}
