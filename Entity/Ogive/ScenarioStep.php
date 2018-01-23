<?php

namespace Tisseo\EndivBundle\Entity\Ogive;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Tisseo\EndivBundle\Types\Ogive\MomentType;

/**
 * ScenarioStep
 */
class ScenarioStep extends OgiveEntity
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $rank;

    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $moment;

    /**
     * @var ScenarioStep
     */
    private $scenarioStepParent;

    /**
     * @var Scenario
     */
    private $scenario;

    /**
     * @var Connector
     */
    private $connector;

    /**
     * @var ConnectorParamList
     */
    private $connectorParamList;

    /**
     * @var Collection
     */
    private $scenarioStepTexts;

    /**
     * @var Collection
     */
    private $scenarioSteps;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->scenarioStepTexts = new ArrayCollection();
        $this->scenarioSteps = new ArrayCollection();
    }

    /**
     * Set id
     *
     * @param int $identifier
     *
     * @return ScenarioStep
     */
    public function setId($identifier)
    {
        $this->id = $identifier;

        return $this;
    }

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
     * Set rank
     *
     * @param int $rank
     *
     * @return ScenarioStep
     */
    public function setRank($rank)
    {
        $this->rank = $rank;

        return $this;
    }

    /**
     * Get rank
     *
     * @return int
     */
    public function getRank()
    {
        return $this->rank;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return ScenarioStep
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
     * Set moment
     *
     * @param int $moment
     *
     * @return ScenarioStep
     */
    public function setMoment($moment)
    {
        if (!in_array($moment, MomentType::$momentTypes)) {
            throw new \Exception(sprintf('Invalid moment value : %s', $moment));
        }
        $this->moment = $moment;

        return $this;
    }

    /**
     * Get moment
     *
     * @return int
     */
    public function getMoment()
    {
        return $this->moment;
    }

    /**
     * Set scenarioStepParent
     *
     * @param ScenarioStep $scenarioStepParent
     *
     * @return ScenarioStep
     */
    public function setScenarioStepParent(ScenarioStep $scenarioStepParent = null)
    {
        $this->scenarioStepParent = $scenarioStepParent;

        return $this;
    }

    /**
     * Get scenarioStepParent
     *
     * @return ScenarioStep
     */
    public function getScenarioStepParent()
    {
        return $this->scenarioStepParent;
    }

    /**
     * Set scenario
     *
     * @param Scenario $scenario
     *
     * @return ScenarioStep
     */
    public function setScenario(Scenario $scenario = null)
    {
        $this->scenario = $scenario;

        return $this;
    }

    /**
     * Get scenario
     *
     * @return Scenario
     */
    public function getScenario()
    {
        return $this->scenario;
    }

    /**
     * Set connector
     *
     * @param Connector $connector
     *
     * @return ScenarioStep
     */
    public function setConnector(Connector $connector = null)
    {
        $this->connector = $connector;

        return $this;
    }

    /**
     * Get connector
     *
     * @return Connector
     */
    public function getConnector()
    {
        return $this->connector;
    }

    /**
     * Set connectorParamList
     *
     * @param ConnectorParamList $connectorParamList
     *
     * @return ScenarioStep
     */
    public function setConnectorParamList(ConnectorParamList $connectorParamList = null)
    {
        $this->connectorParamList = $connectorParamList;

        return $this;
    }

    /**
     * Get connectorParamList
     *
     * @return ConnectorParamList
     */
    public function getConnectorParamList()
    {
        return $this->connectorParamList;
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
     *
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
     *
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
     *
     * @return ScenarioStep
     */
    public function removeScenarioStep(ScenarioStep $scenarioStep)
    {
        $this->scenarioSteps->removeElement($scenarioStep);

        return $this;
    }

    /**
     * Get scenarioStepTexts
     *
     * @return Collection
     */
    public function getScenarioStepTexts()
    {
        return $this->scenarioStepTexts;
    }

    /**
     * Set scenarioStepTexts
     *
     * @param Collection $scenarioStepTexts
     *
     * @return ScenarioStep
     */
    public function setScenarioStepTexts(Collection $scenarioStepTexts)
    {
        $this->scenarioStepTexts = $scenarioStepTexts;

        return $this;
    }

    /**
     * Add scenarioStepText
     *
     * @param ScenarioStep $scenarioStepText
     *
     * @return ScenarioStep
     */
    public function addScenarioStepText(ScenarioStepText $scenarioStepText)
    {
        $this->scenarioStepTexts->add($scenarioStepText);

        return $this;
    }

    /**
     * Remove scenarioStepText
     *
     * @param ScenarioStepText $scenarioStepText
     *
     * @return ScenarioStep
     */
    public function removeScenarioStepText(ScenarioStepText $scenarioStepText)
    {
        $this->scenarioStepTexts->removeElement($scenarioStepText);

        return $this;
    }
}
