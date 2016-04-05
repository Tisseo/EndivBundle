<?php

namespace Tisseo\EndivBundle\Entity\Ogive;

use Doctrine\ORM\Mapping as ORM;

/**
 * ScenarioStepText
 */
class ScenarioStepText extends OgiveEntity
{
    /**
     * @var integer
     */
    private $scenarioStepId;

    /**
     * @var integer
     */
    private $textId;

    /**
     * @var string
     */
    private $label;

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
        return $this->scenarioStepId.'_'.$this->textId;
    }

    /**
     * Set label
     *
     * @param string $label
     * @return ScenarioStepText
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
     * Set rank
     *
     * @param integer $rank
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
     * @param ScenarioStep $scenarioStep
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
