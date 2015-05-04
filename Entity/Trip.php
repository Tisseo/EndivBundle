<?php

namespace Tisseo\EndivBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Trip
 */
class Trip
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
     * @var boolean
     */
    private $isPattern;

    /**
     * @var Comment
     */
    private $comment;

    /**
     * @var Route
     */
    private $route;

    /**
     * @var TripCalendar
     */
    private $tripCalendar;

    /**
     * @var Calendar
     */
    private $periodCalendar;

    /**
     * @var Calendar
     */
    private $dayCalendar;

    /**
     * @var Collection
     */
    private $tripDatasources;

    /**
     * @var Collection
     */
    private $stopTimes;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->tripDatasources = new ArrayCollection();
        $this->stopTimes = new ArrayCollection();
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
     * @return Trip
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
     * Set isPattern
     *
     * @param boolean $isPattern
     * @return Trip
     */
    public function setIsPattern($isPattern)
    {
        $this->isPattern = $isPattern;

        return $this;
    }

    /**
     * Get isPattern
     *
     * @return boolean 
     */
    public function getIsPattern()
    {
        return $this->isPattern;
    }

    /**
     * Set comment
     *
     * @param Comment $comment
     * @return Trip
     */
    public function setComment(Comment $comment = null)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return Comment 
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set route
     *
     * @param Route $route
     * @return Trip
     */
    public function setRoute(Route $route = null)
    {
        $this->route = $route;

        return $this;
    }

    /**
     * Get route
     *
     * @return Route 
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * Set tripCalendar
     *
     * @param TripCalendar $tripCalendar
     * @return Trip
     */
    public function setTripCalendar(TripCalendar $tripCalendar = null)
    {
        $this->tripCalendar = $tripCalendar;

        return $this;
    }

    /**
     * Get tripCalendar
     *
     * @return TripCalendar 
     */
    public function getTripCalendar()
    {
        return $this->tripCalendar;
    }

    /**
     * Set periodCalendar
     *
     * @param Calendar $periodCalendar
     * @return Trip
     */
    public function setPeriodCalendar(Calendar $periodCalendar = null)
    {
        $this->periodCalendar = $periodCalendar;

        return $this;
    }

    /**
     * Get periodCalendar
     *
     * @return Calendar 
     */
    public function getPeriodCalendar()
    {
        return $this->periodCalendar;
    }

    /**
     * Set dayCalendar
     *
     * @param Calendar $dayCalendar
     * @return Trip
     */
    public function setDayCalendar(Calendar $dayCalendar = null)
    {
        $this->dayCalendar = $dayCalendar;

        return $this;
    }

    /**
     * Get dayCalendar
     *
     * @return Calendar 
     */
    public function getDayCalendar()
    {
        return $this->dayCalendar;
    }

    /**
     * Set stopTimes
     *
     * @param Collection $stopTimes
     * @return Trip
     */
    public function setStopTimes(Collection $stopTimes)
    {
        $this->stopTimes = $stopTimes;
        foreach ($this->stopTimes as $stopTime) {
            $stopTime->setTrip($this);
        }
        return $this;
    }

    /**
     * Get stopTimes
     *
     * @return Collection 
     */
    public function getStopTimes()
    {
        return $this->stopTimes;
    }

    /**
     * Add stopTime
     *
     * @param StopTime $stopTime
     * @return Trip
     */
    public function addStopTime(StopTime $stopTime)
    {
        $this->stopTimes[] = $stopTimes;

        return $this;
    }

    /**
     * Remove stopTime
     *
     * @param StopTime $stopTime
     */
    public function removeStopTime(StopTime $stopTime)
    {
        $this->stopTimes->removeElement($stopTime);
    }

    /** Clear stopTimes
     *
     * @return Trip
     */
    public function clearStopTimes()
    {
        $this->stopTimes->clear();
    }

    /**
     * Set tripDatasources
     *
     * @param Collection $tripDatasources
     * @return Trip
     */
    public function setTripDatasources(Collection $tripDatasources)
    {
        $this->tripDatasources = $tripDatasources;
        foreach ($this->tripDatasources as $tripDatasource) {
            $tripDatasource->setTrip($this);
        }
        return $this;
    }

    /**
     * Get tripDatasources
     *
     * @return Collection 
     */
    public function getTripDatasources()
    {
        return $this->tripDatasources;
    }

    /**
     * Add tripDatasources
     *
     * @param TripDatasource $tripDatasource
     * @return Trip
     */
    public function addTripDatasources(TripDatasource $tripDatasource)
    {
        $this->tripDatasources[] = $tripDatasources;

        return $this;
    }

    /**
     * Remove tripDatasources
     *
     * @param TripDatasource $tripDatasource
     */
    public function removeTripDatasources(TripDatasource $tripDatasource)
    {
        $this->tripDatasources->removeElement($tripDatasource);
    }

    /**
     * Add tripDatasources
     *
     * @param \Tisseo\EndivBundle\Entity\TripDatasource $tripDatasources
     * @return Trip
     */
    public function addTripDatasource(\Tisseo\EndivBundle\Entity\TripDatasource $tripDatasources)
    {
        $this->tripDatasources[] = $tripDatasources;

        return $this;
    }

    /**
     * Remove tripDatasources
     *
     * @param \Tisseo\EndivBundle\Entity\TripDatasource $tripDatasources
     */
    public function removeTripDatasource(\Tisseo\EndivBundle\Entity\TripDatasource $tripDatasources)
    {
        $this->tripDatasources->removeElement($tripDatasources);
    }
}
