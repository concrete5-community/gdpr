<?php

namespace Concrete\Package\Gdpr\Controller\SinglePage\Dashboard;

use Concrete\Core\Page\Controller\DashboardPageController;

final class Gdpr extends DashboardPageController
{
    public function view()
    {
        return $this->action('/dashboard/gdpr/scan/overall');
    }
}
