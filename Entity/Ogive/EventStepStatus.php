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
     *            Possible status for events
     */
    public static $statusMap = array(
        self::STATUS_TODO => self::STATUS_TODO,
        self::STATUS_VALIDATED => self::STATUS_VALIDATED,
        self::STATUS_REJECTED => self::STATUS_REJECTED
    );

    /**
     * @var int
     */
    private $id;

    /**
     * @var int
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
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id
     *
     * @param int $id
     *
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
     * @param int $status
     *
     * @return EventStepStatus
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
     * @param \DateTime $dateTime
     *
     * @return EventStepStatus
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
     * @param string $userComment
     *
     * @return EventStepStatus
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
     * @param string $login
     *
     * @return EventStepStatus
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
     * @param EventStep $eventStep
     *
     * @return EventStepStatus
     */
    public function setEventStep($eventStep)
    {
        $this->eventStep = $eventStep;

        return $this;
    }
}
