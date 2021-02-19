<?php

namespace A3020\Gdpr\Entity;

use Concrete\Core\Entity\User\User;
use DateInterval;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *   name="GdprDataTransferFiles",
 * )
 */
class DataTransferFile
{
    /**
     * @ORM\Id @ORM\Column(type="integer", options={"unsigned": true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Concrete\Core\Entity\User\User")
     * @ORM\JoinColumn(name="uID", referencedColumnName="uID", onDelete="cascade")
     */
    protected $user;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    protected $fileLocation;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    protected $hash;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $validUntil;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $downloadedAt;

    public function __construct()
    {
        $this->createdAt = new DateTime();

        $this->validUntil = (new DateTime())->add(new DateInterval('P10D'));
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getFileLocation()
    {
        return $this->fileLocation;
    }

    /**
     * @param string $fileLocation
     */
    public function setFileLocation($fileLocation)
    {
        $this->fileLocation = $fileLocation;
    }

    /**
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * @param string $hash
     */
    public function setHash($hash)
    {
        $this->hash = $hash;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return DateTime
     */
    public function getValidUntil()
    {
        return $this->validUntil;
    }

    /**
     * @param DateTime $validUntil
     */
    public function setValidUntil($validUntil)
    {
        $this->validUntil = $validUntil;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        $today = new DateTime();

        return $this->validUntil > $today;
    }

    /**
     * @return DateTime
     */
    public function getDownloadedAt()
    {
        return $this->downloadedAt;
    }

    /**
     * @param DateTime $downloadedAt
     */
    public function setDownloadedAt($downloadedAt)
    {
        $this->downloadedAt = $downloadedAt;
    }

    public function markAsDownloaded()
    {
        $this->setDownloadedAt(new DateTime());
    }
}
