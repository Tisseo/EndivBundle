<?php

namespace Tisseo\EndivBundle\Entity\Ogive;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Scenario
 */
class Scenario extends OgiveEntity
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
     * @var guid
     */
    private $severityId;

    /**
     * @var guid
     */
    private $causeId;

    /**
     * @var Collection
     */
    private $scenarioSteps;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->scenarioSteps = new ArrayCollection();
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
     * @return Scenario
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
     * Set severityId
     *
     * @param guid $severityId
     * @return Scenario
     */
    public function setSeverityId($severityId)
    {
        $this->severityId = $severityId;

        return $this;
    }

    /**
     * Get severityId
     *
     * @return guid 
     */
    public function getSeverityId()
    {
        return $this->severityId;
    }

    /**
     * Set causeId
     *
     * @param guid $causeId
     * @return Scenario
     */
    public function setCauseId($causeId)
    {
        $this->causeId = $causeId;

        return $this;
    }

    /**
     * Get causeId
     *
     * @return guid 
     */
    public function getCauseId()
    {
        return $this->causeId;
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
     * @return ScenarioStep
     */
    public function setScenarioSteps(Collection $scenarioSteps)
    {
        $this->scenarioSteps = $scenarioSteps;

        return $this;
    }

    /**
     * Add scenarioStep
     *
     * @param ScenarioStep $scenarioStep
     * @return ScenarioStep
     */
    public function addScenarioStep(ScenarioStep $scenarioStep)
    {
        $this->scenarioSteps->add($scenarioStep);

        return $this;
    }

    /**
     * Remove scenarioStep
     *
     * @param ScenarioStep $scenarioStep
     * @return ScenarioStep
     */
    public function removeScenarioStep(ScenarioStep $scenarioStep)
    {
        $this->scenarioSteps->removeElement($scenarioStep);

        return $this;
    }
}
