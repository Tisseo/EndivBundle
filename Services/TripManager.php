<?php

namespace Tisseo\EndivBundle\Services;

use Doctrine\Common\Persistence\ObjectManager;
use Tisseo\EndivBundle\Entity\Trip;
use Tisseo\EndivBundle\Entity\LineVersion;
use Tisseo\EndivBundle\Entity\Comment;
use Tisseo\EndivBundle\Entity\StopTime;

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

    public function findByName($tripName)
    {
        return empty($tripName) ? null : $this->repository->findOneBy(array('name'=>$tripName));
    }

    public function findByRoute($routeId)
    {
        return empty($routeId) ? null : $this->repository->findBy(array('route'=>$routeId));
    }

    public function getStopTimes($TripId)
    {
        $query = $this->om->createQuery("
            SELECT st
            FROM Tisseo\EndivBundle\Entity\StopTime st
            WHERE st.trip = :id
            ORDER BY st.arrivalTime
        ")
        ->setParameter("id", $TripId);

        return $query->getResult();
    }

    public function getTripTemplates($term, $routeId)
    {
        $specials = array("-", " ", "'");
        $cleanTerm = str_replace($specials, "_", $term);

        $connection = $this->om->getConnection()->getWrappedConnection();
        $stmt = $connection->prepare("
            SELECT DISTINCT t.id, t.name
            FROM trip t
            WHERE UPPER(unaccent(t.name)) LIKE UPPER(unaccent(:term))
            AND t.route_id = :routeId
            AND t.is_pattern = TRUE
            ORDER BY t.name
        ");
        $stmt->bindValue(':term', '%'.$cleanTerm.'%');
        $stmt->bindValue(':routeId', $routeId);
        $stmt->execute();
        $results = $stmt->fetchAll();
        
        $array = array();
        foreach($results as $t) {
            $array[] = array("name"=>$t["name"], "id"=>$t["id"]);
        }
        
        return $array;
    }

    public function hasTrips($id)
    {
        $query = $this->om->createQuery("
             SELECT count(t.parent) FROM Tisseo\EndivBundle\Entity\Trip t
             WHERE t.parent = :id
        ")
        ->setParameter("id",$id);

        $res = $query->getResult();

      
        return $res[0][1] > 0 ? true : false;
    }


    public function deleteTrip(Trip $trip){

        $this->om->remove($trip);
        $this->om->flush();

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

        foreach ($trips as $trip)
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

        foreach ($comments as $label => $content)
        {
            if ($content['comment'] === "none" || $label === "none")
            {
                $trips = $this->repository->findById($content["trips"]);

                foreach ($trips as $trip)
                {
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
                    AND c.commentText = ?2
                ");
                $query->setParameters(array(1 => $label, 2 => $content['comment']));
                $result = $query->getResult();

                if (empty($result))
                    $comment = new Comment($label, $content["comment"]);
                else
                    $comment = $result[0];

                $trips = $this->repository->findById($content["trips"]);
                foreach ($trips as $trip)
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

            foreach ($comments as $comment)
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

    public function remove(Trip $trip)
    {
        $this->om->remove($trip);
        $this->om->flush();
    }

    private function populateStopTimes($trip, $time)
    {
        foreach ($trip->getPattern()->getStopTimes() as $stp) {
            if( $stp->getRouteStop()->getScheduledStop() ) {
                if( empty($lastScheduledTime) ) {
                    //first stop
                    $lastScheduledTime = $time;
                } else {
                    $lastScheduledTime += $stp->getArrivalTime();
                }
                $time = $lastScheduledTime;
            } else {
                $time +=  $stp->getArrivalTime();
            }
            $new_stop_time = new StopTime();
            $new_stop_time->setTrip($trip);
            $new_stop_time->setRouteStop($stp->getRouteStop());
            $new_stop_time->setArrivalTime($time);
            $new_stop_time->setDepartureTime($time);
            $trip->addStopTime($new_stop_time);
        }
    }

    public function createTripAndStopTimes(Trip $trip, $stop_times, $route, $isPattern = false)
    {
        $trip->setRoute($route);
        $trip->setIsPattern($isPattern);

        $uniqueService = false;
        if( count($stop_times) == 1 ) {
            $uniqueService = ( empty($stop_times[0]['frequency']) || empty($stop_times[0]['stop']) );
        }
        
        $st_patterns = $trip->getPattern()->getStopTimes();
        foreach ($stop_times as $st) {
            $time_array = explode(":", $st['start']);
            $time = $time_array[0]*3600 + $time_array[1]*60;

            if( empty($st['frequency']) || empty($st['stop']) ) {
                $newTrip = clone $trip;
                if(!$uniqueService) {
                    $h = (int)($time/3600);
                    $m = (int)(( $time - $h*3600 )/60);
                    if( $h < 10 ) $h = '0'.$h;
                    if( $m < 10 ) $m = '0'.$m;
                    $newTrip->setName($h.$m.' '.$newTrip->getName());
                }
                $this->populateStopTimes($newTrip, $time);
                $this->save($newTrip);
            } else {
                //loop
                $time_array = explode(":", $st['stop']);
                $last_time = $time_array[0]*3600 + $time_array[1]*60;
                while ( $time <= $last_time ) {
                    $h = (int)($time/3600);
                    $m = (int)(( $time - $h*3600 )/60);
                    if( $h < 10 ) $h = '0'.$h;
                    if( $m < 10 ) $m = '0'.$m;

                    $newTrip = clone $trip;
                    $newTrip->setName($h.$m.' '.$newTrip->getName());
                    $this->populateStopTimes($newTrip, $time);
                    $this->save($newTrip);

                    $time += $st['frequency']*60;
                }
            }
        }
    }    
}
