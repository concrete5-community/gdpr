<?php

namespace A3020\Gdpr\DataTransfer\Command;

use Concrete\Core\Foundation\Command\Command;

class Request extends Command
{
    protected $requestID;

    /**
     * @param $requestID
     */
    public function __construct($requestID)
    {
        $this->requestID = $requestID;
    }

    /**
     * @return mixed
     */
    public function getRequestID()
    {
        return $this->requestID;
    }
}