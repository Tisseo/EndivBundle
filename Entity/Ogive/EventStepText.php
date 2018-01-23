<?php

namespace Tisseo\EndivBundle\Entity\Ogive;

/**
 * EventStepText
 */
class EventStepText extends OgiveEntity
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $type;

    /**
     * @var string
     */
    private $label;

    /**
     * @var string
     */
    private $text;

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
     * Set type
     *
     * @param int $type
     *
     * @return EventStepText
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set label
     *
     * @param string $label
     *
     * @return EventStepText
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set text
     *
     * @param string $text
     *
     * @return EventStepText
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set eventStep
     *
     * @param EventStep $eventStep
     *
     * @return EventStepText
     */
    public function setEventStep(EventStep $eventStep = null)
    {
        $this->eventStep = $eventStep;

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
}
