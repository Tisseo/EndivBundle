<?php

namespace Tisseo\EndivBundle\Services\Ogive;

use Tisseo\EndivBundle\Entity\Ogive\ScenarioStep;

class ScenarioStepManager extends OgiveManager
{
    public function manage(ScenarioStep $scenarioStep, array $originalTexts)
    {
        $this->updateCollection($scenarioStep, 'getScenarioStepTexts', $originalTexts);

        return $this->save($scenarioStep);
    }

    public function reapply(ScenarioStep $scenarioStep)
    {
        $scenarioSteps = $this->getRepository()->createQueryBuilder('s')
            ->where('s.name = :name')
            ->andWhere('s.scenario is not null')
            ->setParameter('name', $scenarioStep->getName())
            ->getQuery()
            ->getResult();

        $mapper = array(
            'name',
            'moment',
            'mandatory',
            'connector',
            'connectorParamList'
        );

        foreach ($scenarioSteps as $step) {
            $step->merge($scenarioStep, $mapper, false);

            // entity merge can't handle collection well, so there is the ugly code to do it
            $scenarioStepTexts = $step->cloneCollection($scenarioStep->getScenarioStepTexts());

            foreach ($step->getScenarioStepTexts() as $scenarioStepText) {
                $scenarioStepTextId = $scenarioStepText->getId();

                $delStep = array_filter(
                    $scenarioStepTexts->toArray(),
                    function ($clonedStepText) use ($scenarioStepTextId) {
                        return $clonedStepText->getId() === $scenarioStepTextId;
                    }
                );

                if (empty($delStep)) {
                    $step->removeScenarioStepText($scenarioStepText);
                    $this->objectManager->remove($scenarioStepText);
                }
            }

            foreach ($scenarioStepTexts as $clonedStepText) {
                $clonedStepTextId = $clonedStepText->getId();

                $addStep = array_filter(
                    $step->getScenarioStepTexts()->toArray(),
                    function ($stepText) use ($clonedStepTextId) {
                        return $stepText->getId() === $clonedStepTextId;
                    }
                );

                if (empty($addStep)) {
                    $step->addScenarioStepText($clonedStepText);
                }
            }

            $this->objectManager->persist($step);
        }

        $this->objectManager->flush();
    }

    private function findPatternByNameLike($term)
    {
        $data = $this->getRepository()->createQueryBuilder('s')
            ->where('s.scenario is null and s.scenarioStepParent is null and UNACCENT(lower(s.name)) like UNACCENT(:term)')
            ->setParameter('term', '%'.strtolower($term).'%')
            ->getQuery()
            ->getResult();

        return $data;
    }

    public function findScenarioStepParent($term)
    {
        $data = $this->findPatternByNameLike($term);

        $result = array();
        if (!empty($data)) {
            foreach ($data as $scenarioStep) {
                $result[] = array(
                    'label' => $scenarioStep->getName(),
                    'value' => $scenarioStep->getId()
                );
            }
        }

        return $result;
    }

    public function findStepForScenario($term)
    {
        $data = $this->findPatternByNameLike($term);

        return $this->normalize($data);
    }
}
