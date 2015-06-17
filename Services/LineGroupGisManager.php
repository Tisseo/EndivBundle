<?php

namespace Tisseo\EndivBundle\Services;

use Doctrine\Common\Persistence\ObjectManager;
use Tisseo\EndivBundle\Entity\LineGroupGis;



class LineGroupGisManager extends SortManager
{
    /**
     * @var ObjectManager $om
     */
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
        try {
            /** @var  \Tisseo\EndivBundle\Entity\LineGroupGisContent $lineGroupGisContent*/
            foreach($lineGroupGis->getLineGroupGisContents() as $lineGroupGisContent) {
                $lineGroupGisContent->setLineGroupGis($lineGroupGis);
            }

            $this->om->persist($lineGroupGis);

            /** @var  \Tisseo\EndivBundle\Entity\LineGroupGisContent $lineGroupGisContent*/
            foreach($lineGroupGis->getLineGroupGisContents() as $lineGroupGisContent) {
                $lineGroupGisContent->setLineGroupGis($lineGroupGis);
                $this->om->persist($lineGroupGisContent);
            }

            $this->om->persist($lineGroupGis);
            $this->om->flush();
        } catch(\Exception $e) {
            return array($lineGroupGis, 'line_group_gis.error_persist', $e->getMessage());
        }

        return array($lineGroupGis, 'line_group_gis.persisted', null);
    }

    /**
     * @param LineGroupGis $lineGroupGis
     * @return array(boolean, string message)
     *
     * Remove LineGroupGis into database
     */
    public function remove(LineGroupGis $lineGroupGis)
    {
        try {
            $this->om->remove($lineGroupGis);
            $this->om->flush();
        } catch(\Exception $e) {
            return array('line_group_gis.error_remove', $e->getMessage());
        }

        return array('line_group_gis.removed', null);
    }
}
