<?php

namespace Tisseo\EndivBundle\Services;

use Doctrine\Common\Persistence\ObjectManager;
use Tisseo\EndivBundle\Entity\StopTime;

class StopTimeManager extends SortManager
{
    /** @var \Doctrine\Common\Persistence\ObjectManager|null */
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

  /**
   * Get StopTimes with rank routeStop 1  by tripId ordered by departure time
   *
   * @param int $tripId Trip Id
   * @param string $order Default to 'ASC' (ASC|DESC)
   * @param bool $first If true, limit to 1 result (default), set it to false for no limitation
   *
   * @return mixed
   */
    public function getStopTimesByTripId($tripId, $routeStopId, $order='ASC', $first=true)
    {
      $qb = $this->om->createQueryBuilder('st')
        ->select ('st, tr, rs')
        ->from('Tisseo\EndivBundle\Entity\StopTime', 'st')
        ->join('st.routeStop', 'rs', 'WITH', 'st.routeStop = :routeStopId')
        ->join('st.trip', 'tr')
        ->where('st.trip = :tripId')
        ->orderBy('st.departureTime', $order);
      if ($first) {
        $qb->setMaxResults(1);
      }
      $qb->setParameters([
        'tripId' => $tripId,
        'routeStopId' => $routeStopId,
      ]);

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
