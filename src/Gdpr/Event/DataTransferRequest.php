<?php

namespace A3020\Gdpr\Event;

use Concrete\Core\Entity\User\User;
use Symfony\Component\EventDispatcher\GenericEvent;

class DataTransferRequest extends GenericEvent
{
    protected $includeFiles = true;

    /**
     * Encapsulate an event with $user and $args.
     *
     * @param User $user The subject of the event, usually an object
     * @param array $arguments Arguments to store in the event
     */
    public function __construct(User $user, array $arguments = array())
    {
        $this->subject = $user;
        $this->arguments = $arguments;
    }

    /**
     * @return \Concrete\Core\Entity\User\User
     */
    public function getUser()
    {
        return $this->getSubject();
    }

    /**
     * @return bool
     */
    public function shouldIncludeFiles()
    {
        return $this->includeFiles;
    }

    /**
     * Call this method if the user is only interested in the JSON data
     */
    public function skipFiles()
    {
        $this->includeFiles = false;
    }
}
