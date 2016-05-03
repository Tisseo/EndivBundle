<?php

namespace Tisseo\EndivBundle\Services\Ogive;

use Doctrine\Common\Collections\ArrayCollection;
use Tisseo\EndivBundle\Entity\Ogive\Scenario;

class ScenarioManager extends OgiveManager
{
    public function manage(Scenario $scenario, array $originalSteps)
    {
        $this->updateCollection($scenario, 'getScenarioSteps', $originalSteps);

        $parents = $scenario->getScenarioSteps()->filter(
            function($step) {
                return $step->getScenarioStepParent() === null;
            }
        );

        foreach($scenario->getScenarioSteps() as $scenarioStep) {
            if ($scenarioStep->getScenario() === null) {
                if ($scenarioStep->getScenarioStepParent() !== null) {
                    $name = $scenarioStep->getScenarioStepParent()->getName();
                    $parent = $parents->filter(
                        function($step) use ($name) {
                            return $step->getName() === $name;
                        }
                    )->first();
                    $scenarioStep->setScenarioStepParent($parent);
                }

                $scenarioStepTexts = $scenarioStep->getScenarioStepTexts();
                $scenarioStep->setScenarioStepTexts(new ArrayCollection());
                foreach ($scenarioStepTexts as $originalScenarioST) {
                    $scenarioStepText = clone $originalScenarioST;
                    $scenarioStepText->setScenarioStep($scenarioStep);
                    $scenarioStep->addScenarioStepText($scenarioStepText);
                }

                $scenarioStep->setScenario($scenario);
            }
        }

        return $this->save($scenario);
    }
}
