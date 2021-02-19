<?php

namespace A3020\Gdpr\Controller;

use Concrete\Core\Asset\AssetList;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Page\Controller\DashboardPageController;

class DashboardController extends DashboardPageController
{
    /** @var Repository $config */
    protected $config;

    public function on_before_render()
    {
        parent::on_before_render();

        $al = AssetList::getInstance();

        $al->register('javascript', 'gdpr/datatables', 'js/datatables.min.js', [], 'gdpr');
        $this->requireAsset('javascript', 'gdpr/datatables');

        $al->register('javascript', 'gdpr/gdpr', 'js/gdpr.js', [], 'gdpr');
        $this->requireAsset('javascript', 'gdpr/gdpr');

        $al->register('css', 'gdpr/style', 'css/style.css', [], 'gdpr');
        $al->register('css', 'gdpr/datatables', 'css/datatables.css', [], 'gdpr');
        $this->requireAsset('css', 'gdpr/style');
        $this->requireAsset('css', 'gdpr/datatables');
    }

    public function on_start()
    {
        parent::on_start();

        $this->config = $this->app->make(Repository::class);
    }
}
