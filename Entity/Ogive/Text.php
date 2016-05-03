<?php

namespace Tisseo\EndivBundle\Entity\Ogive;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Exclude;

/**
 * Text
 * @ExclusionPolicy("none")
 */
class Text extends OgiveEntity
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
     * @Exclude
     * @var Collection
     */
    private $scenarioStepTexts;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->scenarioStepTexts = new ArrayCollection();
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
     * @return ScenarioStep
     */
    public function removeScenarioStepText(ScenarioStepText $scenarioStepText)
    {
        $this->scenarioStepTexts->removeElement($scenarioStepText);

        return $this;
    }
}
