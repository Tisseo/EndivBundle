<?php

namespace Tisseo\EndivBundle\Entity\Ogive;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Connector
 */
class Connector extends OgiveEntity
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
    private $type;

    /**
     * @var string
     */
    private $details;

    /**
     * @var Collection
     */
    private $scenarioSteps;

    /**
     * @var Collection
     */
    private $eventSteps;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->scenarioSteps = new ArrayCollection();
        $this->eventSteps = new ArrayCollection();
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
     * @return Connector
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
     * Set type
     *
     * @param string $type
     * @return Connector
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set details
     *
     * @param string $details
     * @return Connector
     */
    public function setDetails($details)
    {
        $this->details = $details;

        return $this;
    }

    /**
     * Get details
     *
     * @return string
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * Get scenarioSteps
     *
     * @return Collection
     */
    public function getScenarioSteps()
    {
        return $this->scenarioSteps;
    }

    /**
     * Set scenarioSteps
     *
     * @param Collection $scenarioSteps
     * @return ConnectorParamList
     */
    public function setScenarioSteps(Collection $scenarioSteps)
    {
        $this->scenarioSteps = $scenarioSteps;

        return $this;
    }

    /**
     * Get eventSteps
     *
     * @return Collection $eventSteps
     */
    public function getEventSteps()
    {
        return $this->eventSteps;
    }

    /**
     * Set eventSteps
     *
     * @param Collection eventSteps
     * @return ConnectorParamList
     */
    public function setEventSteps(Collection $eventSteps)
    {
        $this->eventSteps = $eventSteps;

        return $this;
    }

    /**
     * isLinked
     *
     * @return boolean
     */
    public function isLinked()
    {
        if ($this->eventSteps->isEmpty() && $this->scenarioSteps->isEmpty()) {
            return false;
        }

        return true;
    }
}
