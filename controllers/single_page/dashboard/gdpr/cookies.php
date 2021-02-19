<?php

namespace Concrete\Package\Gdpr\Controller\SinglePage\Dashboard\Gdpr;

use A3020\Gdpr\Controller\DashboardController;

final class Cookies extends DashboardController
{
    public function view()
    {
        return $this->action('/dashboard/gdpr/cookies/consent');
    }
}
