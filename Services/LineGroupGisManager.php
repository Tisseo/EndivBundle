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


    /**
     * save
     * @param LineGroupGis $lineGroupGis
     * @return array(boolean, string message, LineGroupGis)
     *
     * Persist and save a LineGroupGis into database.
     */
    public function save(LineGroupGis $lineGroupGis)
    {
        $this->om->persist($lineGroupGis);
        $this->om->flush();

        return array(true,'line_group_gis.persisted', $lineGroupGis);
    }

    /**
     * @param LineGroupGis $lineGroupGis
     * @return array(boolean, string message)
     *
     * Remove LineGroupGis into database
     */
    public function remove(LineGroupGis $lineGroupGis)
    {
        $this->om->remove($lineGroupGis);
        $this->om->flush();

        return array(true,'line_group_gis.removed');
    }
}
