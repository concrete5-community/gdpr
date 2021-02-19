<?php

namespace A3020\Gdpr\Provider;

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
            '/gdpr/data_transfer/download/{hash}' => [
                '\A3020\Gdpr\Controller\DataTransfer\Download::download',
            ],
            '/ccm/system/gdpr/scan/tables' => [
                '\A3020\Gdpr\Ajax\Scan\Tables::view',
            ],
            '/ccm/system/gdpr/scan/blocks' => [
                '\A3020\Gdpr\Ajax\Scan\Blocks::view',
            ],
            '/ccm/system/gdpr/scan/block/{blockTypeHandle}/{pageId}' => [
                '\A3020\Gdpr\Ajax\Scan\Blocks::status',
            ],
            '/ccm/system/gdpr/scan/block/save' => [
                '\A3020\Gdpr\Ajax\Scan\Blocks::saveStatus',
            ],
            '/ccm/system/gdpr/scan/packages' => [
                '\A3020\Gdpr\Ajax\Scan\Packages::view',
            ],
            '/ccm/system/gdpr/scan/table/preview/{tableName}' => [
                '\A3020\Gdpr\Ajax\Scan\Tables::peek',
            ],
            '/ccm/system/gdpr/scan/table/status/save' => [
                '\A3020\Gdpr\Ajax\Scan\Tables::saveStatus',
            ],
            '/ccm/system/gdpr/scan/table/status/{tableName}' => [
                '\A3020\Gdpr\Ajax\Scan\Tables::status',
            ],
            '/ccm/system/gdpr/data_transfer/requests' => [
                '\A3020\Gdpr\Ajax\DataTransfer\Requests::view',
            ],
        ]);
    }

    private function registerListeners()
    {
        $this->app['director']->addListener('on_user_delete', function($event) {
            /** @var \A3020\Gdpr\Listener\OnUserDelete\DeleteLogs $listener */
            $listener = $this->app->make(\A3020\Gdpr\Listener\OnUserDelete\DeleteLogs::class);
            $listener->handle($event);

            /** @var \A3020\Gdpr\Listener\OnUserDelete\DeleteDataTransferFiles $listener */
            $listener = $this->app->make(\A3020\Gdpr\Listener\OnUserDelete\DeleteDataTransferFiles::class);
            $listener->handle($event);
        });

        $this->app['director']->addListener('on_gdpr_data_transfer_request', function($event) {
            /** @var \A3020\Gdpr\Listener\OnDataTransferRequest\Store $listener */
            $listener = $this->app->make(\A3020\Gdpr\Listener\OnDataTransferRequest\Store::class);
            $listener->handle($event);
        });

        $this->app['director']->addListener('on_gdpr_process_data_transfer_request', function($event) {
            /** @var \A3020\Gdpr\Listener\OnProcessDataTransferRequest\SubmitData $listener */
            $listener = $this->app->make(\A3020\Gdpr\Listener\OnProcessDataTransferRequest\SubmitData::class);
            $listener->handle($event);
        });
    }
}
