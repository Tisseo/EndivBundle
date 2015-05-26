<?php

namespace Tisseo\EndivBundle\Services;

use Doctrine\Common\Persistence\ObjectManager;
use Tisseo\EndivBundle\Entity\OdtArea;

class OdtAreaManager extends SortManager
{
    private $om = null;
    private $repository = null;

    public function __construct(ObjectManager $om)
    {   
        $this->om = $om;
        $this->repository = $om->getRepository('TisseoEndivBundle:OdtArea');
    }   

    public function findAll()
    {   
        return ($this->repository->findAll());
    }   

    public function find($OdtAreaId)
    {   
        return empty($OdtAreaId) ? null : $this->repository->find($OdtAreaId);
    }

    public function save(OdtArea $OdtArea)
    {
        $this->om->persist($OdtArea);
        $this->om->flush();
    }
}
