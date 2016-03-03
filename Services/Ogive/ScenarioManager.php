<?php

namespace Tisseo\EndivBundle\Services\Ogive;

use Doctrine\Common\Collections\ArrayCollection;
use Tisseo\EndivBundle\Entity\Ogive\Scenario;

class ScenarioManager extends OgiveManager
{
    public function manage(Scenario $scenario, array $originalSteps)
    {
        $this->updateCollection($scenario, 'getScenarioSteps', $originalSteps);

        $parent = null;
        foreach($scenario->getScenarioSteps() as $scenarioStep) {
            if ($scenarioStep->getScenario() === null) {
                if ($scenarioStep->getScenarioStepParent() === null) {
                    $parent = $scenarioStep;
                } else {
                    $scenarioStep->setScenarioStepParent($parent);
                }
                $scenarioStep->setScenario($scenario);
            }
        }

        return $this->save($scenario);
    }
}
