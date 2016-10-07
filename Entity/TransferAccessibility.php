<?php

namespace Tisseo\EndivBundle\Entity;

/**
 * TransferAccessibility
 */
class TransferAccessibility
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
     * @var Transfer
     */
    private $transfer;


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
     * @return TransferAccessibility
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
     * Set transfer
     *
     * @param  Transfer $transfer
     * @return TransferAccessibility
     */
    public function setTransfer(Transfer $transfer = null)
    {
        $this->transfer = $transfer;

        return $this;
    }

    /**
     * Get transfer
     *
     * @return Transfer
     */
    public function getTransfer()
    {
        return $this->transfer;
    }
}
