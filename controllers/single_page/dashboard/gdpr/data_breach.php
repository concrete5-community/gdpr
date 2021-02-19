<?php

namespace Concrete\Package\Gdpr\Controller\SinglePage\Dashboard\Gdpr;

use A3020\Gdpr\Controller\DashboardController;
use Concrete\Core\Routing\Redirect;

final class DataBreach extends DashboardController
{
    public function view()
    {
        return Redirect::to('/dashboard/gdpr/data_breach/notify_users');
    }
}
