<?php

namespace Tisseo\EndivBundle\Services;

use Doctrine\Common\Persistence\ObjectManager;
use Tisseo\EndivBundle\Entity\Trip;
use Tisseo\EndivBundle\Entity\LineVersion;
use Tisseo\EndivBundle\Entity\Comment;

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

    public function updateComments(array $comments)
    {
        $commentsToDelete = array();

        foreach($comments as $label => $content)
        {
            if ($content['comment'] === "none" || $label === "none")
            {
                $trips = $this->repository->findById($content["trips"]);

                foreach($trips as $trip) {
                    $commentsToDelete[] = $trip->getComment()->getId();
                    $trip->setComment(null);
                    $this->om->persist($trip);
                }
            }
            else
            {
                $query = $this->om->createQuery("
                    SELECT c FROM Tisseo\EndivBundle\Entity\Comment c
                    WHERE c.label = ?1
                ");
                $query->setParameter(1, $label);
                $result = $query->getResult();

                if (empty($result))
                    $comment = new Comment($label, $content["comment"]);
                else
                    $comment = $result[0];

                $trips = $this->repository->findById($content["trips"]);
                foreach($trips as $trip)
                    $comment->addTrip($trip);
            
                $this->om->persist($comment);
            }
        }
        $this->om->flush();

        if (count($commentsToDelete) > 0)
        {
            $commentsToDelete = array_unique($commentsToDelete);
            $query = $this->om->createQuery("
                SELECT c FROM Tisseo\EndivBundle\Entity\Comment c
                WHERE c.id IN (?1)
            ");
            $query->setParameter(1, $commentsToDelete);
            $comments = $query->getResult();

            foreach($comments as $comment)
            {
                if ($comment->getTrips()->isEmpty())
                    $this->om->remove($comment);
            }
            $this->om->flush();
        }
    }

    public function save(Trip $trip)
    {
        $this->om->persist($trip);
        $this->om->flush();
    }
}
