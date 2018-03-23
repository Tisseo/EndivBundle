<?php

namespace Tisseo\EndivBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * GridMaskType
 */
class GridMaskType
{
    /**
     * @var int
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
     * @var string
     */
    private $code;

    /**
     * @var string
     */
    private $scenario;

    /**
     * @var string
     */
    private $included;

    /**
     * @var Datetime
     */
    private $startDate;

    /**
     * @var Datetime
     */
    private $endDate;

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
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set calendarType
     *
     * @param string $calendarType
     *
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
     *
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
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return GridMasktype
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get scenario
     *
     * @return string
     */
    public function getScenario()
    {
        return $this->scenario;
    }

    /**
     * Set scenario
     *
     * @param string $scenario
     *
     * @return GridMasktype
     */
    public function setScenario($scenario)
    {
        $this->scenario = $scenario;

        return $this;
    }

    /**
     * Get included
     *
     * @return string
     */
    public function getIncluded()
    {
        return $this->included;
    }

    /**
     * Set included
     *
     * @param string $included
     *
     * @return GridMasktype
     */
    public function setIncluded($included)
    {
        $this->included = $included;

        return $this;
    }

    /**
     * Get startDate
     *
     * @return \Datetime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set startDate
     *
     * @param \Datetime $startDate
     *
     * @return GridMasktype
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get endDate
     *
     * @return \Datetime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Set endDate
     *
     * @param \Datetime $endDate
     *
     * @return GridMasktype
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Add gridLinkCalendarMaskTypes
     *
     * @param \Tisseo\EndivBundle\Entity\GridLinkCalendarMaskType $gridLinkCalendarMaskTypes
     *
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
     *
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
