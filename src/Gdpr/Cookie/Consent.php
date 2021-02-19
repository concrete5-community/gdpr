<?php

namespace A3020\Gdpr\Cookie;

use Concrete\Core\Cookie\CookieJar;
use Symfony\Component\HttpFoundation\Session\Session;

class Consent
{
    /**
     * @var Session
     */
    private $session;

    /**
     * @var CookieJar
     */
    private $cookieJar;

    public function __construct(Session $session, CookieJar $cookieJar)
    {
        $this->session = $session;
        $this->cookieJar = $cookieJar;
    }

    /**
     * @return bool
     */
    public function exists()
    {
        return (bool) $this->session->has('gdpr.cookies');
    }

    /**
     * @return bool
     */
    public function given()
    {
        return $this->cookieJar->get('cookieconsent_status') === 'allow'
            || (bool) $this->session->get('gdpr.cookies', false);
    }
}
