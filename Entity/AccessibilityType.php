<?php

namespace Tisseo\EndivBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * AccessibilityType
 */
class AccessibilityType
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $startTime;

    /**
     * @var int
     */
    private $endTime;

    /**
     * @var bool
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
     * @var \Doctrine\Common\Collections\Collection
     */
    private $stopAccessibilities;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->stopAccessibilities = new ArrayCollection();
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
     * Set startTime
     *
     * @param int $startTime
     *
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
     * @return int
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * Set endTime
     *
     * @param int $endTime
     *
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
     * @return int
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * Set isRecursive
     *
     * @param bool $isRecursive
     *
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
     * @return bool
     */
    public function getIsRecursive()
    {
        return $this->isRecursive;
    }

    /**
     * Set accessibilityMode
     *
     * @param AccessibilityMode $accessibilityMode
     *
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
     *
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
     *
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
     *
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
