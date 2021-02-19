<?php

namespace A3020\Gdpr\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *   name="GdprDataTransferRequests",
 * )
 */
class DataTransferRequest
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
     * @ORM\Column(type="boolean", nullable=false)
     */
    protected $includeFiles = false;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $requestedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $approvedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $mailedAt;

    public function __construct()
    {
        $this->requestedAt = new DateTime();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return \Concrete\Core\Entity\User\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param \Concrete\Core\Entity\User\User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getIncludeFiles()
    {
        return $this->includeFiles;
    }

    /**
     * @param mixed $includeFiles
     */
    public function setIncludeFiles($includeFiles)
    {
        $this->includeFiles = $includeFiles;
    }

    /**
     * @return DateTime
     */
    public function getRequestedAt()
    {
        return $this->requestedAt;
    }

    /**
     * @param mixed $requestedAt
     */
    public function setRequestedAt($requestedAt)
    {
        $this->requestedAt = $requestedAt;
    }

    /**
     * @return DateTime
     */
    public function getApprovedAt()
    {
        return $this->approvedAt;
    }

    /**
     * @param DateTime $approvedAt
     */
    public function setApprovedAt($approvedAt)
    {
        $this->approvedAt = $approvedAt;
    }

    /**
     * @return DateTime|null
     */
    public function getMailedAt()
    {
        return $this->mailedAt;
    }

    /**
     * @param DateTime $mailedAt
     */
    public function setMailedAt($mailedAt)
    {
        $this->mailedAt = $mailedAt;
    }

    public function markAsMailed()
    {
        $this->mailedAt = new DateTime();
    }
}
