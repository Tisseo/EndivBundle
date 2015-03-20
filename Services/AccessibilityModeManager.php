<?php

namespace Tisseo\EndivBundle\Services;

use Doctrine\Common\Persistence\ObjectManager;
use Tisseo\EndivBundle\Entity\AccessibilityMode;

class AccessibilityModeManager extends SortManager
{
    private $om = null;
    private $repository = null;

    public function __construct(ObjectManager $om)
    {   
        $this->om = $om;
        $this->repository = $om->getRepository('TisseoEndivBundle:AccessibilityMode');
    }   

    public function findAll()
    {   
        return ($this->repository->findAll());
    }   

    public function find($AccessibilityModeId)
    {   
        return empty($AccessibilityModeId) ? null : $this->repository->find($AccessibilityModeId);
    }

    public function save(AccessibilityMode $AccessibilityMode)
    {
        $this->om->persist($AccessibilityMode);
        $this->om->flush();
    }
}
