<?php

namespace Concrete\Package\Gdpr\Controller\SinglePage\Dashboard\Gdpr\Scan;

use A3020\Gdpr\Controller\DashboardController;
use A3020\Gdpr\Form\Helper;

final class Packages extends DashboardController
{
    /** @see \A3020\Gdpr\Ajax\Scan\Packages */

    public function view()
    {
        $this->set('customPackages', implode("\n", $this->config->get('gdpr.scan.packages.custom', [])));
    }

    public function save()
    {
        if (!$this->token->validate('a3020.gdpr.scan.packages')) {
            $this->flash('error', $this->token->getErrorMessage());

            return $this->action('/dashboard/gdpr/scan/packages');
        }

        /** @var Helper $helper */
        $helper = $this->app->make(Helper::class);

        $this->config->save('gdpr.scan.packages.custom', $helper->convertTextArea($this->post('customPackages')));

        return $this->action('/dashboard/gdpr/scan/packages');
    }
}
