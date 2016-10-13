<?php

namespace Tisseo\EndivBundle\Services;

use Doctrine\Common\Collections\Collection;
use Tisseo\EndivBundle\Entity\Trip;
use Tisseo\EndivBundle\Entity\TripDatasource;
use Tisseo\EndivBundle\Entity\Route;
use Tisseo\EndivBundle\Entity\Comment;
use Tisseo\EndivBundle\Entity\StopTime;

class TripManager extends AbstractManager
{
    public function findTripTemplatesLike($term, $routeId)
    {
        $query = $this->createLikeQueryBuilder(array('name'), $term, true)
            ->select('distinct o.id, o.name')
            ->andWhere('o.route = :routeId')
            ->andWhere('o.pattern = true')
            ->orderBy('o.name')
            ->addParameter('routeId', $routeId)
            ->getQuery();

        $results = $query->getResult();

        $array = array();
        foreach ($results as $t) {
            $array[] = array("name"=>$t["name"], "id"=>$t["id"]);
        }

        return $array;
    }

    /**
     * Search a trip id into a collection of trips
     *
     * @param  $trips collection of trip
     * @param  $tripId trip Id
     * @return bool|Trip return false if not found or Trip Entity
     */
    private function isTripIdExist(Collection $trips, $tripId)
    {
        if ($trips->count() > 0) {
            $trip = array_filter(
                $trips->toArray(),
                function ($trip) use ($tripId) {
                    if ($trip->getId() === (int)$tripId && $trip instanceof Trip) {
                        return true;
                    }
                    return false;
                }
            );
            return ($trip) ? array_shift($trip): false;
        } else {
            return false;
        }
    }

    /**
     * Delete all or selected trips from a route
     *
     * @param Route $route         Route entity
     * @param array $selectedTrips list of trips selected to be delete
     */
    public function deleteTripsFromRoute(Route $route, array $selectedTrips = array())
    {
        $trips = $route->getTripsHavingParent();
        $objectManager = $this->getObjectManager();

        if (!empty($selectedTrips)) {
            // replace by criteria
            foreach ($selectedTrips as $key => $selectedTripId) {
                $existingTrip = $this->isTripIdExist($trips, $selectedTripId);
                if ($existingTrip) {
                    $objectManager->remove($existingTrip);
                }
            }
        } else {
            foreach ($trips as $trip) {
                $objectManager->remove($trip);
            }
        }

        $objectManager->flush();

        $trips = $route->getTripsNotPattern();

        if (!empty($selectedTrips)) {
            foreach ($selectedTrips as $key => $selectedTripId) {
                $existingTrip = $this->isTripIdExist($trips, $selectedTripId);
                if ($existingTrip) {
                    $objectManager->remove($existingTrip);
                }
            }
        } else {
            foreach ($trips as $trip) {
                $objectManager->remove($trip);
            }
        }

        $objectManager->flush();
    }

    public function updateComments(array $comments)
    {
        $commentsToDelete = array();

        foreach ($comments as $label => $content) {
            if ($content['comment'] === "none" || $label === "none") {
                $trips = $this->getRepository()->findById($content["trips"]);

                foreach ($trips as $trip) {
                    if ($trip->getComment() !== null) {
                        $commentsToDelete[] = $trip->getComment()->getId();
                        $trip->setComment(null);
                        $objectManager->persist($trip);
                    }
                }
            } else {
                $query = $objectManager->createQuery(
                    "
                    SELECT c FROM Tisseo\EndivBundle\Entity\Comment c
                    WHERE c.label = ?1
                    AND c.commentText = ?2
                "
                );
                $query->setParameters(array(1 => $label, 2 => $content['comment']));
                $result = $query->getResult();

                if (empty($result)) {
                    $comment = new Comment($label, $content["comment"]);
                } else {
                    $comment = $result[0];
                }

                $trips = $this->getRepository()->findById($content["trips"]);
                foreach ($trips as $trip) {
                    $comment->addTrip($trip);
                }

                $objectManager->persist($comment);
            }
        }
        $objectManager->flush();

        if (count($commentsToDelete) > 0) {
            $commentsToDelete = array_unique($commentsToDelete);
            $query = $objectManager->createQuery(
                "
                SELECT c FROM Tisseo\EndivBundle\Entity\Comment c
                WHERE c.id IN (?1)
            "
            );
            $query->setParameter(1, $commentsToDelete);
            $comments = $query->getResult();

            foreach ($comments as $comment) {
                if ($comment->getTrips()->isEmpty()) {
                    $objectManager->remove($comment);
                }
            }
            $objectManager->flush();
        }
    }

    /**
     * Update Trip patterns
     *
     * @param  array          $tripPatterns
     * @param  Route          $route
     * @param  TripDatasource $tripDatasource
     *
     * Creating, updating, deleting Trip entities and their StopTimes/TripDatasources.
     * @usedBy BOABundle
     */
    public function updateTripPatterns($tripPatterns, Route $route, TripDatasource $tripDatasource)
    {
        $sync = false;
        $objectManager = $this->getObjectManager();

        // Checking data first
        foreach ($tripPatterns as $tripPattern) {
            if (empty($tripPattern['name'])) {
                throw new \Exception((empty($tripPattern['id']) ? "A new trip pattern" : "Trip pattern with id : ".$tripPattern['id'])." has an empty name");
            }

            foreach ($tripPattern['stopTimes'] as $index => $dataStopTime) {
                if ($index > 0 && $dataStopTime['time'] < 0) {
                    throw new \Exception((empty($dataStopTime['id']) ? "A new StopTime" : "StopTime with id: ".$dataStopTime['id'])." has a bad time value : ".$dataStopTime['time']);
                }
            }
        }

        // Deleting Trips
        foreach ($route->getTripsPattern() as $tripPattern) {
            $existing = array_filter(
                $tripPatterns,
                function ($object) use ($tripPattern) {
                    return ($object['id'] == $tripPattern->getId());
                }
            );

            if (empty($existing)) {
                if (!$this->patternIsUsed($tripPattern->getId())) {
                    $sync = true;
                    $objectManager->remove($tripPattern);
                } else {
                    throw new \Exception("Can't remove trip pattern with id: ".$tripPattern->getId()." because it is used by other trips");
                }
            }
        }

        $routeStops = $route->getRouteStops();

        // Creating/updating Trips
        foreach ($tripPatterns as $tripPattern) {
            // new trip
            if (empty($tripPattern['id'])) {
                $sync = true;

                $trip = new Trip();
                $trip->setName($tripPattern['name']);
                $trip->setRoute($route);
                $trip->setPattern(true);
                $newTripDatasource = clone $tripDatasource;
                $trip->addTripDatasource($newTripDatasource);

                $objectManager->persist($trip);
            } else {
                $trip = $route->getTrip($tripPattern['id']);
                // updating trip name if different
                if ($trip->getName() != $tripPattern['name']) {
                    $sync = true;
                    $trip->setName($tripPattern['name']);
                }
            }

            $totalTime = 0;
            foreach ($tripPattern['stopTimes'] as $key => $jsonStopTime) {
                $time = intVal($jsonStopTime['time'])*60;
                $totalTime += $time;
                $zoneTime = 0;
                if (!empty($jsonStopTime['zoneTime'])) {
                    $zoneTime = intVal($jsonStopTime['zoneTime'])*60;
                }
                $arrivalTime = $totalTime + $zoneTime;
                if (empty($jsonStopTime['id'])) {
                    $sync = true;

                    $stopTime = new StopTime();
                    $stopTime->setRouteStop($routeStops[$key]);
                    $stopTime->setTrip($trip);
                    $stopTime->setDepartureTime($totalTime);
                    $stopTime->setArrivalTime($arrivalTime);

                    $objectManager->persist($stopTime);
                } else {
                    $stopTime = $routeStops[$key]->getStopTime($jsonStopTime['id']);
                    if ($stopTime->getDepartureTime() !== $totalTime || $stopTime->getArrivalTime() !== $arrivalTime) {
                        $sync = true;
                        $stopTime->setDepartureTime($totalTime);
                        $stopTime->setArrivalTime($arrivalTime);

                        $objectManager->merge($stopTime);
                    }
                }
            }
        }

        if ($sync) {
            $objectManager->flush();
        }
    }

    /**
     * Pattern is used
     *
     * @param  Trip $tripPattern
     * @return boolean
     *
     * Checking wether a pattern is linked to trips or not
     */
    public function patternIsUsed($tripPattern)
    {
        $result = $this->getRepository()->createQueryBuilder('t')
            ->select('count(t)')
            ->where('t.pattern = :pattern')
            ->setParameter('pattern', $tripPattern)
            ->getQuery()
            ->getSingleScalarResult();

        return ($result > 0);
    }

    public function getDateBounds(Route $route)
    {
        $connection = $this->getObjectManager()->getConnection()->getWrappedConnection();

        $stmt = $connection->prepare(
            "
            SELECT UNNEST(trips) as id, (bounds).start_date AS start, (bounds).end_date AS end FROM (
                SELECT CASE WHEN t.day_calendar_id IS NOT NULL THEN getdateboundsbeetweencalendars(t.period_calendar_id, t.day_calendar_id) ELSE (c.computed_start_date, c.computed_end_date, null, null)::date_pair END as bounds, array_agg(t.id) as trips
                FROM trip t
                JOIN calendar c ON c.id = t.period_calendar_id
                WHERE t.route_id = :routeId
                GROUP BY t.period_calendar_id, t.day_calendar_id, c.computed_start_date, c.computed_end_date
            ) AS trip_date;
        "
        );
        $stmt->bindValue(':routeId', $route->getId());
        $stmt->execute();
        $datas = $stmt->fetchAll();

        $result = array();
        foreach ($datas as $data) {
            $result[$data['id']] = $data;
        }

        return $result;
    }

    /**
     * Create Trip and StopTimes
     *
     * @param Trip  $trip
     * @param array $stopTimes
     *
     * Creating new Trip entities and their StopTimes using a Trip 'pattern' and its RouteStops.
     */
    public function createTripAndStopTimes(Trip $trip, $stopTimes)
    {
        // Checking data
        if (empty($stopTimes)) {
            throw new \Exception('StopTimes are empty, please provide at least one row');
        }

        $objectManager = $this->getObjectManager();

        foreach ($stopTimes as $stopTime) {
            if (empty($stopTime['begin'])) {
                throw new \Exception('Start StopTime field is empty');
            }
            if ((empty($stopTime['frequency']) && !empty($stopTime['end']))
                || (!empty($stopTime['frequency']) && empty($stopTime['end']))
            ) {
                throw new \Exception('Frequency and end fields have to be filled');
            }
        }

        $tripDatasource = clone $trip->getTripDatasources()->first();
        $trip->getTripDatasources()->clear();

        $tripCalendar = $trip->getRoute()->getAvailableTripCalendar($trip);
        if ($tripCalendar !== null) {
            $trip->setTripCalendar($tripCalendar);
        }

        foreach ($stopTimes as $jsonStopTime) {
            $beginTimings = explode(":", $jsonStopTime['begin']);
            $beginTime = $beginTimings[0]*3600 + $beginTimings[1]*60;

            if (empty($jsonStopTime['frequency'])) {
                $newTrip = clone $trip;
                $newTripDatasource = clone $tripDatasource;

                $newTrip->setName($beginTimings[0].$beginTimings[1].'_'.$newTrip->getName());
                $newTrip->addTripDatasource($newTripDatasource);

                $this->createStopTimes($newTrip, $beginTime);
                $objectManager->persist($newTrip);
            } else {
                $endTimings = explode(":", $jsonStopTime['end']);
                $endTime = $endTimings[0]*3600 + $endTimings[1]*60;

                while ($beginTime <= $endTime) {
                    $hour = (int)($beginTime/3600);
                    $minute = (int)(($beginTime - $hour*3600)/60);

                    if ($hour < 10) {
                        $hour = '0'.$hour;
                    }
                    if ($minute < 10) {
                        $minute = '0'.$minute;
                    }

                    $newTrip = clone $trip;
                    $newTripDatasource = clone $tripDatasource;

                    $newTrip->setName($hour.$minute.'_'.$newTrip->getName());
                    $newTrip->addTripDatasource($newTripDatasource);

                    $this->createStopTimes($newTrip, $beginTime);

                    $objectManager->persist($newTrip);
                    $beginTime += $jsonStopTime['frequency']*60;
                }
            }
        }

        $objectManager->flush();
    }

    private function createStopTimes(Trip $trip, $time)
    {
        foreach ($trip->getTripPattern()->getStopTimes() as $stopTime) {
            $newDepartureTime = $time + $stopTime->getDepartureTime();
            $newArrivalTime = $time + $stopTime->getArrivalTime();
            $newStopTime = new StopTime();
            $newStopTime->setTrip($trip);
            $newStopTime->setRouteStop($stopTime->getRouteStop());
            $newStopTime->setArrivalTime($newArrivalTime);
            $newStopTime->setDepartureTime($newDepartureTime);

            $trip->addStopTime($newStopTime);
        }
    }
}
