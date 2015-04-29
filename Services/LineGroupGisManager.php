<?php

namespace Tisseo\EndivBundle\Services;

use Doctrine\Common\Persistence\ObjectManager;
use Tisseo\EndivBundle\Entity\LineGroupGis;



class LineGroupGisManager extends SortManager
{
    private $om = null;

    /** @var \Doctrine\ORM\EntityRepository $repository */
    private $repository = null;

    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
        $this->repository = $om->getRepository('TisseoEndivBundle:LineGroupGis');
    }

    public function findAll()
    {
        return ($this->repository->findAll());
    }

    public function find($lineGroupGisId)
    {
        return empty($lineGroupGisId) ? null : $this->repository->find($lineGroupGisId);
    }


    /*
     * save
     * @param Schematic $schematic
     * @return array(boolean, string)
     *
     * Persist and save a Schematic into database.
     */
    public function save(LineGroupGis $lineGroupGis)
    {
        $this->om->persist($lineGroupGis);
        $this->om->flush();

        return array(true,'line_group_gis.persisted');
    }
}
