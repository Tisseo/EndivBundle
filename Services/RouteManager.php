<?php
/**
 * Created by PhpStorm.
 * User: clesauln
 * Date: 09/04/2015
 * Time: 11:15
 */

namespace Tisseo\EndivBundle\Services;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Query\QueryBuilder;

use Tisseo\EndivBundle\Entity\Route;
use Tisseo\EndivBundle\Entity\RouteStop;
use Tisseo\EndivBundle\Entity\Trip;
use Tisseo\EndivBundle\Entity\StopTime;
use Tisseo\EndivBundle\Entity\GridMaskType;
use Tisseo\EndivBundle\Entity\TripCalendar;
use Tisseo\EndivBundle\Entity\TripDatasource;
use Tisseo\EndivBundle\Entity\RouteDatasource;

class RouteManager extends SortManager
{
    private $om= null;
    private $repository= null;

    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
        $this->repository = $om->getRepository("TisseoEndivBundle:Route");
    }

    public function findAll()
    {
        return $this->repository->findAll();
    }

    public function find($routeId)
    {
        return empty($routeId) ? null : $this->repository->find($routeId);
    }

    public function save(Route $route)
    {

        $this->om->persist($route);
        $this->om->flush();
    }

    public function remove($routeId)
    {
        $route = $this->find($routeId);

        if (empty($route))
            throw new \Exception("Can't find the route with ID: ".$routeId);

        $trips = $route->getTripsNotPattern();

        // TODO: Later, condition is if ACTIVE (calendar_start_date > now > calendar_end_date) trips found, can't delete
        if ($trips->count() > 0)
            throw new \Exception("Can't delete this route because it has ".$trips." trips.");

        $lineVersionId = $route->getLineVersion()->getId();

        $this->om->remove($route);
        $this->om->flush();

        return $lineVersionId;
    }

    private function updateRouteSections($route_stops, $route)
    {
        $route_sections = $this->om
                            ->getRepository('TisseoEndivBundle:RouteSection')
                            ->findAll();
        $tomorrow = (new \DateTime('tomorrow'))->format('Ymd');

        for ($i=0; $i < count($route_stops); $i++) {
            $rs = $route_stops[$i];

            if( $i+1 < count($route_stops) ) {
                $startStop = $rs->getWaypoint()->getStop();
                $endStop = $route_stops[$i+1]->getWaypoint()->getStop();

                $filtered_route_sections = array_filter($route_sections,
                    function($rs_filter)
                    use ($startStop, $endStop, $tomorrow) {
                        if( $rs_filter->getStartStop() != $startStop ) return false;
                        if( $rs_filter->getEndStop() != $endStop ) return false;
                        if( $rs_filter->getStartDate()->format('Ymd') > $tomorrow ) return false;
                        if( !$rs_filter->getEndDate() ) {
                            return true;
                        } else {
                            if( $rs_filter->getEndDate()->format('Ymd') <= $tomorrow ) return false;
                        }
                        return true;
                    }
                );
                if(count($filtered_route_sections) > 0) {
                    $route_section = reset($filtered_route_sections);
                    $rs->setRouteSection($route_section);
                    $this->om->persist($rs);
                }
            }
        }
    }

    private function getRouteSectionLength($routeSectionId)
    {
        $connection = $this->om->getConnection()->getWrappedConnection();
        $stmt = $connection->prepare("
            select ST_Length(the_geom) from route_section where id = :rsId::int
        ");
        $stmt->bindValue(':rsId', $routeSectionId, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    private function getNotScheduledStopRatios($route_stops)
    {
        $notScheduledStops = array();
        $tmp = array();
        for ($i=0; $i < count($route_stops); $i++) {
            $rs = $route_stops[$i];
            if( $rs->getRouteSection() )
                $rsLength = $this->getRouteSectionLength($rs->getRouteSection()->getId());
            else
                $rsLength = 0;
            if($rs->getScheduledStop()) {
                if(count($tmp) > 0) {
                    foreach ($tmp as $key => $value) {
                        $tmp[$key]['total'] = $total;
                        if( $tmp[$key]['total'] == 0 )
                            $tmp[$key]['ratio'] = 0;
                        else
                            $tmp[$key]['ratio'] = $tmp[$key]['length'] / $tmp[$key]['total'];
                        unset($tmp[$key]['total']);
                        unset($tmp[$key]['length']);
                        $notScheduledStops[$key] = $tmp[$key];
                    }
                    $tmp = array();
                }
                $total = $rsLength;
            } else {
                $total += $rsLength;
                $tmp[$rs->getId()] = array('length' => $rsLength);
            }
        }

        return $notScheduledStops;
    }

    private function updateNotScheduledStopTimes($trips, $ratios)
    {
        $notScheduledStopTimes = array();

        foreach ($trips as $trip) {
            foreach ($trip->getStopTimes() as $st) {
                if( array_key_exists ( $st->getRouteStop()->getId() , $ratios ) ) {
                    $notScheduledStopTimes[] = $st;
                } else {
                    if( count($notScheduledStopTimes) > 0 ) {
                        foreach ($notScheduledStopTimes as $notScheduledST) {
                            $totalTime = $st->getArrivalTime();

                            $time = (int)($ratios[$notScheduledST->getRouteStop()->getId()]['ratio'] * $totalTime);
                            $time = $time - ($time % 60);
                            $notScheduledST->setArrivalTime($time);
                            $notScheduledST->setDepartureTime($time);
                            $this->om->persist($notScheduledST);
                        }
                    }

                    $notScheduledStopTimes = array();
                }
            }
        }
    }

    public function getInstantiatedServiceTemplates($route)
    {
        $query = $this->om->createQuery("
            SELECT DISTINCT t1.id
            FROM Tisseo\EndivBundle\Entity\Trip t
            JOIN Tisseo\EndivBundle\Entity\Trip t1
            WITH t.pattern = t1
            WHERE t1.route = :route
        ")
        ->setParameter("route", $route);
        //convert associative array of in to array of strings
        $tmp = array_map('current', $query->getArrayResult());
        return array_map('strval', $tmp);
    }

    /**
     * Get Timetable Calendars
     * @param integer lineVersionId
     *
     * Creating an array with grouped Trips by Route with their calendar/trip_calendar.
     */
    public function getTimetableCalendars($lineVersionId)
    {
        $routes = $this->repository->findBy(array('lineVersion' => $lineVersionId));

        $result = array();
        foreach ($routes as $route)
        {
            $way = $route->getWay();
            if (!array_key_exists($way, $result))
                $result[$way] = array();

            foreach ($route->getTripsNotPatternWithCalendars() as $trip)
            {
                $calendarKey = $trip->getDayCalendar()->getName().'_'.$trip->getPeriodCalendar()->getName();

                if (!array_key_exists($calendarKey, $result[$way]))
                {
                    $result[$way][$calendarKey]['dayCalendar'] = $trip->getDayCalendar();
                    $result[$way][$calendarKey]['periodCalendar'] = $trip->getPeriodCalendar();
                    $result[$way][$calendarKey]['route'] = $route->getId();
                }

                $result[$way][$calendarKey]['trips'][] = $trip;

                if ($trip->getTripCalendar() && !array_key_exists('tripCalendar', $result[$way][$calendarKey]))
                    $result[$way][$calendarKey]['tripCalendar'] = $trip->getTripCalendar();
            }
        }

        return $result;
    }

    public function getSortedTypesOfGridMaskType()
    {
        $result = array("Semaine", "Samedi", "Dimanche");
        $query = $this->om->createQuery("
            SELECT DISTINCT g.calendarType
            FROM Tisseo\EndivBundle\Entity\GridMaskType g
            WHERE g.calendarType NOT IN (:type)
        ")
        ->setParameter("type", $result);

        foreach ($query->getResult() as $value) {
            $result[] = $value['calendarType'];
        }
        return $result;
    }

    public function getSortedPeriodsOfGridMaskType()
    {
        $result = array("Base", "Vacances", "Ete");
        $query = $this->om->createQuery("
            SELECT DISTINCT g.calendarPeriod
            FROM Tisseo\EndivBundle\Entity\GridMaskType g
            WHERE g.calendarPeriod NOT IN (:period)
        ")
        ->setParameter("period", $result);

        foreach ($query->getResult() as $value) {
            $result[] = $value['calendarPeriod'];
        }
        return $result;
    }

    /**
     * Link TripCalendars
     * @param array $datas
     *
     * Linking existing TripCalendars to Trip entities.
     * Creating new TripCalendar/GridMaskType if needed.
     */
    public function linkTripCalendars($datas)
    {
        $sync = false;
        foreach ($datas as $data)
        {
            $tripCalendar = null;

            $gridMaskType = $this->om->createQuery("
                SELECT gmt FROM Tisseo\EndivBundle\Entity\GridMaskType gmt
                WHERE gmt.calendarPeriod = :period
                AND gmt.calendarType = :type
            ")
             ->setParameter("period", $data['calendarPeriod'])
             ->setParameter("type", $data['calendarType'])
             ->getOneOrNullResult();

            if (!empty($gridMaskType))
            {
                $pattern = implode(array_values($data['days']));

                // Doctrine CAST(x, y) is transformed into (x || y). x and y have to be varchars.
                $tripCalendar = $this->om->createQuery("
                    SELECT tc FROM Tisseo\EndivBundle\Entity\TripCalendar tc
                    WHERE tc.gridMaskType = :gridMaskType
                    AND CONCAT(CAST(CAST(tc.monday AS INTEGER) AS CHAR), CAST(CAST(tc.tuesday AS INTEGER) AS  CHAR), CAST(CAST(tc.wednesday AS INTEGER) AS CHAR),
                               CAST(CAST(tc.thursday AS INTEGER) AS CHAR), CAST(CAST(tc.friday AS INTEGER) AS CHAR), CAST(CAST(tc.saturday AS INTEGER) AS CHAR),
                               CAST(CAST(tc.sunday AS INTEGER) AS CHAR)) = :pattern
                ")
                ->setParameter("gridMaskType", $gridMaskType)
                ->setParameter("pattern", $pattern)
                ->getOneOrNullResult();

                if (!empty($tripCalendar) && !empty($data['tripCalendar']))
                {
                    $oldTripCalendar = $this->om->createQuery("
                        SELECT tc FROM Tisseo\EndivBundle\Entity\TripCalendar tc
                        WHERE tc.id = :tripCalendar
                    ")
                    ->setParameter("tripCalendar", $data['tripCalendar'])
                    ->getOneOrNullResult();

                    if (!empty($oldTripCalendar) && $oldTripCalendar->getId() === $tripCalendar->getId())
                        continue;
                }
            }

            if (empty($gridMaskType))
            {
                $gridMaskType = new GridMaskType();
                $gridMaskType->setCalendarType($data['calendarType']);
                $gridMaskType->setCalendarPeriod($data['calendarPeriod']);
            }

            if (empty($tripCalendar))
            {
                $tripCalendar = new TripCalendar();
                $tripCalendar->setGridMaskType($gridMaskType);

                //TODO: Find something better (for loop over attributes using an array ?)
                $tripCalendar->setMonday(filter_var($data['days'][1], FILTER_VALIDATE_BOOLEAN));
                $tripCalendar->setTuesday(filter_var($data['days'][2], FILTER_VALIDATE_BOOLEAN));
                $tripCalendar->setWednesday(filter_var($data['days'][3], FILTER_VALIDATE_BOOLEAN));
                $tripCalendar->setThursday(filter_var($data['days'][4], FILTER_VALIDATE_BOOLEAN));
                $tripCalendar->setFriday(filter_var($data['days'][5], FILTER_VALIDATE_BOOLEAN));
                $tripCalendar->setSaturday(filter_var($data['days'][6], FILTER_VALIDATE_BOOLEAN));
                $tripCalendar->setSunday(filter_var($data['days'][7], FILTER_VALIDATE_BOOLEAN));

                $this->om->persist($tripCalendar);
            }
            
            $tripIds = array_values($data['trips']);

            $trips = $this->om->createQuery("
                SELECT t FROM Tisseo\EndivBundle\Entity\Trip t
                WHERE t.id IN(:trips)
            ")
            ->setParameter("trips", $tripIds)
            ->getResult();

            foreach ($trips as $trip)
            {
                $trip->setTripCalendar($tripCalendar);
                $this->om->persist($trip);
            }
            $sync = true;
        }

        if ($sync)
            $this->om->flush();
    }

    // TODO: CHANGE THIS
    public function duplicate($route, $lineVersion, $userName)
    {
        $query = $this->om->createQuery("
            SELECT ds FROM Tisseo\EndivBundle\Entity\Datasource ds
            WHERE ds.name = ?1
        ")->setParameter(1, "Service Données");

        $datasource = $query->getOneOrNullResult();

        $newRoute = new Route();
        $newRoute->setWay($route->getWay());
        $newRoute->setName($route->getName()." (Copie)");
        $newRoute->setDirection($route->getDirection());
        if( $route->getComment() ) {
            $newRoute->setComment($route->getComment());
        }
        $newRoute->setLineVersion($lineVersion);

        $route_stops = array();
        foreach ($route->getRouteStops() as $rs) {
            $newRS = new RouteStop();
            $newRS->setRank($rs->getRank());
            $newRS->setScheduledStop($rs->getScheduledStop());
            $newRS->setPickup($rs->getPickup());
            $newRS->setDropOff($rs->getDropOff());
            $newRS->setReservationRequired($rs->getReservationRequired());
            $newRS->setInternalService($rs->getInternalService());
            $newRS->setRoute($newRoute);
            $newRS->setRouteSection($rs->getRouteSection());
            $newRS->setWaypoint($rs->getWaypoint());
            $this->om->persist($newRS);

            $route_stops[$rs->getId()] = $newRS;
        }

        $services_patterns = $route->getTrips()->filter( function($t) {
            return $t->getIsPattern() == true;
        });

        foreach ($services_patterns as $t) {
            $newTrip = new Trip();
            $newTrip->setName($t->getName());
            $newTrip->setIsPattern($t->getIsPattern());
            $newTrip->setPattern($t->getPattern());
            $newTrip->setComment($t->getComment());
            $newTrip->setRoute($newRoute);
            $newTrip->setTripCalendar($t->getTripCalendar());
            $newTrip->setPeriodCalendar($t->getPeriodCalendar());
            $newTrip->setDayCalendar($t->getDayCalendar());
            $newTrip->setParent($t->getParent());

            foreach ($t->getStopTimes() as $st) {
                $newST = new StopTime();
                $newST->setArrivalTime($st->getArrivalTime());
                $newST->setDepartureTime($st->getDepartureTime());

                $newST->setRouteStop( $route_stops[ $st->getRouteStop()->getId() ] );
                $newST->setTrip($newTrip);
                $newTrip->addStopTime($newST);
                $this->om->persist($newST);
            }

            if (!empty($datasource)) {
                $tripDatasource = new TripDatasource();
                $tripDatasource->setDatasource($datasource);
                $tripDatasource->setTrip($newTrip);
                $tripDatasource->setCode($userName);
                $this->om->persist($tripDatasource);
                $newTrip->addTripDatasources($tripDatasource);
            }
            $this->om->persist($newTrip);
        }

        if (!empty($datasource)) {
            $routeDatasource = new RouteDatasource();
            $routeDatasource->setDatasource($datasource);
            $routeDatasource->setRoute($newRoute);
            $routeDatasource->setCode($userName);
            $this->om->persist($routeDatasource);
            $newRoute->addRouteDatasource($routeDatasource);
        }
        $this->om->persist($newRoute);
        $this->om->flush();
    }
}
