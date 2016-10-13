<?php

namespace Tisseo\EndivBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Schematic
 */
class Schematic
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $comment;

    /**
     * @var \DateTime
     */
    private $date;

    /**
     * @var string
     */
    public $file;

    /**
     * @var boolean
     */
    private $deprecated;

    /**
     * @var boolean
     */
    private $groupGis;

    /**
     * @var \Tisseo\EndivBundle\Entity\Line
     */
    private $line;

    /**
     * @var Collection
     */
    private $lineVersions;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->lineVersions = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param  string $name
     * @return Schematic
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set comment
     *
     * @param  string $comment
     * @return Schematic
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set date
     *
     * @param  \DateTime $date
     * @return Schematic
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set line
     *
     * @param  \Tisseo\EndivBundle\Entity\Line $line
     * @return Schematic
     */
    public function setLine(\Tisseo\EndivBundle\Entity\Line $line = null)
    {
        $this->line = $line;

        return $this;
    }

    /**
     * Get line
     *
     * @return \Tisseo\EndivBundle\Entity\Line
     */
    public function getLine()
    {
        return $this->line;
    }

    /**
     * Set deprecated
     *
     * @param  boolean $deprecated
     * @return Schematic
     */
    public function setDeprecated($deprecated)
    {
        $this->deprecated = $deprecated;

        return $this;
    }

    /**
     * Is deprecated
     *
     * @return boolean
     */
    public function isDeprecated()
    {
        return $this->deprecated;
    }

    /**
     * Set groupGis
     *
     * @param  boolean $groupGis
     * @return Schematic
     */
    public function setGroupGis($groupGis)
    {
        $this->groupGis = $groupGis;

        return $this;
    }

    /**
     * Is groupGis
     *
     * @return boolean
     */
    public function isGroupGis()
    {
        return $this->groupGis;
    }

    /**
     * Get lineVersions
     *
     * @return Collection
     */
    public function getLineVersions()
    {
        return $this->lineVersions;
    }

    public function getDateString()
    {
        return $this->date->format('d/m/Y');
    }

    /**
     * Get file
     *
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set file
     *
     * @param string $file
     */
    public function setFile($file)
    {
        $this->file = $file;
    }
}
