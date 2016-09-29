<?php

namespace Tisseo\EndivBundle\Entity\Ogive;

/**
 * ScenarioStepText
 */
class ScenarioStepText extends OgiveEntity
{
    /**
     * @var integer
     */
    private $type;

    /**
     * @var integer
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
     * @param  integer $type
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
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set rank
     *
     * @param  integer $rank
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
     * @return integer
     */
    public function getRank()
    {
        return $this->rank;
    }

    /**
     * Set scenarioStep
     *
     * @param  ScenarioStep $scenarioStep
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
     * @param  Text $text
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
