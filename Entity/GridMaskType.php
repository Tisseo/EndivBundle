<?php

namespace Tisseo\EndivBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * GridMaskType
 */
class GridMaskType
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $calendarType;

    /**
     * @var string
     */
    private $calendarPeriod;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $gridLinkCalendarMaskTypes;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $tripCalendars;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->gridLinkCalendarMaskTypes = new ArrayCollection();
        $this->tripCalendars = new ArrayCollection();
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
     * Set calendarType
     *
     * @param string $calendarType
     * @return GridMaskType
     */
    public function setCalendarType($calendarType)
    {
        $this->calendarType = $calendarType;

        return $this;
    }

    /**
     * Get calendarType
     *
     * @return string 
     */
    public function getCalendarType()
    {
        return $this->calendarType;
    }

    /**
     * Set calendarPeriod
     *
     * @param string $calendarPeriod
     * @return GridMaskType
     */
    public function setCalendarPeriod($calendarPeriod)
    {
        $this->calendarPeriod = $calendarPeriod;

        return $this;
    }

    /**
     * Get calendarPeriod
     *
     * @return string 
     */
    public function getCalendarPeriod()
    {
        return $this->calendarPeriod;
    }

    /**
     * Add gridLinkCalendarMaskTypes
     *
     * @param \Tisseo\EndivBundle\Entity\GridLinkCalendarMaskType $gridLinkCalendarMaskTypes
     * @return GridMaskType
     */
    public function addGridLinkCalendarMaskType(GridLinkCalendarMaskType $gridLinkCalendarMaskTypes)
    {
        $this->gridLinkCalendarMaskTypes[] = $gridLinkCalendarMaskTypes;

        return $this;
    }

    /**
     * Remove gridLinkCalendarMaskTypes
     *
     * @param \Tisseo\EndivBundle\Entity\GridLinkCalendarMaskType $gridLinkCalendarMaskTypes
     */
    public function removeGridLinkCalendarMaskType(GridLinkCalendarMaskType $gridLinkCalendarMaskTypes)
    {
        $this->gridLinkCalendarMaskTypes->removeElement($gridLinkCalendarMaskTypes);
    }

    /**
     * Get gridLinkCalendarMaskTypes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getGridLinkCalendarMaskTypes()
    {
        return $this->gridLinkCalendarMaskTypes;
    }

    /**
     * Add tripCalendars
     *
     * @param \Tisseo\EndivBundle\Entity\TripCalendar $tripCalendars
     * @return GridMaskType
     */
    public function addTripCalendar(TripCalendar $tripCalendars)
    {
        $this->tripCalendars[] = $tripCalendars;

        return $this;
    }

    /**
     * Remove tripCalendars
     *
     * @param \Tisseo\EndivBundle\Entity\TripCalendar $tripCalendars
     */
    public function removeTripCalendar(TripCalendar $tripCalendars)
    {
        $this->tripCalendars->removeElement($tripCalendars);
    }

    /**
     * Get tripCalendars
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTripCalendars()
    {
        return $this->tripCalendars;
    }
}
