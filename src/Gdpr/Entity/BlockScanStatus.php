<?php

namespace A3020\Gdpr\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *   name="GdprBlockScanStatus",
 * )
 */
class BlockScanStatus
{
    const NOT_FIXED = 'not_fixed';
    const FIXED = 'fixed';

    /**
     * @ORM\Id @ORM\Column(type="integer", options={"unsigned": true})
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    protected $blockTypeHandle;

    /**
     * @ORM\Column(type="integer", nullable=false, options={"unsigned": true})
     */
    protected $pageId;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $isFixed = false;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $isFixedOnAllPages = false;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $comments;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $updatedAt;

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
     * @return string
     */
    public function getBlockTypeHandle()
    {
        return $this->blockTypeHandle;
    }

    /**
     * @param string $blockTypeHandle
     */
    public function setBlockTypeHandle($blockTypeHandle)
    {
        $this->blockTypeHandle = $blockTypeHandle;
    }

    /**
     * @return int
     */
    public function getPageId()
    {
        return $this->pageId;
    }

    /**
     * @param int $pageId
     */
    public function setPageId($pageId)
    {
        $this->pageId = $pageId;
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

    public function setFixedOnAllPages($fixed)
    {
        $this->isFixedOnAllPages = (bool) $fixed;
    }

    /**
     * @return bool
     */
    public function isFixedOnAllPages()
    {
        return $this->isFixedOnAllPages;
    }

    public function markAsFixedOnAllPages()
    {
        $this->isFixedOnAllPages = true;
    }

    public function markAsUnfixedOnAllPages()
    {
        $this->isFixedOnAllPages = false;
    }

    /**
     * @return string|null
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @param string|null $comments
     */
    public function setComments($comments)
    {
        $comments = trim($comments);
        $this->comments = $comments ? $comments : null;
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
}
