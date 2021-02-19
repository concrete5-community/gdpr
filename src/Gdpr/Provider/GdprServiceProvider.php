<?php

namespace A3020\Gdpr\Provider;

use A3020\Gdpr\Listener\OnPageOutput\DisableTracking;
use A3020\Gdpr\Listener\OnUserDelete\DeleteLogs;
use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Routing\RouterInterface;

class GdprServiceProvider implements ApplicationAwareInterface
{
    use ApplicationAwareTrait;

    /** @var Repository */
    protected $config;

    public function __construct(Repository $config)
    {
        $this->config = $config;
    }

    public function register()
    {
        $this->registerRoutes();
        $this->registerListeners();
    }

    private function registerRoutes()
    {
        /** @var RouterInterface $router */
        $router = $this->app->make(RouterInterface::class);

        $router->registerMultiple([
            '/ccm/system/gdpr/scan/tables' => [
                '\A3020\Gdpr\Ajax\Scan\Tables::view',
            ],
            '/ccm/system/gdpr/scan/blocks' => [
                '\A3020\Gdpr\Ajax\Scan\Blocks::view',
            ],
            '/ccm/system/gdpr/scan/packages' => [
                '\A3020\Gdpr\Ajax\Scan\Packages::view',
            ],
            '/ccm/system/gdpr/scan/table/{tableName}' => [
                '\A3020\Gdpr\Ajax\Scan\Tables::peek',
            ],
            '/ccm/system/gdpr/scan/block/{blockType}/{pageId}' => [
                '\A3020\Gdpr\Ajax\Scan\Blocks::status',
            ],
            '/ccm/system/gdpr/scan/block/save' => [
                '\A3020\Gdpr\Ajax\Scan\Blocks::saveStatus',
            ],
        ]);
    }

    private function registerListeners()
    {
        $this->app['director']->addListener('on_user_delete', function($event) {
            /** @var DeleteLogs $listener */
            $listener = $this->app->make(\A3020\Gdpr\Listener\OnUserDelete\DeleteLogs::class);
            $listener->handle($event);
        });

        // Disable the tracking code if needed
        if ($this->config->get('gdpr.settings.tracking.disabled', false)) {
            $this->app['director']->addListener('on_page_output', function($event) {
                /** @var DisableTracking $listener */
                $listener = $this->app->make(\A3020\Gdpr\Listener\OnPageOutput\DisableTracking::class);
                $listener->handle($event);
            });
        }
    }
}
