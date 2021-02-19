<?php

namespace A3020\Gdpr\Listener\OnBeforeRender;

use A3020\Gdpr\Cookie\Configuration;
use Concrete\Core\View\View;

class AddCookieConsent
{
    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function handle($event)
    {
        $view = View::getInstance();

        $view->addFooterItem($this->getCookieJs());
        $view->requireAsset('javascript', 'gdpr/gdpr-cookie');
        $view->requireAsset('javascript', 'gdpr/cookieconsent');
        $view->requireAsset('css', 'gdpr/cookieconsent');
    }

    public function getCookieJs()
    {
        $script = '<script>';
        $script .= 'window.addEventListener("load", function(){';
        $script .= 'window.cookieconsent.initialise('.json_encode($this->configuration->getConfiguration()).')});';

        $script .= '</script>';

        return $script;
    }
}
