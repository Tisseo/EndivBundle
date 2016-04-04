<?php

namespace Tisseo\EndivBundle\Services\Ogive;

use Tisseo\EndivBundle\Entity\Ogive\ScenarioStep;

class ScenarioStepManager extends OgiveManager
{
    public function reapply(ScenarioStep $scenarioStep)
    {
        $scenarioSteps = $this->getRepository()->createQueryBuilder('s')
            ->where('s.name = :name')
            ->andWhere('s.scenario is not null')
            ->setParameter('name', $scenarioStep->getName())
            ->getQuery()
            ->getResult()
        ;

        $mapper = array(
            'name',
            'moment',
            'mandatory',
            'connector',
            'connectorParamList',
            'scenarioStepParent'
        );

        foreach ($scenarioSteps as $step) {
            $step->merge($scenarioStep, $mapper, false);
            $this->objectManager->persist($step);
        }

        $this->objectManager->flush();
    }

    private function findPatternByNameLike($term)
    {
        $data = $this->getRepository()->createQueryBuilder('s')
            ->where('s.scenario is null and s.scenarioStepParent is null and lower(s.name) like :term')
            ->setParameter('term', '%'.strtolower($term).'%')
            ->getQuery()
            ->getResult()
        ;

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
