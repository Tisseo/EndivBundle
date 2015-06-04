<?php

namespace Tisseo\EndivBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * TripCalendar
 */
class TripCalendar
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var boolean
     */
    private $monday;

    /**
     * @var boolean
     */
    private $tuesday;

    /**
     * @var boolean
     */
    private $wednesday;

    /**
     * @var boolean
     */
    private $thursday;

    /**
     * @var boolean
     */
    private $friday;

    /**
     * @var boolean
     */
    private $saturday;

    /**
     * @var boolean
     */
    private $sunday;

    /**
     * @var \Tisseo\EndivBundle\Entity\GridMaskType
     */
    private $gridMaskType;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $trips;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->trips = new ArrayCollection();
    }

    public function getPattern()
    {
        return array($this->monday, $this->tuesday, $this->wednesday, $this->thursday, $this->friday, $this->saturday, $this->sunday);
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
     * Set monday
     *
     * @param boolean $monday
     * @return TripCalendar
     */
    public function setMonday($monday)
    {
        $this->monday = $monday;

        return $this;
    }

    /**
     * Get monday
     *
     * @return boolean 
     */
    public function getMonday()
    {
        return $this->monday;
    }

    /**
     * Set tuesday
     *
     * @param boolean $tuesday
     * @return TripCalendar
     */
    public function setTuesday($tuesday)
    {
        $this->tuesday = $tuesday;

        return $this;
    }

    /**
     * Get tuesday
     *
     * @return boolean 
     */
    public function getTuesday()
    {
        return $this->tuesday;
    }

    /**
     * Set wednesday
     *
     * @param boolean $wednesday
     * @return TripCalendar
     */
    public function setWednesday($wednesday)
    {
        $this->wednesday = $wednesday;

        return $this;
    }

    /**
     * Get wednesday
     *
     * @return boolean 
     */
    public function getWednesday()
    {
        return $this->wednesday;
    }

    /**
     * Set thursday
     *
     * @param boolean $thursday
     * @return TripCalendar
     */
    public function setThursday($thursday)
    {
        $this->thursday = $thursday;

        return $this;
    }

    /**
     * Get thursday
     *
     * @return boolean 
     */
    public function getThursday()
    {
        return $this->thursday;
    }

    /**
     * Set friday
     *
     * @param boolean $friday
     * @return TripCalendar
     */
    public function setFriday($friday)
    {
        $this->friday = $friday;

        return $this;
    }

    /**
     * Get friday
     *
     * @return boolean 
     */
    public function getFriday()
    {
        return $this->friday;
    }

    /**
     * Set saturday
     *
     * @param boolean $saturday
     * @return TripCalendar
     */
    public function setSaturday($saturday)
    {
        $this->saturday = $saturday;

        return $this;
    }

    /**
     * Get saturday
     *
     * @return boolean 
     */
    public function getSaturday()
    {
        return $this->saturday;
    }

    /**
     * Set sunday
     *
     * @param boolean $sunday
     * @return TripCalendar
     */
    public function setSunday($sunday)
    {
        $this->sunday = $sunday;

        return $this;
    }

    /**
     * Get sunday
     *
     * @return boolean 
     */
    public function getSunday()
    {
        return $this->sunday;
    }

    /**
     * Set gridMaskType
     *
     * @param \Tisseo\EndivBundle\Entity\GridMaskType $gridMaskType
     * @return TripCalendar
     */
    public function setGridMaskType(GridMaskType $gridMaskType = null)
    {
        $this->gridMaskType = $gridMaskType;

        return $this;
    }

    /**
     * Get gridMaskType
     *
     * @return \Tisseo\EndivBundle\Entity\GridMaskType 
     */
    public function getGridMaskType()
    {
        return $this->gridMaskType;
    }

    /**
     * Get trips
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTrips()
    {
        return $this->trips;
    }

    /**
     * Set trips
     *
     * @param \Tisseo\EndivBundle\Entity\Trip $trips
     * @return TripCalendar
     */
    public function setTrips(Trip $trips)
    {
        $this->trips = $trips;
        foreach ($this->trips as $trip) {
            $trip->setTripCalendar($this);
        }

        return $this;
    }

    /**
     * Add trip
     *
     * @param \Tisseo\EndivBundle\Entity\Trip $trip
     * @return TripCalendar
     */
    public function addTrip(Trip $trip)
    {
        $this->trips[] = $trip;
        $trip->setTripCalendar($this);

        return $this;
    }

    /**
     * Remove trip
     *
     * @param \Tisseo\EndivBundle\Entity\Trip $trip
     */
    public function removeTrip(Trip $trip)
    {
        $this->trips->removeElement($trip);
    }
}
