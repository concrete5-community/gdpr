<?php

namespace A3020\Gdpr\Event;

use Symfony\Component\EventDispatcher\GenericEvent;

class ProcessDataTransferRequest extends GenericEvent implements \JsonSerializable
{
    protected $data = [];
    protected $files = [];

    /**
     * @return \Concrete\Core\Entity\User\User
     */
    public function getUser()
    {
        return $this->getSubject();
    }

    /**
     * @param mixed $data
     */
    public function addData($data)
    {
        $this->data[] = $data;
    }

    /**
     * @param string $path
     */
    public function addFile($path)
    {
        $this->files[] = [
            'path' => $path,
        ];
    }

    /**
     * @return array
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return $this->data;
    }
}
