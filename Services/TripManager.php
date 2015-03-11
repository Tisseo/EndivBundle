<?php

namespace Tisseo\EndivBundle\Services;

use Doctrine\Common\Persistence\ObjectManager;
use Tisseo\EndivBundle\Entity\Trip;
use Tisseo\EndivBundle\Entity\LineVersion;

class TripManager
{
    private $om = null;
    private $repository = null;

    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
        $this->repository = $om->getRepository('TisseoEndivBundle:Trip');
    }

    public function findAll()
    {
        return ($this->repository->findAll());
    }

    public function find($tripId)
    {
        return empty($tripId) ? null : $this->repository->find($tripId);
    }

    public function findOne($tripId)
    {
        return empty($tripId) ? null : $this->repository->findOne($tripId);
    }

    public function deleteTrips(LineVersion $lineVersion)
    {
        $query = $this->om->createQuery("
            SELECT t, MIN(ce.startDate) as min_start_date FROM Tisseo\EndivBundle\Entity\Trip t
            JOIN t.route r
            JOIN r.lineVersion lv
            JOIN t.periodCalendar pc
            JOIN pc.calendarElements ce
            WHERE lv.id = ?1
            GROUP BY t.id
            ORDER BY min_start_date DESC
        ");
        $query->setParameter(1, $lineVersion->getId());

        $trips = $query->getResult();

        $flushSize = 100;
        $cpt = 0;

        foreach($trips as $trip)
        {
            if (strnatcmp($trip['min_start_date'], $lineVersion->getEndDate()->format('Y-m-d')) > 0)
            {
                $cpt++;
                foreach ($trip[0]->getStopTimes() as $stopTime)
                    $this->om->remove($stopTime);
                $this->om->remove($trip[0]);

                if ($cpt % $flushSize == 0)
                    $this->om->flush();
            }
            else
                break;
        }
        $this->om->flush();
    }

    public function save(Trip $trip)
    {
        $this->om->persist($trip);
        $this->om->flush();
    }
}
