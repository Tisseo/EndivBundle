<?php

namespace Tisseo\EndivBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Calendar
 */
class Calendar
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
    private $calendarType;

    /**
     * @var Collection
     */
    private $periodTrips;

    /**
     * @var Collection
     */
    private $dayTrips;

    /**
     * @var Collection
     */
    private $calendarElements;

    /**
     * @var Collection
     */
    private $calendarDatasources;

    /**
     * @var LineVersion
     */
    private $lineVersion;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->calendarElements = new ArrayCollection();
        $this->calendarDatasources = new ArrayCollection();
        $this->periodTrips = new ArrayCollection();
        $this->dayTrips = new ArrayCollection();
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
     * @return Calendar
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
     * Set calendarType
     *
     * @param string $calendarType
     * @return Calendar
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
     * Add calendarElement
     *
     * @param CalendarElement $calendarElement
     * @return Calendar
     */
    public function addCalendarElement(CalendarElement $calendarElement)
    {
        $this->calendarElements[] = $calendarElement;

        return $this;
    }

    /**
     * Remove calendarElement
     *
     * @param CalendarElement $calendarElement
     */
    public function removeCalendarElement(CalendarElement $calendarElement)
    {
        $this->calendarElements->removeElement($calendarElement);
    }

    /**
     * Get calendarElements
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCalendarElements()
    {
        return $this->calendarElements;
    }

    /**
     * Add calendarDatasources
     *
     * @param CalendarDatasource $calendarDatasources
     * @return Calendar
     */
    public function addCalendarDatasource(CalendarDatasource $calendarDatasources)
    {
        $this->calendarDatasources[] = $calendarDatasources;

        return $this;
    }

    /**
     * Remove calendarDatasources
     *
     * @param CalendarDatasource $calendarDatasources
     */
    public function removeCalendarDatasource(CalendarDatasource $calendarDatasources)
    {
        $this->calendarDatasources->removeElement($calendarDatasources);
    }

    /**
     * Get calendarDatasources
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCalendarDatasources()
    {
        return $this->calendarDatasources;
    }

    /**
     * Add periodTrips
     *
     * @param Trip $periodTrips
     * @return Calendar
     */
    public function addPeriodTrip(Trip $periodTrips)
    {
        $this->periodTrips[] = $periodTrips;

        return $this;
    }

    /**
     * Remove periodTrips
     *
     * @param Trip $periodTrips
     */
    public function removePeriodTrip(Trip $periodTrips)
    {
        $this->periodTrips->removeElement($periodTrips);
    }

    /**
     * Get periodTrips
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPeriodTrips()
    {
        return $this->periodTrips;
    }

    /**
     * Add dayTrips
     *
     * @param Trip $dayTrips
     * @return Calendar
     */
    public function addDayTrip(Trip $dayTrips)
    {
        $this->dayTrips[] = $dayTrips;

        return $this;
    }

    /**
     * Remove dayTrips
     *
     * @param Trip $dayTrips
     */
    public function removeDayTrip(Trip $dayTrips)
    {
        $this->dayTrips->removeElement($dayTrips);
    }

    /**
     * Get dayTrips
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDayTrips()
    {
        return $this->dayTrips;
    }

    /**
     * Set lineVersion
     *
     * @param LineVersion $lineVersion
     * @return Route
     */
    public function setLineVersion(LineVersion $lineVersion = null)
    {
        $this->lineVersion = $lineVersion;

        return $this;
    }

    /**
     * Get lineVersion
     *
     * @return LineVersion 
     */
    public function getLineVersion()
    {
        return $this->lineVersion;
    }
}
