<?php

namespace Tisseo\EndivBundle\Entity\Ogive;

/**
 * EventStepText
 */
class EventStepText extends OgiveEntity
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
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

    const TEXT_TYPE_GENERIC = 0;
    const TEXT_TYPE_OBJECT = 1;
    const TEXT_TYPE_BODY = 2;
    const TEXT_TYPE_SUBTITLE = 3;

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
     * Set type
     *
     * @param  integer $type
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
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set label
     *
     * @param  string $label
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
     * @param  string $text
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
     * @param  EventStep $eventStep
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
