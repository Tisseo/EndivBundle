<?php

namespace Tisseo\EndivBundle\Entity;


/**
 * LineGroup
 */
class LineGroup
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
     * @var \Doctrine\Common\Collections\Collection
     */
    private $lineGroupContents;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->lineGroupContents = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return LineGroup
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
     * Add lineGroupContents
     *
     * @param  \Tisseo\EndivBundle\Entity\LineGroupContent $lineGroupContents
     * @return LineGroup
     */
    public function addLineGroupContent(\Tisseo\EndivBundle\Entity\LineGroupContent $lineGroupContents)
    {
        $this->lineGroupContents[] = $lineGroupContents;

        return $this;
    }

    /**
     * Remove lineGroupContents
     *
     * @param \Tisseo\EndivBundle\Entity\LineGroupContent $lineGroupContents
     */
    public function removeLineGroupContent(\Tisseo\EndivBundle\Entity\LineGroupContent $lineGroupContents)
    {
        $this->lineGroupContents->removeElement($lineGroupContents);
    }

    /**
     * Get lineGroupContents
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLineGroupContents()
    {
        return $this->lineGroupContents;
    }
}
