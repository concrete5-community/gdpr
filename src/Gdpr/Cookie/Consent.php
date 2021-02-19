<?php

namespace A3020\Gdpr\Cookie;

use Symfony\Component\HttpFoundation\Session\Session;

class Consent
{
    /**
     * @var Session
     */
    private $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
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
        return (bool) $this->session->get('gdpr.cookies', false);
    }
}
