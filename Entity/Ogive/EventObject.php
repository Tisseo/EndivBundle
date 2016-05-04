<?php

namespace Tisseo\EndivBundle\Entity\Ogive;

/**
 * EventObject
 */
class EventObject extends OgiveEntity
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
    private $object;


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
     * Set object
     *
     * @param Object $object
     * @return EventObject
     */
    public function setObject(Object $object = null)
    {
        $this->object = $object;

        return $this;
    }

    /**
     * Get object
     *
     * @return Object
     */
    public function getObject()
    {
        return $this->object;
    }
}
