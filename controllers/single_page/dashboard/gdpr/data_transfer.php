<?php

namespace Concrete\Package\Gdpr\Controller\SinglePage\Dashboard\Gdpr;

use A3020\Gdpr\Controller\DashboardController;
use Concrete\Core\Routing\Redirect;

final class DataTransfer extends DashboardController
{
    public function view()
    {
        return Redirect::to('/dashboard/gdpr/data_transfer/requests');
    }
}
