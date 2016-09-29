<?php

namespace Tisseo\EndivBundle\Entity;


/**
 * PoiAddressAccessibility
 */
class PoiAddressAccessibility
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
     * @var PoiAddress
     */
    private $poiAddress;


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
     * @param  AccessibilityType $accessibilityType
     * @return PoiAddressAccessibility
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
     * Set poiAddress
     *
     * @param  PoiAddress $poiAddress
     * @return PoiAddressAccessibility
     */
    public function setPoiAddress(PoiAddress $poiAddress = null)
    {
        $this->poiAddress = $poiAddress;

        return $this;
    }

    /**
     * Get poiAddress
     *
     * @return PoiAddress
     */
    public function getPoiAddress()
    {
        return $this->poiAddress;
    }
}
