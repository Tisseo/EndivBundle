<?php

namespace Tisseo\EndivBundle\Services;

use Doctrine\Common\Persistence\ObjectManager;
use Tisseo\EndivBundle\Entity\PoiType;

class PoiTypeManager extends SortManager
{
    private $om = null;
    private $repository = null;

    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
        $this->repository = $om->getRepository('TisseoEndivBundle:PoiType');
    }

    public function findAll()
    {
        return $this->repository->findAll();
    }

    public function find($PoiTypeId)
    {
        return empty($PoiTypeId) ? null : $this->repository->find($PoiTypeId);
    }

    public function save(PoiType $PoiType)
    {
        $this->om->persist($PoiType);
        $this->om->flush();
    }
}
