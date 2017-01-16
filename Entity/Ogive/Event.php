<?php

namespace Tisseo\EndivBundle\Entity\Ogive;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;

/**
 * Event
 */
class Event extends OgiveEntity
{
    const STATUS_OPEN = 1;
    const STATUS_CLOSED = 2;
    const STATUS_REJECTED = 3;

    /**
     * @var array
     * Possible status for events
     */
    public static $statusMap = array(
        self::STATUS_OPEN => self::STATUS_OPEN,
        self::STATUS_CLOSED => self::STATUS_CLOSED,
        self::STATUS_REJECTED => self::STATUS_REJECTED
    );

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
     * @var unknown
     */
    private $login;

    /**
     * @var Event
     */
    private $eventParent;

    /**
     * @var Collection
     */
    private $eventSteps;

    /**
     * @var Collection
     */
    private $objects;

    /**
     * @var Collection
     */
    private $periods;

    /**
     * @var Collection
     */
    private $eventDatasources;

    /**
     * @var Collection
     */
    private $eventObjects;

    /**
     * @var Collection
     */
    private $messages;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->periods = new ArrayCollection();
        $this->objects = new ArrayCollection();
        $this->eventObjects = new ArrayCollection();
        $this->eventDatasources = new ArrayCollection();
        $this->eventSteps = new ArrayCollection();
        $this->messages = new ArrayCollection();
    }

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
     * Get login
     *
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * Set login
     *
     * @param string $login
     * @return Event
     */
    public function setLogin($login)
    {
        $this->login = $login;
        return $this;
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

    /**
     * Get eventSteps
     *
     * @return Collection
     */
    public function getEventSteps()
    {
        return $this->eventSteps;
    }

    /**
     * Set eventSteps.
     *
     * @param Collection eventSteps
     * @return Event
     */
    public function setEventSteps(Collection $eventSteps)
    {
        $this->eventSteps = $eventSteps;

        return $this;
    }

    /**
     * Get periods
     *
     * @return Collection
     */
    public function getPeriods()
    {
        return $this->periods;
    }

    /**
     * Set periods.
     *
     * @param Collection periods
     * @return Event
     */
    public function setPeriods(Collection $periods)
    {
        $this->periods = $periods;

        return $this;
    }

    /**
     * Get eventDatasources
     *
     * @return Collection
     */
    public function getEventDatasources()
    {
        return $this->eventDatasources;
    }

    /**
     * Set eventDatasources.
     *
     * @param Collection eventDatasources
     * @return Event
     */
    public function setEventDatasources(Collection $eventDatasources)
    {
        $this->eventDatasources = $eventDatasources;

        return $this;
    }

    /**
     * Get eventobjects
     *
     * @return Collection
     */
    public function getEventObjects()
    {
        return $this->eventObjects;
    }

    /**
     * Set event objects
     *
     * @param Collection eventObjects
     * @return Event
     */
    public function setEventObjects(Collection $eventObjects)
    {
        $this->eventObjects = $eventObjects;

        return $this;
    }

    /**
     * Add event object
     *
     * @param EventObject $eventObject
     * @return Event
     */
    public function addEventObject(EventObject $eventObject)
    {
        $this->eventObjects->add($eventObject);

        return $this;
    }

    /**
     * Get objects
     *
     * @return Collection
     */
    public function getObjects()
    {
        return $this->objects;
    }

    /**
     * Set objects
     *
     * @param Collection objects
     * @return Event
     */
    public function setObjects(Collection $objects)
    {
        $this->objects = $objects;

        return $this;
    }

    /**
     * Add event object
     *
     * @param Object $object
     * @return Event
     */
    public function addObject(Object $object)
    {
        $this->objects->add($object);

        return $this;
    }

    /**
     * Get extrema period dates
     *
     * @return array
     */
    public function getExtremaPeriodDates()
    {
        $extrema = array();

        if ($this->getPeriods()->count() == 0) {
            return $extrema;
        }

        $min = Criteria::create()
            ->orderBy(array('startDate' => Criteria::ASC))
            ->setMaxResults(1)
        ;

        $max = Criteria::create()
            ->orderBy(array('endDate' => Criteria::DESC))
            ->setMaxResults(1)
        ;

        $extrema['min'] = $this->periods->matching($min)->first()->getStartDate();
        $extrema['max'] = $this->periods->matching($max)->first()->getEndDate();

        return $extrema;
    }

    /**
     * Add message
     *
     * @param Message $message
     * @return Message
     */
    public function addMessage(Message $message)
    {
        $this->message->add($message);

        return $this;
    }

    /**
     * Remove message
     *
     * @param Message $message
     * @return Message
     */
    public function removeMessage(Message $message)
    {
        $this->message->removeElement($message);

        return $this;
    }

    /**
     * Get messages
     *
     * @return Collection
     */
    public function getMessages()
    {
        return $this->messages;
    }
}
