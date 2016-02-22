<?php

namespace Tisseo\EndivBundle\Entity\Ogive;

use Doctrine\ORM\Mapping as ORM;

/**
 * Event
 */
class Event
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var guid
     */
    private $chaosType;

    /**
     * @var guid
     */
    private $chaosCause;

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
    private $trafficReportId;

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
     * Set chaosType
     *
     * @param guid $chaosType
     * @return Event
     */
    public function setChaosType($chaosType)
    {
        $this->chaosType = $chaosType;

        return $this;
    }

    /**
     * Get chaosType
     *
     * @return guid 
     */
    public function getChaosType()
    {
        return $this->chaosType;
    }

    /**
     * Set chaosCause
     *
     * @param guid $chaosCause
     * @return Event
     */
    public function setChaosCause($chaosCause)
    {
        $this->chaosCause = $chaosCause;

        return $this;
    }

    /**
     * Get chaosCause
     *
     * @return guid 
     */
    public function getChaosCause()
    {
        return $this->chaosCause;
    }

    /**
     * Set eventStatusId
     *
     * @param integer $eventStatusId
     * @return Event
     */
    public function setEventStatusId($eventStatusId)
    {
        $this->eventStatusId = $eventStatusId;

        return $this;
    }

    /**
     * Get eventStatusId
     *
     * @return integer 
     */
    public function getEventStatusId()
    {
        return $this->eventStatusId;
    }

    /**
     * Set trafficReportId
     *
     * @param guid $trafficReportId
     * @return Event
     */
    public function setTrafficReportId($trafficReportId)
    {
        $this->trafficReportId = $trafficReportId;

        return $this;
    }

    /**
     * Get trafficReportId
     *
     * @return guid 
     */
    public function getTrafficReportId()
    {
        return $this->trafficReportId;
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
