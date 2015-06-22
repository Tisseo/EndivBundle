<?php

namespace Tisseo\EndivBundle\Services;

use Doctrine\Common\Persistence\ObjectManager;
use Tisseo\EndivBundle\Entity\PhysicalMode;

class PhysicalModeManager extends SortManager
{
    private $om = null;
    private $repository = null;

    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
        $this->repository = $om->getRepository('TisseoEndivBundle:PhysicalMode');
    }

    public function findAll()
    {
        return ($this->repository->findAll());
    }

    public function find($physicalModeId)
    {
        return empty($physicalModeId) ? null : $this->repository->find($physicalModeId);
    }

    public function save(PhysicalMode $physicalMode)
    {
        $this->om->persist($physicalMode);
        $this->om->flush();
    }
}
