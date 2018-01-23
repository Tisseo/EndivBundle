<?php

namespace Tisseo\EndivBundle\Entity;

/**
 * StopAccessibility
 */
class StopAccessibility
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var AccessibilityType
     */
    private $accessibilityType;

    /**
     * @var Stop
     */
    private $stop;

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
     * Set accessibilityType
     *
     * @param AccessibilityType $accessibilityType
     *
     * @return StopAccessibility
     */
    public function setAccessibilityType(AccessibilityType $accessibilityType = null)
    {
        $this->accessibilityType = $accessibilityType;

        return $this;
    }

    /**
     * Get accessibilityType
     *
     * @return AccessibilityType
     */
    public function getAccessibilityType()
    {
        return $this->accessibilityType;
    }

    /**
     * Set stop
     *
     * @param Stop $stop
     *
     * @return StopAccessibility
     */
    public function setStop(Stop $stop = null)
    {
        $this->stop = $stop;

        return $this;
    }

    /**
     * Get stop
     *
     * @return Stop
     */
    public function getStop()
    {
        return $this->stop;
    }
}
