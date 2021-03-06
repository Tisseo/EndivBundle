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
        return $this->repository->findAll();
    }

    public function find($tripId)
    {
        return empty($tripId) ? null : $this->repository->find($tripId);
    }

    public function findByName($tripName)
    {
        return empty($tripName) ? null : $this->repository->findOneBy(array('name' => $tripName));
    }

    public function findByRoute($routeId)
    {
        return empty($routeId) ? null : $this->repository->findBy(array('route' => $routeId));
    }

    public function getTripTemplates($term, $routeId)
    {
        $specials = array('-', ' ', "'");
        $cleanTerm = str_replace($specials, '_', $term);

        $connection = $this->om->getConnection()->getWrappedConnection();
        $stmt = $connection->prepare('
            SELECT DISTINCT t.id, t.name
            FROM trip t
            WHERE UPPER(unaccent(t.name)) LIKE UPPER(unaccent(:term))
            AND t.route_id = :routeId
            AND t.is_pattern = TRUE
            ORDER BY t.name
        ');
        $stmt->bindValue(':term', '%'.$cleanTerm.'%');
        $stmt->bindValue(':routeId', $routeId);
        $stmt->execute();
        $results = $stmt->fetchAll();

        $array = array();
        foreach ($results as $t) {
            $array[] = array('name' => $t['name'], 'id' => $t['id']);
        }

        return $array;
    }

    public function getTripWithStops($id)
    {
        $queryBuilder = $this->om->createQueryBuilder('t');
        $expr = $queryBuilder->expr();

        $queryBuilder
            ->select('t', 'td', 'st', 'rs', 'w', 's', 'odt', 'sd', 'sa', 'c')
            ->from('Tisseo\EndivBundle\Entity\Trip', 't')
            ->leftJoin('t.tripDatasources', 'td')
            ->leftJoin('t.stopTimes', 'st')
            ->leftJoin('st.routeStop', 'rs')
            ->leftJoin('rs.waypoint', 'w')
            ->leftJoin('w.stop', 's')
            ->leftJoin('s.stopArea', 'sa')
            ->leftJoin('sa.city', 'c')
            ->leftJoin('w.odtArea', 'odt')
            ->leftJoin('s.stopDatasources', 'sd')
            ->where($expr->eq('t.id', ':id'))
            ->setParameter(':id', $id)
        ;

        $result = $queryBuilder->getQuery()->getOneOrNullResult();

        return $result;
    }

    public function hasTrips($id)
    {
        $query = $this->om->createQuery("
             SELECT count(t.parent) FROM Tisseo\EndivBundle\Entity\Trip t
             WHERE t.parent = :id
        ")
        ->setParameter('id', $id);

        $res = $query->getResult();

        return $res[0][1] > 0 ? true : false;
    }

    public function deleteTrip(Trip $trip)
    {
        $this->om->remove($trip);
        $this->om->flush();
    }

    /**
     * Search a trip id into a collection of trips
     *
     * @param $trips collection of trip
     * @param $tripId trip Id
     *
     * @return bool|Trip return false if not found or Trip Entity
     */
    private function isTripIdExist($trips, $tripId)
    {
        if ($trips->count() > 0) {
            $trip = array_filter(
                $trips->toArray(),
                function ($trip) use ($tripId) {
                    if ($trip->getId() === (int) $tripId && $trip instanceof Trip) {
                        return true;
                    }

                    return false;
                }
            );

            return ($trip) ? array_shift($trip) : false;
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

        if (!empty($selectedTrips)) {
            foreach ($selectedTrips as $key => $selectedTripId) {
                $existingTrip = $this->isTripIdExist($trips, $selectedTripId);
                if ($existingTrip) {
                    $this->om->remove($existingTrip);
                }
            }
        } else {
            foreach ($trips as $trip) {
                $this->om->remove($trip);
            }
        }

        $this->om->flush();

        $trips = $route->getTripsNotPattern();

        if (!empty($selectedTrips)) {
            foreach ($selectedTrips as $key => $selectedTripId) {
                $existingTrip = $this->isTripIdExist($trips, $selectedTripId);
                if ($existingTrip) {
                    $this->om->remove($existingTrip);
                }
            }
        } else {
            foreach ($trips as $trip) {
                $this->om->remove($trip);
            }
        }

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

        foreach ($trips as $trip) {
            if (strnatcmp($trip['min_start_date'], $lineVersion->getEndDate()->format('Y-m-d')) > 0) {
                ++$cpt;
                foreach ($trip[0]->getStopTimes() as $stopTime) {
                    $this->om->remove($stopTime);
                }
                $this->om->remove($trip[0]);

                if ($cpt % $flushSize == 0) {
                    $this->om->flush();
                }
            } else {
                break;
            }
        }
        $this->om->flush();
    }

    public function updateComments(array $comments)
    {
        $commentsToDelete = array();

        foreach ($comments as $label => $content) {
            if ($content['comment'] === 'none' || $label === 'none') {
                $trips = $this->repository->findById($content['trips']);

                foreach ($trips as $trip) {
                    if ($trip->getComment() !== null) {
                        $commentsToDelete[] = $trip->getComment()->getId();
                        $trip->setComment(null);
                        $this->om->persist($trip);
                    }
                }
            } else {
                $query = $this->om->createQuery("
                    SELECT c FROM Tisseo\EndivBundle\Entity\Comment c
                    WHERE c.label = ?1
                    AND c.commentText = ?2
                ");
                $query->setParameters(array(1 => $label, 2 => $content['comment']));
                $result = $query->getResult();

                if (empty($result)) {
                    $comment = new Comment($label, $content['comment']);
                } else {
                    $comment = $result[0];
                }

                $trips = $this->repository->findById($content['trips']);
                foreach ($trips as $trip) {
                    $comment->addTrip($trip);
                }

                $this->om->persist($comment);
            }
        }
        $this->om->flush();

        if (count($commentsToDelete) > 0) {
            $commentsToDelete = array_unique($commentsToDelete);
            $query = $this->om->createQuery("
                SELECT c FROM Tisseo\EndivBundle\Entity\Comment c
                WHERE c.id IN (?1)
            ");
            $query->setParameter(1, $commentsToDelete);
            $comments = $query->getResult();

            foreach ($comments as $comment) {
                if ($comment->getTrips()->isEmpty()) {
                    $this->om->remove($comment);
                }
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
     *
     * @param array          $tripPatterns
     * @param Route          $route
     * @param TripDatasource $tripDatasource
     *
     * Creating, updating, deleting Trip entities and their StopTimes/TripDatasources.
     * @usedBy BOABundle
     */
    public function updateTripPatterns($tripPatterns, Route $route, TripDatasource $tripDatasource)
    {
        $sync = false;

        // Checking data first
        foreach ($tripPatterns as $tripPattern) {
            if (empty($tripPattern['name'])) {
                throw new \Exception((empty($tripPattern['id']) ? 'A new trip pattern' : 'Trip pattern with id : '.$tripPattern['id']).' has an empty name');
            }
            foreach ($tripPattern['stopTimes'] as $index => $dataStopTime) {
                if ($index > 0 && $dataStopTime['time'] < 0) {
                    throw new \Exception((empty($dataStopTime['id']) ? 'A new StopTime' : 'StopTime with id: '.$dataStopTime['id']).' has a bad time value : '.$dataStopTime['time']);
                }
            }
        }

        // Deleting Trips
        foreach ($route->getTripsPattern() as $tripPattern) {
            $existing = array_filter(
                $tripPatterns,
                function ($object) use ($tripPattern) {
                    return $object['id'] == $tripPattern->getId();
                }
            );

            if (empty($existing)) {
                if (!$this->patternIsUsed($tripPattern->getId())) {
                    $sync = true;
                    $this->om->remove($tripPattern);
                } else {
                    throw new \Exception("Can't remove trip pattern with id: ".$tripPattern->getId().' because it is used by other trips');
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
                $trip->setIsPattern(true);
                $newTripDatasource = clone $tripDatasource;
                $trip->addTripDatasource($newTripDatasource);

                $this->om->persist($trip);
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
                $time = intval($jsonStopTime['time']) * 60;
                $totalTime += $time;
                $zoneTime = 0;
                if (!empty($jsonStopTime['zoneTime'])) {
                    $zoneTime = intval($jsonStopTime['zoneTime']) * 60;
                }
                $arrivalTime = $totalTime + $zoneTime;
                if (empty($jsonStopTime['id'])) {
                    $sync = true;

                    $stopTime = new StopTime();
                    $stopTime->setRouteStop($routeStops[$key]);
                    $stopTime->setTrip($trip);
                    $stopTime->setDepartureTime($totalTime);
                    $stopTime->setArrivalTime($arrivalTime);

                    $this->om->persist($stopTime);
                } else {
                    $stopTime = $routeStops[$key]->getStopTime($jsonStopTime['id']);
                    if ($stopTime->getDepartureTime() !== $totalTime || $stopTime->getArrivalTime() !== $arrivalTime) {
                        $sync = true;
                        $stopTime->setDepartureTime($totalTime);
                        $stopTime->setArrivalTime($arrivalTime);

                        $this->om->merge($stopTime);
                    }
                }
            }
        }

        if ($sync) {
            $this->om->flush();
        }
    }

    /**
     * Pattern is used
     *
     * @param Trip $tripPattern
     *
     * @return bool
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

        return $result > 0;
    }

    public function getDateBounds($route)
    {
        $connection = $this->om->getConnection()->getWrappedConnection();

        $stmt = $connection->prepare('
            SELECT UNNEST(trips) as id, (bounds).start_date AS start, (bounds).end_date AS end FROM (
                SELECT CASE WHEN t.day_calendar_id IS NOT NULL THEN get_date_bounds_beetween_calendars_optimized(t.period_calendar_id, t.day_calendar_id) ELSE (c.computed_start_date, c.computed_end_date, null, null)::date_pair END as bounds, array_agg(t.id) as trips
                FROM trip t
                JOIN calendar c ON c.id = t.period_calendar_id
                WHERE t.route_id = :routeId
                GROUP BY t.period_calendar_id, t.day_calendar_id, c.computed_start_date, c.computed_end_date
            ) AS trip_date;
        ');
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
     * Get the trip lists for one route
     *
     * @param Route $route
     *
     * @return ArrayCollection
     */
    public function getTripsListForOneRoute(Route $route)
    {
        $query = $this->repository->createQueryBuilder('t')
            ->select('t, st, pc, dc')
            ->where('t.isPattern = false')
            ->andWhere('r.id = :routeId')
            ->join('t.route', 'r')
            ->join('r.routeStops', 'rs', 'with', 'rs.rank = 1')
            ->join('t.stopTimes', 'st', 'with', 'st.routeStop = rs')
            ->join('t.periodCalendar', 'pc')
            ->leftJoin('t.dayCalendar', 'dc')
            ->setParameter('routeId', $route->getId())
            ->getQuery();

        return $query->getResult();
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
        foreach ($stopTimes as $stopTime) {
            if (empty($stopTime['begin'])) {
                throw new \Exception('Start StopTime field is empty');
            }
            if ((empty($stopTime['frequency']) && !empty($stopTime['end'])) ||
                (!empty($stopTime['frequency']) && empty($stopTime['end']))) {
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
            $beginTimings = explode(':', $jsonStopTime['begin']);
            $beginTime = $beginTimings[0] * 3600 + $beginTimings[1] * 60;

            if (empty($jsonStopTime['frequency'])) {
                $newTrip = clone $trip;
                $newTripDatasource = clone $tripDatasource;

                $newTrip->setName($beginTimings[0].$beginTimings[1].'_'.$newTrip->getName());
                $newTrip->addTripDatasource($newTripDatasource);

                $this->createStopTimes($newTrip, $beginTime);
                $this->om->persist($newTrip);
            } else {
                $endTimings = explode(':', $jsonStopTime['end']);
                $endTime = $endTimings[0] * 3600 + $endTimings[1] * 60;

                while ($beginTime <= $endTime) {
                    $hour = (int) ($beginTime / 3600);
                    $minute = (int) (($beginTime - $hour * 3600) / 60);

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

                    $this->om->persist($newTrip);
                    $beginTime += $jsonStopTime['frequency'] * 60;
                }
            }
        }

        $this->om->flush();
    }

    private function createStopTimes(Trip $trip, $time)
    {
        foreach ($trip->getPattern()->getStopTimes() as $stopTime) {
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
