<?php

namespace Tisseo\EndivBundle\Services;

use Doctrine\Common\Persistence\ObjectManager;
use Tisseo\EndivBundle\Entity\Trip;
use Tisseo\EndivBundle\Entity\TripDatasource;
use Tisseo\EndivBundle\Entity\Route;
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
        ->setParameter("id", $id);

        $res = $query->getResult();

        return $res[0][1] > 0 ? true : false;
    }


    public function deleteTrip(Trip $trip) {
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
                    if ($trip->getComment() !== null)
                    {
                        $commentsToDelete[] = $trip->getComment()->getId();
                        $trip->setComment(null);
                        $this->om->persist($trip);
                    }
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

    /**
     * VERIFIED USEFUL FUNCTIONS
     */

    /**
     * Update Trip patterns
     * @param array $tripPatterns
     * @param Route $route
     * @param TripDatasource $tripDatasource
     *
     * Creating, updating, deleting Trip entities and their StopTimes/TripDatasources.
     * @usedBy BOABundle
     */
    public function updateTripPatterns($tripPatterns, Route $route, TripDatasource $tripDatasource)
    {
        $sync = false;

        // Checking data first
        foreach ($tripPatterns as $tripPattern)
        {
            if (empty($tripPattern['name']))
                throw new \Exception((empty($tripPattern['id']) ? "A new trip pattern" : "Trip pattern with id : ".$tripPattern['id'])." has an empty name");

            foreach ($tripPattern['stopTimes'] as $index => $dataStopTime)
            {
                if ($index > 0 && $dataStopTime['time'] < 0)
                    throw new \Exception((empty($dataStopTime['id']) ? "A new StopTime" : "StopTime with id: ".$dataStopTime['id'])." has a bad time value : ".$dataStopTime['time']);
            }

        }

        // Deleting Trips
        foreach ($route->getTripsPattern() as $tripPattern)
        {
            $existing = array_filter(
                $tripPatterns,
                function ($object) use ($tripPattern) {
                    return ($object['id'] == $tripPattern->getId());
                }
            );

            if (empty($existing))
            {
                if (!$this->patternIsUsed($tripPattern->getId()))
                {
                    $sync = true;
                    $this->om->remove($tripPattern);
                }
                else
                    throw new \Exception("Can't remove trip pattern with id: ".$tripPattern->getId()." because it is used by other trips");
            }
        }

        $routeStops = $route->getOrderedRouteStops();

        // Creating/updating Trips
        foreach ($tripPatterns as $tripPattern)
        {
            if (empty($tripPattern['id']))
            {
                $sync = true;

                $trip = new Trip();
                $trip->setName($tripPattern['name']);
                $trip->setRoute($route);
                $trip->setIsPattern(true);
                $newTripDatasource = clone $tripDatasource;
                $trip->addTripDatasource($newTripDatasource);

                $this->om->persist($trip);
            }
            else
                $trip = $route->getTrip($tripPattern['id']);

            $totalTime = 0;
            foreach ($tripPattern['stopTimes'] as $key => $jsonStopTime)
            {
                $time = intVal($jsonStopTime['time'])*60;
                $totalTime += $time;

                if (empty($jsonStopTime['id']))
                {
                    $sync = true;

                    $stopTime = new StopTime();
                    $stopTime->setRouteStop($routeStops[$key]);
                    $stopTime->setTrip($trip);
                    $stopTime->setDepartureTime($totalTime);
                    $stopTime->setArrivalTime($totalTime);

                    $this->om->persist($stopTime);
                }
                else
                {
                    $stopTime = $routeStops[$key]->getStopTime($jsonStopTime['id']);
                    if ($stopTime->getDepartureTime() !== $totalTime)
                    {
                        $sync = true;
                        $stopTime->setDepartureTime($totalTime);
                        $stopTime->setArrivalTime($totalTime);

                        $this->om->merge($stopTime);
                    }
                }
            }
        }

        if ($sync)
            $this->om->flush();
    }

    /**
     * Pattern is used
     * @param Trip $tripPattern
     * @return boolean
     *
     * Checking wether a pattern is linked to trips or not
     */
    public function patternIsUsed($tripPattern)
    {
        $result = $this->repository->createQueryBuilder('t')
            ->select('count(t)')
            ->where('t.pattern = :pattern')
            ->setParameter('pattern', $tripPattern)
            ->getQuery()
            ->getSingleScalarResult()
        ;

        return ($result > 0);
    }

    public function getDateBounds($route)
    {
        $connection = $this->om->getConnection()->getWrappedConnection();
        $stmt = $connection->prepare("
            SELECT id,
                getdateboundsbeetweencalendars(day_calendar_id, period_calendar_id) as bounds
            FROM trip
            WHERE route_id = :routeId
            AND day_calendar_id IS NOT NULL
            AND period_calendar_id IS NOT NULL
        ");
        $stmt->bindValue(':routeId', $route->getId());
        $stmt->execute();
        $datas = $stmt->fetchAll();

        $results = array();
        foreach ($datas as $item)
        {
            $data = explode(",", str_replace(")", "", str_replace("(", "", $item['bounds'])));
            $results[$item['id']] = array('start' => $data[0], 'end' => $data[1]);
        }

        return $results;
    }

    /**
     * Create Trip and StopTimes
     * @param Trip $trip
     * @param array $stopTimes
     * 
     * Creating new Trip entities and their StopTimes using a Trip 'pattern' and its RouteStops.
     */
    public function createTripAndStopTimes(Trip $trip, $stopTimes)
    {
        // Checking data
        if (empty($stopTimes))
            throw new \Exception('StopTimes are empty, please provide at least one row');

        foreach ($stopTimes as $stopTime)
        {
            if (empty($stopTime['begin']))
                throw new \Exception('Start StopTime field is empty');
            if ((empty($stopTime['frequency']) && !empty($stopTime['end'])) ||
                (!empty($stopTime['frequency']) && empty($stopTime['end'])))
                throw new \Exception('Frequency and end fields have to be filled');
        }

        $tripDatasource = clone $trip->getTripDatasources()->first();
        $trip->getTripDatasources()->clear();

        foreach ($stopTimes as $jsonStopTime)
        {
            $beginTimings = explode(":", $jsonStopTime['begin']);
            $beginTime = $beginTimings[0]*3600 + $beginTimings[1]*60;

            if (empty($jsonStopTime['frequency']))
            {
                $newTrip = clone $trip;
                $newTripDatasource = clone $tripDatasource;

                $newTrip->setName($beginTimings[0].$beginTimings[1].'_'.$newTrip->getName());
                $newTrip->addTripDatasource($newTripDatasource);

                $this->createStopTimes($newTrip, $beginTime);
                $this->om->persist($newTrip);
            }
            else
            {
                $endTimings = explode(":", $jsonStopTime['end']);
                $endTime = $endTimings[0]*3600 + $endTimings[1]*60;
        
                while ($beginTime <= $endTime)
                {
                    $hour = (int)($beginTime/3600);
                    $minute = (int)(($beginTime - $hour*3600)/60);
                    if ($hour < 10)
                        $hour = '0'.$hour;
                    if ($minute < 10)
                        $minute = '0'.$minute;

                    $newTrip = clone $trip;
                    $newTripDatasource = clone $tripDatasource;

                    $newTrip->setName($hour.$minute.'_'.$newTrip->getName());
                    $newTrip->addTripDatasource($newTripDatasource);

                    $this->createStopTimes($newTrip, $beginTime);

                    $this->om->persist($newTrip);
                    $beginTime += $stopTime['frequency']*60;
                }
            }
        }

        $this->om->flush();
    }

    private function createStopTimes(Trip $trip, $time)
    {
        foreach ($trip->getPattern()->getStopTimes() as $stopTime)
        {
            $newTime = $time + $stopTime->getDepartureTime();

            $newStopTime = new StopTime();
            $newStopTime->setTrip($trip);
            $newStopTime->setRouteStop($stopTime->getRouteStop());
            $newStopTime->setArrivalTime($newTime);
            $newStopTime->setDepartureTime($newTime);
            
            $trip->addStopTime($newStopTime);
        }
    }
}
