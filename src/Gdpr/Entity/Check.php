<?php

namespace A3020\Gdpr\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *   name="GdprChecks",
 * )
 */
class Check
{
    /**
     * @ORM\Id @ORM\Column(type="integer", options={"unsigned": true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $handle;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    protected $name;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $description;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $category;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $checkedAt;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string|null
     */
    public function getHandle()
    {
        return $this->handle;
    }

    /**
     * @param string $handle
     */
    public function setHandle($handle)
    {
        $this->handle = $handle;
    }

    /**
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string|null
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getCheckedAt()
    {
        return $this->checkedAt;
    }

    /**
     * @param mixed $checkedAt
     */
    public function setCheckedAt($checkedAt)
    {
        $this->checkedAt = $checkedAt;
    }

    /**
     * @return bool
     */
    public function isChecked()
    {
        return $this->checkedAt !== null;
    }

    /**
     * @return string|null
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param string $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

    public function markAsChecked()
    {
        $this->checkedAt = new DateTimeImmutable();
    }

    public function markAsUnChecked()
    {
        $this->checkedAt = null;
    }
}
