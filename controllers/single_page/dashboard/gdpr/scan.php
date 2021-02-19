<?php

namespace Concrete\Package\Gdpr\Controller\SinglePage\Dashboard\Gdpr;

use A3020\Gdpr\Controller\DashboardController;

final class Scan extends DashboardController
{
    public function view()
    {
        return $this->action('/dashboard/gdpr/scan/overall');
    }
}
