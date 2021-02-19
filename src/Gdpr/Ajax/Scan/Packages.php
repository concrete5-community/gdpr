<?php

namespace A3020\Gdpr\Ajax\Scan;

use A3020\Gdpr\Controller\AjaxController;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Entity\Package;
use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Support\Facade\Package as PackageFacade;

class Packages extends AjaxController
{
    public function view()
    {
        $json['data'] = $this->getRecords();

        return $this->app->make(ResponseFactory::class)->json($json);
    }

    /**
     * Return a list of packages that are known to be problematic with GDPR
     *
     * @return array
     */
    private function getRecords()
    {
        $records = [];

        foreach ($this->getPackages() as $handle => $why) {
            /** @see \Concrete\Core\Package\PackageService */
            $pkg = PackageFacade::getByHandle($handle);

            /** @var Package $pkg */
            if (!$pkg) {
                continue;
            }

            $records[] = [
                'package_icon' => $this->getPackageIcon($pkg),
                'package_name' => $pkg->getPackageName(),
                'package_description' => $pkg->getPackageDescription(),
                'why' =>  $why,
            ];
        }

        return $records;
    }

    /**
     * @return array
     */
    private function getPackages()
    {
        $config = $this->app->make(Repository::class);
        $packages = $config->get('gdpr::packages.default');

        foreach ($config->get('gdpr.scan.packages.custom', []) as $handle) {
            $packages[$handle] = '-';
        }

        return $packages;
    }

    /**
     * @param $pkg
     *
     * @return string
     */
    private function getPackageIcon($pkg)
    {
        /** @var \Concrete\Core\Application\Service\Urls $service */
        $service = $this->app->make('helper/concrete/urls');

        return $service->getPackageIconURL($pkg);
    }
}
