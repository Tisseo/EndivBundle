<?php

namespace Tisseo\EndivBundle\Services;

use Doctrine\Common\Persistence\ObjectManager;
use Tisseo\EndivBundle\Entity\StopTime;

class StopTimeManager extends SortManager
{
    /** @var \Doctrine\Common\Persistence\ObjectManager|null  */
    private $om = null;
    private $repository = null;

    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
        $this->repository = $om->getRepository('TisseoEndivBundle:StopTime');
    }

    public function findAll()
    {
        return $this->repository->findAll();
    }

    public function find($StopId)
    {
        return empty($StopId) ? null : $this->repository->find($StopId);
    }

    public function getStopTimeWhoStartBetween($startTime, $endTime, $routeStopId)
    {
        $qb = $this->om->createQueryBuilder('st')
            ->select('st')
            ->from('Tisseo\EndivBundle\Entity\StopTime', 'st')
            ->join('st.routeStop', 'rs', 'WITH', 'st.routeStop = :routeStopId')
            ->where('st.departureTime BETWEEN :time and :endTime')
            ->setParameters([
                'routeStopId' => $routeStopId,
                'time' => $startTime,
                'endTime' => $endTime,
            ])
        ;

        return $qb->getQuery()->getResult();
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
