<?php

namespace Tisseo\EndivBundle\Entity\Ogive;

/**
 * ScenarioStepText
 */
class ScenarioStepText extends OgiveEntity
{
    /**
     * @var int
     */
    private $scenarioStepId;

    /**
     * @var int
     */
    private $textId;

    /**
     * @var int
     */
    private $type;

    /**
     * @var int
     */
    private $rank;

    /**
     * @var ScenarioStep
     */
    private $scenarioStep;

    /**
     * @var Text
     */
    private $text;

    /**
     * Get id
     *
     * @return string
     */
    public function getId()
    {
        return $this->scenarioStep->getId().'_'.$this->text->getId();
    }

    /**
     * Set type
     *
     * @param int $type
     *
     * @return ScenarioStepText
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
     * Set rank
     *
     * @param int $rank
     *
     * @return ScenarioStepText
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
     * Set scenarioStep
     *
     * @param ScenarioStep $scenarioStep
     *
     * @return ScenarioStepText
     */
    public function setScenarioStep(ScenarioStep $scenarioStep = null)
    {
        $this->scenarioStep = $scenarioStep;

        return $this;
    }

    /**
     * Get scenarioStep
     *
     * @return ScenarioStep
     */
    public function getScenarioStep()
    {
        return $this->scenarioStep;
    }

    /**
     * Set text
     *
     * @param Text $text
     *
     * @return ScenarioStepText
     */
    public function setText(Text $text = null)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return Text
     */
    public function getText()
    {
        return $this->text;
    }
}
