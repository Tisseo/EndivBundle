<?php

namespace Tisseo\EndivBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Modification
 */
class Modification
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $description;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $modificationLinks;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->modificationLinks = new ArrayCollection();
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
     * Set description
     *
     * @param string $description
     * @return Modification
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Add modificationLinks
     *
     * @param \Tisseo\EndivBundle\Entity\ModificationLink $modificationLinks
     * @return Modification
     */
    public function addModificationLink(\Tisseo\EndivBundle\Entity\ModificationLink $modificationLinks)
    {
        $this->modificationLinks[] = $modificationLinks;

        return $this;
    }

    /**
     * Remove modificationLinks
     *
     * @param \Tisseo\EndivBundle\Entity\ModificationLink $modificationLinks
     */
    public function removeModificationLink(\Tisseo\EndivBundle\Entity\ModificationLink $modificationLinks)
    {
        $this->modificationLinks->removeElement($modificationLinks);
    }

    /**
     * Get modificationLinks
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getModificationLinks()
    {
        return $this->modificationLinks;
    }
}
