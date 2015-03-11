<?php

namespace Tisseo\EndivBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CalendarElement
 */
class CalendarElement
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $startDate;

    /**
     * @var \DateTime
     */
    private $endDate;

    /**
     * @var string
     */
    private $positive;

    /**
     * @var integer
     */
    private $interval;

    /**
     * @var \Tisseo\EndivBundle\Entity\Calendar
     */
    private $calendar;

    /**
     * @var \Tisseo\EndivBundle\Entity\Calendar
     */
    private $includedCalendar;


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
     * Set startDate
     *
     * @param \DateTime $startDate
     * @return CalendarElement
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get startDate
     *
     * @return \DateTime 
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set endDate
     *
     * @param \DateTime $endDate
     * @return CalendarElement
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get endDate
     *
     * @return \DateTime 
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Set positive
     *
     * @param string $positive
     * @return CalendarElement
     */
    public function setPositive($positive)
    {
        $this->positive = $positive;

        return $this;
    }

    /**
     * Get positive
     *
     * @return string 
     */
    public function getPositive()
    {
        return $this->positive;
    }

    /**
     * Set interval
     *
     * @param integer $interval
     * @return CalendarElement
     */
    public function setInterval($interval)
    {
        $this->interval = $interval;

        return $this;
    }

    /**
     * Get interval
     *
     * @return integer 
     */
    public function getInterval()
    {
        return $this->interval;
    }

    /**
     * Set calendar
     *
     * @param \Tisseo\EndivBundle\Entity\Calendar $calendar
     * @return CalendarElement
     */
    public function setCalendar(\Tisseo\EndivBundle\Entity\Calendar $calendar = null)
    {
        $this->calendar = $calendar;

        return $this;
    }

    /**
     * Get calendar
     *
     * @return \Tisseo\EndivBundle\Entity\Calendar 
     */
    public function getCalendar()
    {
        return $this->calendar;
    }

    /**
     * Set includedCalendar
     *
     * @param \Tisseo\EndivBundle\Entity\Calendar $includedCalendar
     * @return CalendarElement
     */
    public function setIncludedCalendar(\Tisseo\EndivBundle\Entity\Calendar $includedCalendar = null)
    {
        $this->includedCalendar = $includedCalendar;

        return $this;
    }

    /**
     * Get includedCalendar
     *
     * @return \Tisseo\EndivBundle\Entity\Calendar 
     */
    public function getIncludedCalendar()
    {
        return $this->includedCalendar;
    }
}
