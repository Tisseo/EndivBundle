<?php

namespace Tisseo\EndivBundle\Entity\Ogive;

use Doctrine\ORM\Mapping as ORM;

/**
 * EventObject
 */
class EventObject
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var Event
     */
    private $event;

    /**
     * @var EmergencyStatus
     */
    private $emergencyStatus;

    /**
     * @var Object
     */
    private $objects;


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
     * Set event
     *
     * @param Event $event
     * @return EventObject
     */
    public function setEvent(Event $event = null)
    {
        $this->event = $event;

        return $this;
    }

    /**
     * Get event
     *
     * @return Event 
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * Set emergencyStatus
     *
     * @param EmergencyStatus $emergencyStatus
     * @return EventObject
     */
    public function setEmergencyStatus(EmergencyStatus $emergencyStatus = null)
    {
        $this->emergencyStatus = $emergencyStatus;

        return $this;
    }

    /**
     * Get emergencyStatus
     *
     * @return EmergencyStatus 
     */
    public function getEmergencyStatus()
    {
        return $this->emergencyStatus;
    }

    /**
     * Set objects
     *
     * @param Object $objects
     * @return EventObject
     */
    public function setObject(Object $objects = null)
    {
        $this->objects = $objects;

        return $this;
    }

    /**
     * Get objects
     *
     * @return Object 
     */
    public function getObject()
    {
        return $this->objects;
    }
}
