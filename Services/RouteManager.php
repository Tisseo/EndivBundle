<?php

namespace Tisseo\EndivBundle\Services;

use Tisseo\EndivBundle\Entity\Route;
use Tisseo\EndivBundle\Entity\RouteStop;
use Tisseo\EndivBundle\Entity\Trip;
use Tisseo\EndivBundle\Entity\StopTime;
use Tisseo\EndivBundle\Entity\GridMaskType;
use Tisseo\EndivBundle\Entity\TripCalendar;
use Tisseo\EndivBundle\Entity\TripDatasource;
use Tisseo\EndivBundle\Entity\RouteDatasource;
use Tisseo\EndivBundle\Entity\Stop;
use Tisseo\EndivBundle\Entity\RouteExportDestination;
use Tisseo\EndivBundle\Services\StopManager;

class RouteManager extends AbstractManager
{
    /**
     * {inheritdoc}
     */
    public function save(Route $route)
    {
        $objectManager = $this->getObjectManager();

        $comment = $route->getComment();

        if (!empty($comment)
            && $comment->getLabel() === null
            && $comment->getCommentText() === null
        ) {
            $route->setComment();
            $objectManager->remove($comment);
        }

        $objectManager->persist($route);
        $objectManager->flush();
    }

    public function remove($routeId)
    {
        $route = $this->find($routeId);

        if (empty($route)) {
            throw new \Exception("Can't find the route with ID: ".$routeId);
        }

        $trips = $route->getTripsNotPattern();

        // TODO: Later, condition is if ACTIVE (calendar_start_date > now > calendar_end_date) trips found, can't delete
        if ($trips->count() > 0) {
            throw new \Exception("Can't delete this route because it has ".$trips." trips.");
        }

        $objectManager = $this->getObjectManager();
        $lineVersionId = $route->getLineVersion()->getId();

        $objectManager->remove($route);
        $objectManager->flush();

        return $lineVersionId;
    }

    public function getInstantiatedServiceTemplates($route)
    {
        $query = $this->getObjectManager()->createQuery(
            "
            SELECT DISTINCT t1.id
            FROM Tisseo\EndivBundle\Entity\Trip t
            JOIN Tisseo\EndivBundle\Entity\Trip t1
            WITH t.pattern = t1
            WHERE t1.route = :route
        "
        )
            ->setParameter("route", $route);
        //convert associative array of in to array of strings
        $tmp = array_map('current', $query->getArrayResult());
        return array_map('strval', $tmp);
    }

    /**
     * Get Timetable Calendars
     *
     * @param integer lineVersionId
     *
     * Creating an array with grouped Trips by Route with their calendar/trip_calendar.
     */
    public function getTimetableCalendars($lineVersionId)
    {
        $routes = $this->getRepository()->findBy(array('lineVersion' => $lineVersionId));

        $result = array();
        foreach ($routes as $route) {
            $way = $route->getWay();
            if (!array_key_exists($way, $result)) {
                $result[$way] = array();
            }

            foreach ($route->getTripsNotPatternWithCalendars() as $trip) {
                $calendarKey = $trip->getDayCalendar()->getName().'_'.$trip->getPeriodCalendar()->getName();

                if (!array_key_exists($calendarKey, $result[$way])) {
                    $result[$way][$calendarKey]['dayCalendar'] = $trip->getDayCalendar();
                    $result[$way][$calendarKey]['periodCalendar'] = $trip->getPeriodCalendar();
                    $result[$way][$calendarKey]['route'] = $route->getId();
                }

                $result[$way][$calendarKey]['trips'][] = $trip;

                if ($trip->getTripCalendar() && !array_key_exists('tripCalendar', $result[$way][$calendarKey])) {
                    $result[$way][$calendarKey]['tripCalendar'] = $trip->getTripCalendar();
                }
            }
        }

        return $result;
    }

    public function getSortedTypesOfGridMaskType()
    {
        $result = array("Semaine", "Samedi", "Dimanche");
        $query = $this->getObjectManager()->createQuery(
            "
            SELECT DISTINCT g.calendarType
            FROM Tisseo\EndivBundle\Entity\GridMaskType g
            WHERE g.calendarType NOT IN (:type)
        "
        )
            ->setParameter("type", $result);

        foreach ($query->getResult() as $value) {
            $result[] = $value['calendarType'];
        }

        return $result;
    }

    public function getSortedPeriodsOfGridMaskType()
    {
        $result = array("Base", "Vacances", "Ete");
        $query = $this->getObjectManager()->createQuery(
            "
            SELECT DISTINCT g.calendarPeriod
            FROM Tisseo\EndivBundle\Entity\GridMaskType g
            WHERE g.calendarPeriod NOT IN (:period)
        "
        )
            ->setParameter("period", $result);

        foreach ($query->getResult() as $value) {
            $result[] = $value['calendarPeriod'];
        }

        return $result;
    }

    /**
     * Link TripCalendars
     *
     * @param array $datas
     *
     * Linking existing TripCalendars to Trip entities.
     * Creating new TripCalendar/GridMaskType if needed.
     */
    public function linkTripCalendars($datas)
    {
        $objectManager = $this->getObjectManager();

        foreach ($datas as $data) {
            $tripCalendar = null;

            $gridMaskType = $this->getRepository('Tisseo\EndivBundle\Entity\GridMaskType')
                ->createQueryBuilder('gmt')
                ->where('gmt.calendarPeriod = :period')
                ->andWhere('gmt.calendarType = :type')
                ->setParameter("period", $data['calendarPeriod'])
                ->setParameter("type", $data['calendarType'])
                ->getQuery()
                ->getOneOrNullResult();

            if (!empty($gridMaskType)) {
                $pattern = implode(array_values($data['days']));

                // Doctrine CAST(x, y) is transformed into (x || y). x and y have to be varchars.
                $tripCalendar = $this->getRepository('Tisseo\EndivBundle\Entity\TripCalendar')
                    ->createQueryBuilder('tc')
                    ->where('tc.gridMaskType = :gmt')
                    ->andWhere('concat(cast(cast(tc.monday as integer) as char), cast(cast(tc.tuesday as integer) as char),
                        cast(cast(tc.wednesday as integer) as char), cast(cast(tc.thursday as integer) as char),
                        cast(cast(tc.friday as integer) as char), cast(cast(tc.saturday as integer) as char),
                        cast(cast(tc.sunday as integer) as char)) = :pattern')
                    ->setParameter("gmt", $gridMaskType)
                    ->setParameter("pattern", $pattern)
                    ->getQuery()
                    ->getOneOrNullResult();

                if (!empty($tripCalendar) && !empty($data['tripCalendar'])) {
                    $oldTripCalendar = $this->getRepository('Tisseo\EndivBundle\Entity\TripCalendar')
                        ->createQueryBuilder('tc')
                        ->where('tc.id = :tc')
                        ->setParameter('tc', $data['tripCalendar'])
                        ->getQuery()
                        ->getOneOrNullResult();

                    if (!empty($oldTripCalendar) && $oldTripCalendar->getId() === $tripCalendar->getId()) {
                        continue;
                    }
                }
            }

            if (empty($gridMaskType)) {
                $gridMaskType = new GridMaskType();
                $gridMaskType->setCalendarType($data['calendarType']);
                $gridMaskType->setCalendarPeriod($data['calendarPeriod']);

                $objectManager->persist($gridMaskType);
                $objectManager->flush($gridMaskType);
            }

            if (empty($tripCalendar)) {
                $tripCalendar = new TripCalendar();
                $tripCalendar->setGridMaskType($gridMaskType);

                $tripCalendar->setMonday(filter_var($data['days'][1], FILTER_VALIDATE_BOOLEAN));
                $tripCalendar->setTuesday(filter_var($data['days'][2], FILTER_VALIDATE_BOOLEAN));
                $tripCalendar->setWednesday(filter_var($data['days'][3], FILTER_VALIDATE_BOOLEAN));
                $tripCalendar->setThursday(filter_var($data['days'][4], FILTER_VALIDATE_BOOLEAN));
                $tripCalendar->setFriday(filter_var($data['days'][5], FILTER_VALIDATE_BOOLEAN));
                $tripCalendar->setSaturday(filter_var($data['days'][6], FILTER_VALIDATE_BOOLEAN));
                $tripCalendar->setSunday(filter_var($data['days'][7], FILTER_VALIDATE_BOOLEAN));

                $objectManager->persist($tripCalendar);
                $objectManager->flush($tripCalendar);
            }

            $tripIds = array_values($data['trips']);

            $trips = $this->getRepository('Tisseo\EndivBundle\Entity\Trip')
                ->createQueryBuilder('t')
                ->where('t.id IN (:trips)')
                ->setParameter('trips', $tripIds)
                ->getQuery()
                ->getResult();

            foreach ($trips as $trip) {
                $trip->setTripCalendar($tripCalendar);
                $objectManager->persist($trip);
            }
        }

        $objectManager->flush();
    }

    public function updateExportDestinations($route, $exportDestinations)
    {
        $sync = false;
        $objectManager = $this->getObjectManager();
        foreach ($route->getRouteExportDestinations() as $rExportDest) {
            if (!($exportDestinations->contains($rExportDest->getExportDestination()))) {
                $sync = true;
                $route->removeRouteExportDestination($rExportDest);
                $objectManager->remove($rExportDest);
            }
        }

        foreach ($exportDestinations as $exportDestination) {
            if (!($route->getExportDestinations()->contains($exportDestination))) {
                $rExportDest = new RouteExportDestination();
                $rExportDest->setExportDestination($exportDestination);
                $rExportDest->setRoute($route);
                $sync = true;
                $route->addRouteExportDestination($rExportDest);
                $objectManager->persist($rExportDest);
            }
        }
        if ($sync) {
            $objectManager->flush();
        }
    }

    public function getRouteStopsJson($route)
    {
        if (empty($route)) {
            return null;
        }

        // if the route is a 'TAD zonal' route, then returns a list of stops whitout geometry,
        // with all stops from 'stop' routeStops, concatenated will all stops from 'odtArea' routeStops
        if ($route->getWay() == Route::WAY_AREA) {
            $stops = array();
            foreach ($route->getRouteStops() as $routeStop) {
                if ($routeStop->isOdtAreaRouteStop()) {
                    foreach ($routeStop->getWaypoint()->getOdtArea()->getOpenedOdtStops() as $odtStop) {
                        $stops[] = $odtStop->getStop();
                    }
                } else {
                    $stops[] = $routeStop->getWaypoint()->getStop();
                }
            }

            return $this->getService('stop')->getStopsJson($stops, true);
        }
        //otherwise, we return a list of stops with their WKT
        else {
            $connection = $this->ObjectManager()->getConnection()->getWrappedConnection();

            $query="SELECT DISTINCT s.id as id, s.master_stop_id as master_stop_id, sh.short_name as name, sd.code as code, rs.rank as rank, ST_X(ST_Transform(sh.the_geom, 4326)) as x, ST_Y(ST_Transform(sh.the_geom, 4326)) as y, ST_AsGeoJSON(ST_Transform(rsec.the_geom, 4326)) as geom
                FROM stop s
                JOIN waypoint w on s.id = w.id
                JOIN route_stop rs on w.id = rs.waypoint_id
                JOIN route r on rs.route_id = r.id
                LEFT JOIN route_section rsec on rs.route_section_id = rsec.id
                JOIN stop_datasource sd on s.id = sd.stop_id
                JOIN stop_history sh on (sh.stop_id = COALESCE(s.master_stop_id, s.id))
                WHERE r.id = :route_id
                AND sh.start_date <= CURRENT_DATE
                AND (sh.end_date IS NULL OR sh.end_date > CURRENT_DATE)
                ORDER BY rs.rank";
            $stmt = $connection->prepare($query);
            $stmt->bindValue(':route_id', $route->getId());
            $stmt->execute();
            return $stmt->fetchAll();
        }
    }

    public function duplicate($route, $lineVersion, $userName)
    {
        $objectManager = $this->getObjectManager();
        $query = $objectManager->createQuery(
            "
            SELECT ds FROM Tisseo\EndivBundle\Entity\Datasource ds
            WHERE ds.name = ?1
        "
        )->setParameter(1, "Service Données");

        $datasource = $query->getOneOrNullResult();

        $newRoute = new Route();
        $newRoute->setWay($route->getWay());
        $newRoute->setName($route->getName()." (Copie)");
        $newRoute->setDirection($route->getDirection());
        if ($route->getComment()) {
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
            $objectManager->persist($newRS);

            $route_stops[$rs->getId()] = $newRS;
        }

        $services_patterns = $route->getTrips()->filter(
            function (Trip $t) {
                return $t->getPattern() === true;
            }
        );

        foreach ($services_patterns as $t) {
            $newTrip = new Trip();
            $newTrip->setName($t->getName());
            $newTrip->setPattern($t->isPattern());
            $newTrip->setTripPattern($t->getTripPattern());
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

                $newST->setRouteStop($route_stops[ $st->getRouteStop()->getId() ]);
                $newST->setTrip($newTrip);
                $newTrip->addStopTime($newST);
                $objectManager->persist($newST);
            }

            if (!empty($datasource)) {
                $tripDatasource = new TripDatasource();
                $tripDatasource->setDatasource($datasource);
                $tripDatasource->setTrip($newTrip);
                $tripDatasource->setCode($userName);
                $objectManager->persist($tripDatasource);
                $newTrip->addTripDatasource($tripDatasource);
            }
            $objectManager->persist($newTrip);
        }

        if (!empty($datasource)) {
            $routeDatasource = new RouteDatasource();
            $routeDatasource->setDatasource($datasource);
            $routeDatasource->setRoute($newRoute);
            $routeDatasource->setCode($userName);
            $objectManager->persist($routeDatasource);
            $newRoute->addRouteDatasource($routeDatasource);
        }

        $objectManager->persist($newRoute);
        $objectManager->flush();
    }
}
