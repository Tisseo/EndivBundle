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
    const CALENDAR_TYPE_HYBRID = 'mixte';
    const CALENDAR_TYPE_PERIOD = 'periode';
    const CALENDAR_TYPE_DAY = 'jour';
    const CALENDAR_TYPE_ACCESSIBILITY = 'accessibilite';
    const CALENDAR_TYPE_BRICK = 'brique';

    /**
     * Property calendarTypes
     */
    public static $calendarTypes = array(
        self::CALENDAR_TYPE_DAY => self::CALENDAR_TYPE_DAY,
        self::CALENDAR_TYPE_PERIOD => self::CALENDAR_TYPE_PERIOD,
        self::CALENDAR_TYPE_HYBRID => self::CALENDAR_TYPE_HYBRID,
        self::CALENDAR_TYPE_ACCESSIBILITY => self::CALENDAR_TYPE_ACCESSIBILITY,
        self::CALENDAR_TYPE_BRICK => self::CALENDAR_TYPE_BRICK
    );

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
    private $calendarDatasources;

    /**
     * @var Collection
     */
    private $accessibilityTypes;

    /**
     * @var LineVersion
     */
    private $lineVersion;

    /**
     * @var computed_start_date
     */
    private $computedStartDate;

    /**
     * @var computed_end_date
     */
    private $computedEndDate;

    /**
     * @var Collection
     */
    private $calendarElements;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->calendarDatasources = new ArrayCollection();
        $this->periodTrips = new ArrayCollection();
        $this->dayTrips = new ArrayCollection();
        $this->accessibilityTypes = new ArrayCollection();
        $this->calendarElements = new ArrayCollection();
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
        if (in_array($calendarType, self::$calendarTypes))
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
     * Add calendarDatasources
     *
     * @param CalendarDatasource $calendarDatasources
     * @return Calendar
     */
    public function addAccessibilityType(AccessibilityType $accessibilityType)
    {
        $this->accessibilityTypes[] = $accessibilityType;

        return $this;
    }

    /**
     * Remove calendarDatasources
     *
     * @param CalendarDatasource $calendarDatasources
     */
    public function removeAccessibilityType(AccessibilityType $accessibilityType)
    {
        $this->accessibilityTypes->removeElement($accessibilityType);
    }

    /**
     * Get calendarDatasources
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAccessibilityType()
    {
        return $this->accessibilityTypes;
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

   /**
     * Set computedStartDate
     *
     * @param string $computedStartDate
     * @return Calendar
     */
    public function setComputedStartDate($computedStartDate)
    {
        $this->computedStartDate = $computedStartDate;

        return $this;
    }

    /**
     * Get computedStartDate
     *
     * @return string
     */
    public function getComputedStartDate()
    {
        return $this->computedStartDate;
    }

   /**
     * Set computedEndDate
     *
     * @param string $computedEndDate
     * @return Calendar
     */
    public function setComputedEndDate($computedEndDate)
    {
        $this->computedEndDate = $computedEndDate;

        return $this;
    }

    /**
     * Get computedEndDate
     *
     * @return string
     */
    public function getComputedEndDate()
    {
        return $this->computedEndDate;
    }

    /**
     * Set calendarElements
     *
     * @param Collection $calendarElements
     * @return Route
     */
    public function setCalendarElements(Collection $calendarElements)
    {
        $this->calendarElements = $calendarElements;
        foreach ($this->calendarElements as $calendarElement) {
            $calendarElement->setRoute($this);
        }
        return $this;
    }

    /**
     * Get calendarElements
     *
     * @return Collection
     */
    public function getCalendarElements()
    {
        return $this->calendarElements;
    }

    /**
     * Add calendarElements
     *
     * @param CalendarElement $calendarElement
     * @return Route
     */
    public function addCalendarElement(CalendarElement $calendarElement)
    {
        $this->calendarElements[] = $calendarElement;
        $calendarElement->setRoute($this);

        return $this;
    }

    /**
     * Remove calendarElements
     *
     * @param CalendarElement $calendarElement
     */
    public function removeCalendarElement(CalendarElement $calendarElement)
    {
        $this->calendarElements->removeElement($calendarElement);
    }

    public function __toString()
    {
        return $this->name;
    }
}
