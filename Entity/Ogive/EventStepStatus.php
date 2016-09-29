<?php

namespace Tisseo\EndivBundle\Entity\Ogive;

/**
 * EventStepStatus
 */
class EventStepStatus extends OgiveEntity
{
    const STATUS_TODO = 1;
    const STATUS_VALIDATED = 2;
    const STATUS_REJECTED = 3;

    /**
     * @var array
     * Possible status for events
     */
    public static $statusMap = array(
        self::STATUS_TODO => self::STATUS_TODO,
        self::STATUS_VALIDATED => self::STATUS_VALIDATED,
        self::STATUS_REJECTED => self::STATUS_REJECTED
    );

    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $status;

    /**
     * @var \DateTime
     */
    private $dateTime;

    /**
     * @var string
     */
    private $userComment;

    /**
     * @var string
     */
    private $login;

    /**
     * @var EventStep
     */
    private $eventStep;

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
     * Set id
     *
     * @param  integer $id
     * @return EventStepStatus
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get status
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set Status
     *
<<<<<<< HEAD:Entity/Ogive/EventStepStatus.php
     * @param integer $status
     * @return EventStepStatus
=======
     * @param  integer $status
     * @return LinkEventStepStatus
>>>>>>> d844f7c... syntax: use phpcs-fixer:Entity/Ogive/LinkEventStepStatus.php
     */
    public function setStatus($status)
    {
        if (!in_array($status, self::$statusMap)) {
            throw new \Exception(sprintf('The status %s is invalid', $status));
        }

        $this->status = $status;

        return $this;
    }

    /**
     * Get dateTime
     */
    public function getDateTime()
    {
        return $this->dateTime;
    }

    /**
     * Set dateTime
     *
<<<<<<< HEAD:Entity/Ogive/EventStepStatus.php
     * @param \DateTime $dateTime
     * @return EventStepStatus
=======
     * @param  \DateTime $dateTime
     * @return LinkEventStepStatus
>>>>>>> d844f7c... syntax: use phpcs-fixer:Entity/Ogive/LinkEventStepStatus.php
     */
    public function setDateTime(\DateTime $dateTime)
    {
        $this->dateTime = $dateTime;

        return $this;
    }

    /**
     * Get userComment
     */
    public function getUserComment()
    {
        return $this->userComment;
    }

    /**
     * Set userComment
     *
<<<<<<< HEAD:Entity/Ogive/EventStepStatus.php
     * @param string $userComment
     * @return EventStepStatus
=======
     * @param  string $userComment
     * @return LinkEventStepStatus
>>>>>>> d844f7c... syntax: use phpcs-fixer:Entity/Ogive/LinkEventStepStatus.php
     */
    public function setUserComment($userComment)
    {
        $this->userComment = $userComment;

        return $this;
    }

    /**
     * Get login
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * Set login
     *
<<<<<<< HEAD:Entity/Ogive/EventStepStatus.php
     * @param string $login
     * @return EventStepStatus
=======
     * @param  string $login
     * @return LinkEventStepStatus
>>>>>>> d844f7c... syntax: use phpcs-fixer:Entity/Ogive/LinkEventStepStatus.php
     */
    public function setLogin($login)
    {
        $this->login = $login;

        return $this;
    }

    /**
     * Get eventStep
     *
     * @return EventStep
     */
    public function getEventStep()
    {
        return $this->eventStep;
    }

    /**
     * Set eventStep
     *
<<<<<<< HEAD:Entity/Ogive/EventStepStatus.php
     * @param EventStep $eventStep
     * @return EventStepStatus
=======
     * @param  EventStep $eventStep
     * @return LinkEventStepStatus
>>>>>>> d844f7c... syntax: use phpcs-fixer:Entity/Ogive/LinkEventStepStatus.php
     */
    public function setEventStep($eventStep)
    {
        $this->eventStep = $eventStep;

        return $this;
    }
}
