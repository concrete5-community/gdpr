<?php

namespace Concrete\Package\Gdpr\Controller\SinglePage\Dashboard\Gdpr\Scan;

use A3020\Gdpr\Controller\DashboardController;
use A3020\Gdpr\Tracking\Code;
use Concrete\Core\Http\Request;

final class Overall extends DashboardController
{
    public function view()
    {
        $this->set('config', $this->config);

        $this->set('isConnectionSecure', $this->isConnectionSecure());
        $this->set('hasTrackingCode', $this->app->make(Code::class)->has());
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
}
