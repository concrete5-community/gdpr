<?php

namespace Concrete\Package\Gdpr\Controller\SinglePage\Dashboard\Gdpr\Scan;

use A3020\Gdpr\Controller\DashboardController;
use A3020\Gdpr\Tracking\Code;
use Concrete\Core\Geolocator\GeolocatorService;
use Concrete\Core\Http\Request;

final class Overall extends DashboardController
{
    public function view()
    {
        $this->set('config', $this->config);

        $this->set('isConnectionSecure', $this->isConnectionSecure());
        $this->set('hasTrackingCode', $this->app->make(Code::class)->has());
        $this->set('geoPluginStatus', $this->getGeoPluginStatus());
    }

    private function isConnectionSecure()
    {
        /** @var Request $request */
        $request = $this->app->make('request');

        // Hm, this sometimes returns false although https is used.
        $isSecure = $request->isSecure();

        if ($isSecure) {
            return true;
        }

        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            return true;
        }

        return false;
    }

    /**
     * Get GeoPlugin status
     *
     * null: GeoLocator is not available in this c5 version, or none of the geolocators is active.
     * false: geoPlugin is not currently active (it requests http://www.geoplugin.net by default).
     * true: geoPlugin is active and may be used.
     *
     * @return null
     */
    private function getGeoPluginStatus()
    {
        if (!class_exists(GeolocatorService::class)) {
            return null;
        }

        $geoLocatorService = $this->app->make(GeolocatorService::class);
        $geoLocator = $geoLocatorService->getCurrent();
        if (!$geoLocator) {
            return null;
        }

        return $geoLocator->getGeolocatorHandle() === 'geoplugin';
    }
}
