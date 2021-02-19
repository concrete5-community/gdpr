<?php

namespace A3020\Gdpr\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *   name="GdprTableScanStatus",
 * )
 */
class TableScanStatus implements \JsonSerializable
{
    /**
     * @ORM\Id @ORM\Column(type="integer", options={"unsigned": true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    protected $tableName;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $isFixed = false;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $notes;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $updatedAt;

    /** @var bool */
    protected $isSystemStatus;

    public function __construct()
    {
        $this->updatedAt = new DateTimeImmutable();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return bool
     */
    public function isFixed()
    {
        return $this->isFixed;
    }

    public function setFixed($fixed)
    {
        $this->isFixed = (bool) $fixed;
    }

    public function markAsFixed()
    {
        $this->isFixed = true;
    }

    public function markAsNotFixed()
    {
        $this->isFixed = false;
    }

    /**
     * @return string|null
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * @param string|null $notes
     */
    public function setNotes($notes)
    {
        $notes = trim($notes);
        $this->notes = $notes ? $notes : null;
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param mixed $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    public function setTableName($tableName)
    {
        $this->tableName = $tableName;
    }

    public function isSystemStatus($value)
    {
        $this->isSystemStatus = (bool) $value;
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
        return [
            'fixed' => $this->isFixed(),
            'info' => e($this->getNotes()),
        ];
    }
}
