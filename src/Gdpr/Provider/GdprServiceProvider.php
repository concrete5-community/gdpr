<?php

namespace A3020\Gdpr\Provider;

use A3020\Gdpr\Cookie\Consent;
use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Asset\AssetList;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Cookie\CookieJar;
use Concrete\Core\Http\Request;
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
        $this->registerAssets();
    }

    private function registerRoutes()
    {
        /** @var RouterInterface $router */
        $router = $this->app->make(RouterInterface::class);

        $router->registerMultiple([
            '/ccm/system/gdpr/consent' => [
                '\A3020\Gdpr\Ajax\Consent::store',
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
        ]);
    }

    private function registerListeners()
    {
        $this->app['director']->addListener('on_user_delete', function($event) {
            /** @var \A3020\Gdpr\Listener\OnUserDelete\DeleteLogs $listener */
            $listener = $this->app->make(\A3020\Gdpr\Listener\OnUserDelete\DeleteLogs::class);
            $listener->handle($event);
        });

        // Disable the tracking code if needed
        if ($this->shouldDisableTrackingCode()) {
            $this->app['director']->addListener('on_page_output', function($event) {
                /** @var \A3020\Gdpr\Listener\OnPageOutput\DisableTracking $listener */
                $listener = $this->app->make(\A3020\Gdpr\Listener\OnPageOutput\DisableTracking::class);
                $listener->handle($event);
            });
        }


        // Show / enable cookie consent if needed
        if ($this->shouldLoadCookieConsent()) {
            $this->app['director']->addListener('on_before_render', function ($event) {
                /** @var \A3020\Gdpr\Listener\OnBeforeRender\AddCookieConsent $listener */
                $listener = $this->app->make(\A3020\Gdpr\Listener\OnBeforeRender\AddCookieConsent::class);
                $listener->handle($event);
            });
        }
    }

    private function registerAssets()
    {
        if (!$this->shouldLoadCookieConsent()) {
            return;
        }

        $al = AssetList::getInstance();

        $al->register('javascript', 'gdpr/gdpr-cookie', 'js/gdpr-cookie.js', [], 'gdpr');
        $al->register('javascript', 'gdpr/cookieconsent', 'js/cookieconsent.min.js', [], 'gdpr');
        $al->register('css', 'gdpr/cookieconsent', 'css/cookieconsent.min.css', [], 'gdpr');
    }

    private function shouldDisableTrackingCode()
    {
        if ($this->config->get('gdpr.settings.tracking.disabled', false)) {
            return true;
        }

        if ($this->config->get('gdpr.cookies.consent.enabled', false) === false) {
            return false;
        }

        /** @var Consent $consent */
        $consent = $this->app->make(Consent::class);

        switch ($this->config->get('gdpr.cookies.consent.type', 'notice')) {
            case 'opt-in':
                // If no consent is given, tracking should be disabled
                return !$consent->given();
            case 'opt-out':
                // If the user hasn't decided, we permit tracking
                if (!$consent->exists()) {
                    return false;
                }

                // If no consent is given, tracking is not allowed
                return !$consent->given();
        }

        // notice
        return false;
    }

    private function shouldLoadCookieConsent()
    {
        if (!$this->config->get('gdpr.cookies.consent.enabled', false)) {
            return false;
        }

        /** @var Request $request */
        $request = $this->app->make(Request::class);

        // Disable in admin area
        if (strpos($request->getRequestUri(), '/dashboard') !== false) {
            return false;
        }

        // Disable for AJAX requests
        if ($request->isXmlHttpRequest()) {
            return false;
        }

        /** @var CookieJar $jar */
        $jar = $this->app->make('cookie');
        if ($jar->has('cookieconsent_status')) {
            return false;
        }

        return true;
    }
}
