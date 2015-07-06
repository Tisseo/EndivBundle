<?php

namespace Tisseo\EndivBundle\Services;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\EntityManager;

use Tisseo\EndivBundle\Entity\Stop;
use Tisseo\EndivBundle\Entity\StopTime;


class StopTimeManager extends SortManager
{
    private $om = null;
    private $repository = null;

    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
        $this->repository = $om->getRepository('TisseoEndivBundle:StopTime');
    }

    public function findAll()
    {
        return ($this->repository->findAll());
    }

    public function find($StopId)
    {
        return empty($StopId) ? null : $this->repository->find($StopId);
    }

    //TODO: This seems to be bad, change/delete
    public function save(StopTime $Stop)
    {
        if (!$Stop->getId()) {
            // new stop + new stop_history

            $this->om->persist($Stop);
            $this->om->flush();
            $this->om->refresh($Stop);
            $newId = $Stop->getId();

            $Stop->setId($newId);

        }

        $this->om->persist($Stop);
        $this->om->flush();
        $this->om->refresh($Stop);
    }


}
