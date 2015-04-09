<?php

namespace Tisseo\EndivBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Stop
 */
class Stop
{
    /**
     * @var \Tisseo\EndivBundle\Entity\Waypoint
     */
    private $id;

    /**
     * @var \Tisseo\EndivBundle\Entity\Stop
     */
    private $masterStop;

    /**
     * @var \Tisseo\EndivBundle\Entity\StopArea
     */
    private $stopArea;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $stopDatasources;
	
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $stopHistories;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $stopAccessibilities;	
	
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->stopDatasources = new ArrayCollection();
        $this->stopHistories = new ArrayCollection();
        $this->stopAccessibilities = new ArrayCollection();
    }
	
	
    /**
     * Set id
     *
     * @param \Tisseo\EndivBundle\Entity\Waypoint $id
     * @return Stop
     */
    public function setId(\Tisseo\EndivBundle\Entity\Waypoint $id = null)
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
     * Set masterStop
     *
     * @param \Tisseo\EndivBundle\Entity\Stop $masterStop
     * @return Stop
     */
    public function setMasterStop(\Tisseo\EndivBundle\Entity\Stop $masterStop = null)
    {
        $this->masterStop = $masterStop;

        return $this;
    }

    /**
     * Get masterStop
     *
     * @return \Tisseo\EndivBundle\Entity\Stop 
     */
    public function getMasterStop()
    {
        return $this->masterStop;
    }

    /**
     * Set stopArea
     *
     * @param \Tisseo\EndivBundle\Entity\StopArea $stopArea
     * @return Stop
     */
    public function setStopArea(\Tisseo\EndivBundle\Entity\StopArea $stopArea = null)
    {
        $this->stopArea = $stopArea;

        return $this;
    }

    /**
     * Get stopArea
     *
     * @return \Tisseo\EndivBundle\Entity\StopArea 
     */
    public function getStopArea()
    {
        return $this->stopArea;
    }
    /**
     * @var \Tisseo\EndivBundle\Entity\Waypoint
     */
    private $waypoint;


    /**
     * Set waypoint
     *
     * @param \Tisseo\EndivBundle\Entity\Waypoint $waypoint
     * @return Stop
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
     * Get stopDatasources
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getStopDatasources()
    {
        return $this->stopDatasources;
    }

    /**
     * Set stopDatasources
     *
     * @param \Doctrine\Common\Collections\Collection $stopDatasources
     * @return Line
     */
    public function setStopDatasources(Collection $stopDatasources)
    {
        $this->stopDatasources = $stopDatasources;
        foreach ($this->stopDatasources as $stopDatasource) {
            $stopDatasource->setStop($this);
        }
        return $this;
    }

    /**
     * Add stopDatasource
     *
     * @param StopDatasource $stopDatasources
     * @return Line
     */
    public function addStopDatasources(StopDatasource $stopDatasource)
    {
        $this->stopDatasources[] = $stopDatasource;
        $stopDatasource->setStop($this);
        return $this;
    }

    /**
     * Remove stopDatasources
     *
     * @param StopDatasource $stopDatasources
     */
    public function removeStopDatasources(StopDatasource $stopDatasources)
    {
        $this->stopDatasources->removeElement($stopDatasources);
    }	

    /**
     * Get getStopHistories
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getStopHistories()
    {
        return $this->stopHistories;
    }

    /**
     * Set StopHistories
     *
     * @param \Doctrine\Common\Collections\Collection $stopHistories
     * @return Line
     */
    public function setStopHistories(Collection $stopHistories)
    {
        $this->stopHistories = $stopHistories;
        foreach ($this->stopHistories as $stopHistory) {
            $stopHistory->setStop($this);
        }
        return $this;
    }

    /**
     * Add stopHistory
     *
     * @param stopHistory $stopHistory
     * @return Line
     */
    public function addStopHistory(stopHistory $stopHistory)
    {
        $this->stopHistories[] = $stopHistory;
        $stopHistory->setStop($this);
        return $this;
    }

    /**
     * Remove stopHistory
     *
     * @param stopHistory $stopHistory
     */
    public function removeStopHistory(stopHistory $stopHistory)
    {
        $this->stopHistories->removeElement($stopHistory);
    }	
	
    /**
     * Get getStopAccessibilities
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getStopAccessibilities()
    {
        return $this->stopAccessibilities;
    }

    /**
     * Set StopAccessibilities
     *
     * @param \Doctrine\Common\Collections\Collection $stopAccessibilities
     * @return Line
     */
    public function setStopAccessibilities(Collection $stopAccessibilities)
    {
        $this->stopAccessibilities = $stopAccessibilities;
        foreach ($this->stopAccessibilities as $stopAccessibility) {
            $stopAccessibility->setStop($this);
        }
        return $this;
    }

    /**
     * Add stopAccessibility
     *
     * @param stopAccessibility $stopAccessibility
     * @return Line
     */
    public function addStopAccessibility(stopAccessibility $stopAccessibility)
    {
        $this->stopAccessibilities[] = $stopAccessibility;
        $stopAccessibility->setStop($this);
        return $this;
    }

    /**
     * Remove stopAccessibility
     *
     * @param stopAccessibility $stopAccessibility
     */
    public function removeStopAccessibility(stopAccessibility $stopAccessibility)
    {
        $this->stopAccessibilities->removeElement($stopAccessibility);
    }	
}
