<?php

namespace Tisseo\EndivBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * LineGroupGis
 */
class LineGroupGis
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
    private $nbBus;

    /**
     * @var string
     */
    private $comment;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $lineGroupGisContents;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->lineGroupGisContents = new ArrayCollection();
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
     * @param string $name
     * @return LineGroupGis
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
     * Add lineGroupGisContents
     *
     * @param \Tisseo\EndivBundle\Entity\LineGroupGisContent $lineGroupGisContents
     * @return LineGroupGis
     */
    public function addLineGroupGisContent(LineGroupGisContent $lineGroupGisContents)
    {
        $this->setLineGroupGisContents($lineGroupGisContents);
        $this->lineGroupGisContents[] = $lineGroupGisContents;
        return $this;
    }

    /**
     * Remove lineGroupGisContents
     *
     * @param \Tisseo\EndivBundle\Entity\LineGroupGisContent $lineGroupGisContents
     */
    public function removeLineGroupGisContent(LineGroupGisContent $lineGroupGisContents)
    {
        $this->lineGroupGisContents->removeElement($lineGroupGisContents);
    }

    /**
     * Get lineGroupGisContents
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLineGroupGisContents()
    {
        return $this->lineGroupGisContents;
    }

    public function setLineGroupGisContents(LineGroupGisContent $lineGroupGisContent)
    {
        $lineGroupGisContent->setLineGroupGis($this);
    }

    /**
     * Set nbBus
     *
     * @param string $nbBus
     * @return LineGroupGis
     */
    public function setNbBus($nbBus)
    {
        $this->nbBus = $nbBus;

        return $this;
    }

    /**
     * Get nbBus
     *
     * @return string
     */
    public function getNbBus()
    {
        return $this->nbBus;
    }

    /**
     * Set comment
     *
     * @param string $comment
     * @return LineGroupGis
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
}
