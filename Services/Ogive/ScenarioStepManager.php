<?php

namespace Tisseo\EndivBundle\Services\Ogive;

class ScenarioStepManager extends OgiveManager
{
    private function findPatternByNameLike($term)
    {
        $data = $this->repository->createQueryBuilder('s')
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
