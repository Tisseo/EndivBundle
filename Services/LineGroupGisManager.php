<?php

namespace Tisseo\EndivBundle\Services;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Collections\ArrayCollection;
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
        $lineGroupGisContents = clone $lineGroupGis->getLineGroupGisContents();

        if ($lineGroupGis->getId() === null)
        {
            $lineGroupGis->clearLineGroupGisContents();
            $this->om->persist($lineGroupGis);
        }

        foreach($lineGroupGisContents as $lineGroupGisContent)
        {
            $lineGroupGisContent->setLineGroupGis($lineGroupGis);
            $this->om->persist($lineGroupGisContent);
        }

        $this->om->persist($lineGroupGis);
        $this->om->flush();
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
    }
}
