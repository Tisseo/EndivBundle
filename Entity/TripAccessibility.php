<?php

namespace Tisseo\EndivBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TripAccessibility
 */
class TripAccessibility
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var AccessibilityType
     */
    private $accessibilityType;

    /**
     * @var Trip
     */
    private $trip;


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
     * Set accessibilityType
     *
     * @param AccessibilityType $accessibilityType
     * @return TripAccessibility
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
     * Set trip
     *
     * @param Trip $trip
     * @return TripAccessibility
     */
    public function setTrip(Trip $trip = null)
    {
        $this->trip = $trip;

        return $this;
    }

    /**
     * Get trip
     *
     * @return Trip
     */
    public function getTrip()
    {
        return $this->trip;
    }
}
