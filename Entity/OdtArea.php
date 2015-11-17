<?php

namespace Tisseo\EndivBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;

/**
 * OdtArea
 */
class OdtArea
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $comment;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $odtStops;

   /**
     * Constructor
     */
    public function __construct()
    {
        $this->odtStops = new ArrayCollection();

    }

    /**
     * Set name
     *
     * @param string $name
     * @return OdtArea
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
     * @return OdtArea
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
     * Set id
     *
     * @param integer $id
     * @return OdtArea
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id
     *
     * @return \Tisseo\EndivBundle\Entity\Waypoint
     */
    public function getId()
    {
        return $this->id;
    }
    /**
     * @var \Tisseo\EndivBundle\Entity\Waypoint
     */
    private $waypoint;


    /**
     * Set waypoint
     *
     * @param \Tisseo\EndivBundle\Entity\Waypoint $waypoint
     * @return OdtArea
     */
    public function setWaypoint(\Tisseo\EndivBundle\Entity\Waypoint $waypoint = null)
    {
        $this->waypoint = $waypoint;

        return $this;
    }

    /**
     * Get waypoint
     *
     * @return \Tisseo\EndivBundle\Entity\Waypoint
     */
    public function getWaypoint()
    {
        return $this->waypoint;
    }

    /**
     * Get getOdtStops
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOdtStops()
    {
        return $this->odtStops;
    }

    public function getOpenedOdtStops()
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->orX(
                    Criteria::expr()->isNull('endDate'),
                    Criteria::expr()->gte('endDate', new \DateTime())
                ));
        return $this->odtStops->matching($criteria);
    }

    /**
     * Set odtStops
     *
     * @param \Doctrine\Common\Collections\Collection $odtStops
     * @return OdtArea
     */
    public function setOdtStops(Collection $odtStops)
    {
        $this->odtStops = $odtStops;
        foreach ($this->odtStops as $odtStop) {
            $odtStop->setOdtArea($this);
        }
        return $this;
    }

    /**
     * Add odtStop
     *
     * @param OdtStop $odtStop
     * @return OdtArea
     */
    public function addOdtStop(odtStop $odtStop)
    {
        $this->odtStops[] = $odtStop;
        $odtStop->setOdtArea($this);
        return $this;
    }

    /**
     * Remove odtStop
     *
     * @param OdtStop $odtStop
     */
    public function removeOdtStop(odtStop $odtStop)
    {
        $this->odtStops->removeElement($odtStop);
    }

}
