<?php

namespace Tisseo\EndivBundle\Entity\Ogive;

use Doctrine\ORM\Mapping as ORM;

/**
 * EventStepText
 */
class EventStepText
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $labelType;

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
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set labelType
     *
     * @param string $labelType
     * @return EventStepText
     */
    public function setLabelType($labelType)
    {
        $this->labelType = $labelType;

        return $this;
    }

    /**
     * Get labelType
     *
     * @return string 
     */
    public function getLabelType()
    {
        return $this->labelType;
    }

    /**
     * Set text
     *
     * @param string $text
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
