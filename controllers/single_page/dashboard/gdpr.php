<?php

namespace Concrete\Package\Gdpr\Controller\SinglePage\Dashboard;

use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Routing\Redirect;

final class Gdpr extends DashboardPageController
{
    public function view()
    {
        return Redirect::to('/dashboard/gdpr/scan/overall');
    }
}
