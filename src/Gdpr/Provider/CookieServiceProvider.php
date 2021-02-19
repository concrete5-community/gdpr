<?php

namespace A3020\Gdpr\Provider;

use A3020\Gdpr\Cookie\Consent;
use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Asset\AssetList;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Cookie\CookieJar;
use Concrete\Core\Http\Request;
use Concrete\Core\Page\Page;
use Concrete\Core\Routing\RouterInterface;

class CookieServiceProvider implements ApplicationAwareInterface
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
        $this->loadCookieConsent();
    }

    private function registerRoutes()
    {
        /** @var RouterInterface $router */
        $router = $this->app->make(RouterInterface::class);

        $router->registerMultiple([
            '/ccm/system/gdpr/consent' => [
                '\A3020\Gdpr\Ajax\Consent::store',
            ],
        ]);
    }

    private function loadCookieConsent()
    {
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

            $al = AssetList::getInstance();

            $al->register('javascript', 'gdpr/gdpr-cookie', 'js/gdpr-cookie.js', [], 'gdpr');
            $al->register('javascript', 'gdpr/cookieconsent', 'js/cookieconsent.min.js', [], 'gdpr');
            $al->register('css', 'gdpr/cookieconsent', 'css/cookieconsent.min.css', [], 'gdpr');
        }
    }

    /**
     * @return bool
     */
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

    /**
     * @return bool
     */
    private function shouldLoadCookieConsent()
    {
        if (!$this->config->get('gdpr.cookies.consent.enabled', false)) {
            return false;
        }

        /** @var Request $request */
        $request = $this->app->make(Request::class);

        // Disable in admin area
        if (stripos($request->getRequestUri(), '/dashboard') !== false) {
            return false;
        }

        // Disable for AJAX requests
        if ($request->isXmlHttpRequest()) {
            return false;
        }

        if ($this->isWhitelisted($request)) {
            return false;
        }

        // If full page caching is enabled, we always want to load the assets
        // otherwise .js files would be missing in some cases
        if ($this->config->get('concrete.cache.pages')) {
            return true;
        }

        /** @var CookieJar $jar */
        $jar = $this->app->make('cookie');

        // The user has decided, so we no longer need the cookie bar
        if ($jar->has('cookieconsent_status')) {
            return false;
        }

        return true;
    }

    /**
     * Whitelisted pages don't have the cookie consent bar
     *
     * E.g. on the /login page
     *
     * @param Request $request
     *
     * @return bool
     */
    private function isWhitelisted(Request $request)
    {
        $disabledPages = $this->config->get('gdpr::cookie_consent.disabled_pages', []);
        if (empty($disabledPages)) {
            return false;
        }

        $requestUri = str_replace('/index.php', '', $request->getRequestUri());
        $requestUri = rtrim($requestUri, '/');

        return in_array($requestUri, $disabledPages);
    }
}
