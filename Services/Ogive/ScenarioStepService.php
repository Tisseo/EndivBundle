<?php

namespace Tisseo\EndivBundle\Services\Ogive;

use Doctrine\Common\Persistence\ObjectManager;

class ScenarioStepManager
{
    private $om = null;
    private $repository = null;

    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
        $this->repository = $om->getRepository('TisseoEndivBundle:Ogive\ScenarioStep');
    }

    public function save(ScenarioStep $scenarioStep)
    {
        $this->om->persist($scenarioStep);
        $this->om->flush();
    }
}
