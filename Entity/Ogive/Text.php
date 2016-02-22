<?php

namespace Tisseo\EndivBundle\Entity\Ogive;

use Doctrine\ORM\Mapping as ORM;

/**
 * Text
 */
class Text
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $label;

    /**
     * @var string
     */
    private $text;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $scenarioStep;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->scenarioStep = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set label
     *
     * @param string $label
     * @return Text
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
     * @param string $text
     * @return Text
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
     * Add scenarioStep
     *
     * @param ScenarioStep $scenarioStep
     * @return Text
     */
    public function addScenarioStep(ScenarioStep $scenarioStep)
    {
        $this->scenarioStep[] = $scenarioStep;

        return $this;
    }

    /**
     * Remove scenarioStep
     *
     * @param ScenarioStep $scenarioStep
     */
    public function removeScenarioStep(ScenarioStep $scenarioStep)
    {
        $this->scenarioStep->removeElement($scenarioStep);
    }

    /**
     * Get scenarioStep
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getScenarioStep()
    {
        return $this->scenarioStep;
    }
}
