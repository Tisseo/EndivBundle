<?php

namespace Tisseo\EndivBundle\Entity\Ogive;

/**
 * GroupObject
 */
class EventStepFile
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $filename;

    /**
     * @var string
     */
    private $label;

    /**
     * @var bool
     */
    private $deleted;

    /**
     * @var
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
     * Set filename
     *
     * @param string $filename
     *
     * @return EventStepFile
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Get filename
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set groupType
     *
     * @param string $label
     *
     * @return EventStepFile
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
     * Set event step
     *
     * @param \Tisseo\EndivBundle\Entity\Ogive\EventStep
     *
     * @return $this
     */
    public function setEventStep(EventStep $eventStep)
    {
        $this->eventStep = $eventStep;

        return $this;
    }

    /**
     * Get event step
     *
     * @return \Tisseo\EndivBundle\Entity\Ogive\EventStep
     */
    public function getEventStep()
    {
        return $this->eventStep;
    }

    /**
     * Set $deleted
     * Set to true to specify this file has been physically removed
     *
     * @param bool $deleted
     *
     * @return EventStepFile
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * Get deleted
     * Is file has been physically removed ?
     *
     * @return bool
     */
    public function getDeleted()
    {
        return $this->deleted;
    }
}
