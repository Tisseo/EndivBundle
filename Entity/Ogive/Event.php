<?php

namespace Tisseo\EndivBundle\Entity\Ogive;

use Doctrine\ORM\Mapping as ORM;

/**
 * Event
 */
class Event extends OgiveEntity
{
    const STATUS_OPEN = 1;
    
    const STATUS_CLOSED = 2;
    
    const STATUS_REJECTED = 3;
    
    /**
     * @var integer
     */
    private $id;

    /**
     * @var guid
     */
    private $chaosSeverity;

    /**
     * @var string
     */
    private $chaosInternalCause;

    /**
     * @var integer
     */
    private $status;

    /**
     * @var boolean
     */
    private $isPublished;

    /**
     * @var guid
     */
    private $chaosDisruptionId;

    /**
     * @var string
     */
    private $reference;

    /**
     * @var boolean
     */
    private $isEmergency;

    /**
     * @var Event
     */
    private $eventParent;


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
     * Set chaosSeverity
     *
     * @param guid $chaosSeverity
     * @return Event
     */
    public function setChaosSeverity($chaosSeverity)
    {
        $this->chaosSeverity = $chaosSeverity;

        return $this;
    }

    /**
     * Get chaosSeverity
     *
     * @return guid 
     */
    public function getChaosSeverity()
    {
        return $this->chaosSeverity;
    }

    /**
     * Set chaosInternalCause
     *
     * @param string $chaosInternalCause
     * @return Event
     */
    public function setChaosInternalCause($chaosInternalCause)
    {
        $this->chaosInternalCause = $chaosInternalCause;

        return $this;
    }

    /**
     * Get chaosInternalCause
     *
     * @return string 
     */
    public function getChaosInternalCause()
    {
        return $this->chaosInternalCause;
    }

    /**
     * Set chaosDisruptionId
     *
     * @param guid $chaosDisruptionId
     * @return Event
     */
    public function setChaosDisruptionId($chaosDisruptionId)
    {
        $this->chaosDisruptionId = $chaosDisruptionId;

        return $this;
    }

    /**
     * Get chaosDisruptionId
     *
     * @return guid 
     */
    public function getChaosDisruptionId()
    {
        return $this->chaosDisruptionId;
    }

    /**
     * Set reference
     *
     * @param string $reference
     * @return Event
     */
    public function setReference($reference)
    {
        $this->reference = $reference;

        return $this;
    }

    /**
     * Get reference
     *
     * @return string 
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * Set isEmergency
     *
     * @param boolean $isEmergency
     * @return Event
     */
    public function setIsEmergency($isEmergency)
    {
        $this->isEmergency = $isEmergency;

        return $this;
    }

    /**
     * Get isEmergency
     *
     * @return boolean 
     */
    public function getIsEmergency()
    {
        return $this->isEmergency;
    }

    /**
     * Set eventParent
     *
     * @param Event $eventParent
     * @return Event
     */
    public function setEventParent(Event $eventParent = null)
    {
        $this->eventParent = $eventParent;

        return $this;
    }

    /**
     * Get eventParent
     *
     * @return Event 
     */
    public function getEventParent()
    {
        return $this->eventParent;
    }
    
    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }
    
    /**
     * Set status
     *
     * @param integer status
     * @return Event
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }
    
    /**
     * Get isPublished.
     *
     * @return boolean
     */
    public function isPublished()
    {
        return $this->isPublished;
    }
    
    /**
     * Set isPublished.
     *
     * @param boolean isPublished
     * @return Event
     */
    public function setIsPublished($isPublished)
    {
        $this->isPublished = $isPublished;

        return $this;
    }
}
