<?php

namespace Concrete\Package\Gdpr\Controller\SinglePage\Dashboard\Gdpr;

use A3020\Gdpr\Controller\DashboardController;

final class DataTransfer extends DashboardController
{
    public function view()
    {
        return $this->action('/dashboard/gdpr/data_transfer/requests');
    }
}
