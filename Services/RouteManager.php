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

    /*
    return an array:
    [route.way]
    |_[trip.day_calendar.name#trip.period_calendar.name]
      |_['calendars']
      |  |_['day']
      |  | |_trip.day_calendar
      |  |_['period']
      |    |_trip.period_calendar
      |_['trips'] -> trip.isPattern = FALSE
      | |_trip1
      | |_trip2...
      |_['objects']
        |_[grid_mask_type]
        | |_id
        |_[trip_calendar]
          |_id
    */
    public function getTimetableCalendars($lineVersionId)
    {
        $query = $this->om->createQuery("
            SELECT t
            FROM Tisseo\EndivBundle\Entity\Trip t
            JOIN t.route r
            JOIN r.lineVersion lv
            WHERE lv.id = :id
            AND t.isPattern = false
            ORDER BY t.route
        ")
        ->setParameter("id", $lineVersionId);

        $trips = $query->getResult();
        $result = array();

        foreach ($trips as $t)
        {
            $way = $t->getRoute()->getWay();
            if (!array_key_exists($way, $result))
                $result[$way] = array();

            if ($t->getDayCalendar() && $t->getPeriodCalendar())
            {
                $calendarKey = $t->getDayCalendar()->getName().'#'.$t->getPeriodCalendar()->getName();

                if (!array_key_exists($calendarKey, $result[$way]))
                {
                    $result[$way][$calendarKey] = array();
                    $result[$way][$calendarKey]['calendars'] = array();
                    $result[$way][$calendarKey]['calendars']['day'] = $t->getDayCalendar();
                    $result[$way][$calendarKey]['calendars']['period'] = $t->getPeriodCalendar();
                    $result[$way][$calendarKey]['trips'] = array();
                    $result[$way][$calendarKey]['objects'] = array();
                }

                $result[$way][$calendarKey]['trips'][] = $t;

                if ($t->getTripCalendar())
                {
                    if (!array_key_exists('grid_mask_type', $result[$way][$calendarKey]['objects']))
                        $result[$way][$calendarKey]['objects']['grid_mask_type'] = $t->getTripCalendar()->getGridMaskType();
                    $result[$way][$calendarKey]['objects']['trip_calendar'] = $t->getTripCalendar();
                }
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

    private function gridIsEmpty($grid)
    {
        return (
            empty($grid["grid_calendar_type"]) &&
            empty($grid["grid_calendar_period"]) &&
            empty($grid["monday"]) &&
            empty($grid["tuesday"]) &&
            empty($grid["wednesday"]) &&
            empty($grid["thursday"]) &&
            empty($grid["friday"]) &&
            empty($grid["saturday"]) &&
            empty($grid["sunday"])
        );
    }

    public function saveFHCalendars($grids)
    {
        foreach ($grids as $grid) {
            if( !$this->gridIsEmpty($grid) ) {
                //get trip collection
                $query = $this->om->createQuery("
                    SELECT t
                    FROM Tisseo\EndivBundle\Entity\Trip t
                    JOIN t.dayCalendar dc
                    JOIN t.periodCalendar pc
                    WHERE dc.id = :day
                    AND pc.id = :period
                ")
                ->setParameter("day", $grid["calendar_day"])
                ->setParameter("period", $grid["calendar_period"]);
                $trips = $query->getResult();

                //get or create grid mask type
                if( isset($grid["grid_calendar"]) ) {
                    $query = $this->om->createQuery("
                        SELECT g
                        FROM Tisseo\EndivBundle\Entity\GridMaskType g
                        WHERE g.id = :id
                    ")
                    ->setParameter("id", $grid["grid_calendar"]);
                } else {
                    //new record in form
                    $query = $this->om->createQuery("
                        SELECT g
                        FROM Tisseo\EndivBundle\Entity\GridMaskType g
                        WHERE g.calendarType = :type
                        AND g.calendarPeriod = :period
                    ")
                    ->setParameter("type", $grid["grid_calendar_type"])
                    ->setParameter("period", $grid["grid_calendar_period"]);
                }

                $gridMaskType = $query->getOneOrNullResult();
                if( empty($gridMaskType) ) {
                    $gridMaskType = new GridMaskType();
                    $gridMaskType->setCalendarType($grid["grid_calendar_type"]);
                    $gridMaskType->setCalendarPeriod($grid["grid_calendar_period"]);
                    $this->om->persist($gridMaskType);
                }

                //create or update trip calendar
                $query = $this->om->createQuery("
                    SELECT tc
                    FROM Tisseo\EndivBundle\Entity\TripCalendar tc
                    WHERE tc.gridMaskType = :gridMaskType
                    AND tc.monday = :monday
                    AND tc.tuesday = :tuesday
                    AND tc.wednesday = :wednesday
                    AND tc.thursday = :thursday
                    AND tc.friday = :friday
                    AND tc.saturday = :saturday
                    AND tc.sunday = :sunday
                ")
                ->setParameter("gridMaskType", $gridMaskType)
                ->setParameter("monday", empty($grid["monday"]) ? false: true)
                ->setParameter("tuesday", empty($grid["tuesday"]) ? false: true)
                ->setParameter("wednesday", empty($grid["wednesday"]) ? false: true)
                ->setParameter("thursday", empty($grid["thursday"]) ? false: true)
                ->setParameter("friday", empty($grid["friday"]) ? false: true)
                ->setParameter("saturday", empty($grid["saturday"]) ? false: true)
                ->setParameter("sunday", empty($grid["sunday"]) ? false: true);
                $tripCalendar = $query->getOneOrNullResult();
                if( empty($tripCalendar) ) {
                    $tripCalendar = new TripCalendar();
                    $tripCalendar->setGridMaskType($gridMaskType);
                    $tripCalendar->setMonday(empty($grid["monday"]) ? false: true);
                    $tripCalendar->setTuesday(empty($grid["tuesday"]) ? false: true);
                    $tripCalendar->setWednesday(empty($grid["wednesday"]) ? false: true);
                    $tripCalendar->setThursday(empty($grid["thursday"]) ? false: true);
                    $tripCalendar->setFriday(empty($grid["friday"]) ? false: true);
                    $tripCalendar->setSaturday(empty($grid["saturday"]) ? false: true);
                    $tripCalendar->setSunday(empty($grid["sunday"]) ? false: true);
                    $gridMaskType->addTripCalendar($tripCalendar);
                    $this->om->persist($tripCalendar);
                    $this->om->persist($gridMaskType);
                }

                foreach ($trips as $trip) {
                    if( $trip->getTripCalendar() != $tripCalendar ) {
                        $trip->setTripCalendar($tripCalendar);
                        $tripCalendar->addTrip($trip);
                        $this->om->persist($trip);
                    }
                }
                $this->om->persist($tripCalendar);
                $this->om->persist($gridMaskType);
            }
        }
        $this->om->flush();
    }

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
