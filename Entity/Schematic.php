<?php

namespace Tisseo\EndivBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

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
    private $filePath;

    /**
     * @var \Tisseo\EndivBundle\Entity\Line
     */
    private $line;


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
     * @param string $name
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
     * @param string $comment
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
     * @param \DateTime $date
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
     * Set filePath
     *
     * @param string $filePath
     * @return Schematic
     */
    public function setFilePath($filePath)
    {
        $this->filePath = $filePath;

        return $this;
    }

    /**
     * Get filePath
     *
     * @return string 
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * Set line
     *
     * @param \Tisseo\EndivBundle\Entity\Line $line
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
     * @var \Doctrine\Common\Collections\Collection
     */
    private $LineVersion;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->LineVersion = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add LineVersion
     *
     * @param \Tisseo\EndivBundle\Entity\LineVersion $lineVersion
     * @return Schematic
     */
    public function addLineVersion(\Tisseo\EndivBundle\Entity\LineVersion $lineVersion)
    {
        $this->LineVersion[] = $lineVersion;

        return $this;
    }

    /**
     * Remove LineVersion
     *
     * @param \Tisseo\EndivBundle\Entity\LineVersion $lineVersion
     */
    public function removeLineVersion(\Tisseo\EndivBundle\Entity\LineVersion $lineVersion)
    {
        $this->LineVersion->removeElement($lineVersion);
    }

    /**
     * Get LineVersion
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getLineVersion()
    {
        return $this->LineVersion;
    }
}
