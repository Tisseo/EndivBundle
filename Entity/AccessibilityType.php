<?php

namespace Tisseo\EndivBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AccessibilityType
 */
class AccessibilityType
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $startTime;

    /**
     * @var integer
     */
    private $endTime;

    /**
     * @var boolean
     */
    private $isRecursive;

    /**
     * @var AccessibilityMode
     */
    private $accessibilityMode;

    /**
     * @var Calendar
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
     * Set startTime
     *
     * @param integer $startTime
     * @return AccessibilityType
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;

        return $this;
    }

    /**
     * Get startTime
     *
     * @return integer 
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * Set endTime
     *
     * @param integer $endTime
     * @return AccessibilityType
     */
    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;

        return $this;
    }

    /**
     * Get endTime
     *
     * @return integer 
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * Set isRecursive
     *
     * @param boolean $isRecursive
     * @return AccessibilityType
     */
    public function setIsRecursive($isRecursive)
    {
        $this->isRecursive = $isRecursive;

        return $this;
    }

    /**
     * Get isRecursive
     *
     * @return boolean 
     */
    public function getIsRecursive()
    {
        return $this->isRecursive;
    }

    /**
     * Set accessibilityMode
     *
     * @param AccessibilityMode $accessibilityMode
     * @return AccessibilityType
     */
    public function setAccessibilityMode(AccessibilityMode $accessibilityMode = null)
    {
        $this->accessibilityMode = $accessibilityMode;

        return $this;
    }

    /**
     * Get accessibilityMode
     *
     * @return AccessibilityMode 
     */
    public function getAccessibilityMode()
    {
        return $this->accessibilityMode;
    }

    /**
     * Set calendar
     *
     * @param Calendar $calendar
     * @return AccessibilityType
     */
    public function setCalendar(Calendar $calendar = null)
    {
        $this->calendar = $calendar;

        return $this;
    }

    /**
     * Get calendar
     *
     * @return Calendar 
     */
    public function getCalendar()
    {
        return $this->calendar;
    }
}
