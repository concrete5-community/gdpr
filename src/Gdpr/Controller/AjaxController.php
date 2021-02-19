<?php

namespace A3020\Gdpr\Controller;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Page\Page;

class AjaxController extends \Concrete\Core\Controller\Controller implements ApplicationAwareInterface
{
    use ApplicationAwareTrait;

    /** @var Repository $config */
    protected $config;

    public function on_start()
    {
        $this->checkPermissions();
        $this->config = $this->app->make(Repository::class);
    }

    public function checkPermissions()
    {
        $page = Page::getByPath('/dashboard/gdpr');
        $cp = new \Permissions($page);
        if (!$page || $page->isError() || !$cp->canViewPage()) {
            die(t('Access Denied'));
        }
    }
}
