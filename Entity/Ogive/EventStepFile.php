<?php

namespace Tisseo\EndivBundle\Entity\Ogive;

use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Boolean;

/**
 * GroupObject
 */
class EventStepFile
{
    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var string $filename
     */
    private $filename;

    /**
     * @var string $label
     */
    private $label;

    /**
     * @var boolean $deleted
     */
    private $deleted;

    /**
     * @var $eventStep
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
     * Set filename
     *
     * @param string $filename
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
     * @param \Tisseo\EndivBundle\Entity\Ogive\EventStep
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
     * @param boolean $deleted
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
     * @return boolean
     */
    public function getDeleted()
    {
        return $this->deleted;
    }
}
