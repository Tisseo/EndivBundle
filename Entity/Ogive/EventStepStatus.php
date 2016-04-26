<?php

namespace Tisseo\EndivBundle\Entity\Ogive;

use Doctrine\ORM\Mapping as ORM;

/**
 * EventStepStatus
 */
class EventStepStatus extends OgiveEntity
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $color;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $eventStep;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->eventStep = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set name
     *
     * @param string $name
     * @return EventStepStatus
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set color
     *
     * @param string $color
     * @return EventStepStatus
     */
    public function setColor($color)
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Get color
     *
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * Add eventStep
     *
     * @param EventStep $eventStep
     * @return EventStepStatus
     */
    public function addEventStep(EventStep $eventStep)
    {
        $this->eventStep[] = $eventStep;

        return $this;
    }

    /**
     * Remove eventStep
     *
     * @param EventStep $eventStep
     */
    public function removeEventStep(EventStep $eventStep)
    {
        $this->eventStep->removeElement($eventStep);
    }

    /**
     * Get eventStep
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEventStep()
    {
        return $this->eventStep;
    }
}
