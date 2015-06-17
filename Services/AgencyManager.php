<?php

namespace Tisseo\EndivBundle\Services;

use Doctrine\Common\Persistence\ObjectManager;
use Tisseo\EndivBundle\Entity\Agency;

class AgencyManager extends SortManager
{
    private $om = null;
    private $repository = null;

    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
        $this->repository = $om->getRepository('TisseoEndivBundle:Agency');
    }

    public function findAll()
    {
        return ($this->repository->findAll());
    }

    public function find($AgencyId)
    {
        return empty($AgencyId) ? null : $this->repository->find($AgencyId);
    }

    public function save(Agency $Agency)
    {
        $this->om->persist($Agency);
        $this->om->flush();
    }
}
