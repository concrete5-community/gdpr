<?php

namespace Concrete\Package\Gdpr\Controller\SinglePage\Dashboard\Gdpr;

use A3020\Gdpr\Controller\DashboardController;

final class Cleanup extends DashboardController
{
    public function view()
    {
        return $this->action('/dashboard/gdpr/cleanup/express_forms');
    }
}
