<?php

namespace A3020\Gdpr\Job\FormSubmissions;

use Concrete\Core\Job\Job;
use Concrete\Core\Support\Facade\Package as PackageFacade;

class Service
{
    const JOB_HANDLE = 'gdpr_remove_form_submissions';
    const PACKAGE_HANDLE = 'gdpr';

    public function isInstalled()
    {
        return (bool) Job::getByHandle(self::JOB_HANDLE);
    }

    public function installOrDeinstall($enable)
    {
        if ($enable) {
            return $this->install();
        }

        return $this->deinstall();
    }

    protected function install()
    {
        if ($this->isInstalled()) {
            return true;
        }

        /** @see \Concrete\Core\Package\PackageService */
        $pkg = PackageFacade::getByHandle(self::PACKAGE_HANDLE);

        Job::installByPackage(self::JOB_HANDLE, $pkg);

        return true;
    }

    protected function deinstall()
    {
        $job = Job::getByHandle(self::JOB_HANDLE);
        if ($job) {
            $job->uninstall(self::JOB_HANDLE);
        }

        return true;
    }
}
