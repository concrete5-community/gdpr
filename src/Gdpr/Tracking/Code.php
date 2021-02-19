<?php

namespace A3020\Gdpr\Tracking;

use Concrete\Core\Application\Application;
use Concrete\Core\Entity\Site\Site;

class Code
{
    /** @var Application */
    private $app;

    protected $config;

    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->config = $this->getConfig();
    }

    /**
     * Return true if a header or footer tracking code is configured
     *
     * @return bool
     */
    public function has()
    {
        return $this->config->get('seo.tracking.code.header') || $this->config->get('seo.tracking.code.footer');
    }

    public function header()
    {
        return $this->config->get('seo.tracking.code.header');
    }

    public function footer()
    {
        return $this->config->get('seo.tracking.code.footer');
    }

    private function getConfig()
    {
        /** @var Site $site */
        $site = $this->app->make('site')->getSite();

        return $site->getConfigRepository();
    }
}
