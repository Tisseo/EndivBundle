<?php

namespace Tisseo\EndivBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Type;

/**
 * CalendarElement
 */
class CalendarElement
{
    const CALENDAR_OPERATOR_UNION = '+';
    const CALENDAR_OPERATOR_EXCLUSION = '-';
    const CALENDAR_OPERATOR_INTERSECTION = '&';

    /**
     * Property operators
     */
    public static $operators = array(
        self::CALENDAR_OPERATOR_UNION => self::CALENDAR_OPERATOR_UNION,
        self::CALENDAR_OPERATOR_INTERSECTION => self::CALENDAR_OPERATOR_INTERSECTION,
        self::CALENDAR_OPERATOR_EXCLUSION => self::CALENDAR_OPERATOR_EXCLUSION
    );

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
     * @var integer
     */
    private $rank;

    /**
     * @var string
     * @Type("string")
     */
    private $operator;

    /**
     * @var integer
     */
    private $interval;

    /**
     * @var \Tisseo\EndivBundle\Entity\Calendar
     */
    private $includedCalendar;

    /**
     * @var \Tisseo\EndivBundle\Entity\Calendar
     */
    private $calendar;

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
     * Set rank
     *
     * @param string $rank
     * @return CalendarElement
     */
    public function setRank($rank)
    {
        $this->rank = $rank;

        return $this;
    }

    /**
     * Get rank
     *
     * @return string
     */
    public function getRank()
    {
        return $this->rank;
    }

    /**
     * Set operator
     *
     * @param string $operator
     * @return CalendarElement
     */
    public function setOperator($operator)
    {
        if (in_array($operator, self::$operators))
            $this->operator = $operator;

        return $this;
    }

    /**
     * Get operator
     *
     * @return string
     */
    public function getOperator()
    {
        return $this->operator;
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
     * Set includedCalendar
     *
     * @param \Tisseo\EndivBundle\Entity\Calendar $includedCalendar
     * @return CalendarElement
     */
    public function setIncludedCalendar(Calendar $includedCalendar = null)
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

    /**
     * Set calendar
     *
     * @param \Tisseo\EndivBundle\Entity\Calendar $calendar
     * @return CalendarElement
     */
    public function setCalendar(Calendar $calendar = null)
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
}
