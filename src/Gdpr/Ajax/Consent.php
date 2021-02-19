<?php

namespace A3020\Gdpr\Ajax;

use A3020\Gdpr\Controller\AjaxController;
use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Cookie\CookieJar;
use Concrete\Core\Http\ResponseFactory;

class Consent extends \Concrete\Core\Controller\Controller implements ApplicationAwareInterface
{
    use ApplicationAwareTrait;

    public function store()
    {
        if ($this->post('consent') === 'allow') {
            $this->allow();
        } else {
            $this->deny();
        }

        return $this->app->make(ResponseFactory::class)->json([
            'success' => true,
            'consent' => $this->post('consent') === 'allow' ? 'allow' : 'deny',
        ]);
    }

    private function allow()
    {
        $session = $this->app->make('session');
        $session->set('gdpr.cookies', true);
    }

    private function deny()
    {
        $session = $this->app->make('session');
        $session->set('gdpr.cookies', false);

        $this->deleteAllCookies();
    }

    private function deleteAllCookies()
    {
        if (!isset($_SERVER['HTTP_COOKIE'])) {
            return;
        }

        /** @var CookieJar $jar */
        $jar = $this->app->make('cookie');

        $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
        foreach ($cookies as $cookie) {
            $parts = explode('=', $cookie);
            $name = trim($parts[0]);

            if ($name === 'CONCRETE5' || $name === 'CONCRETE5_LOGIN' || $name === 'cookieconsent_status') {
                continue;
            }

            $jar->set($name, null, time() - 1000, '');
            $jar->set($name, null, time() - 1000, '/');
        }
    }
}
