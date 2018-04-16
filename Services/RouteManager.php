<?php

namespace Tisseo\EndivBundle\Services;

use Doctrine\Common\Persistence\ObjectManager;
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

class RouteManager extends SortManager
{
    private $om = null;
    private $repository = null;
    private $stopManager = null;

    public function __construct(ObjectManager $om, StopManager $stopManager)
    {
        $this->om = $om;
        $this->repository = $om->getRepository('TisseoEndivBundle:Route');
        $this->stopManager = $stopManager;
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
        $comment = $route->getComment();

        if (
            !empty($comment) &&
            $comment->getLabel() == null &&
            $comment->getCommentText() == null
        ) {
            $route->setComment();
            $this->om->remove($comment);
        }

        $this->om->persist($route);
        $this->om->flush();
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
            throw new \Exception("Can't delete this route because it has ".$trips.' trips.');
        }

        $lineVersionId = $route->getLineVersion()->getId();

        $this->om->remove($route);
        $this->om->flush();

        return $lineVersionId;
    }

    private function getRouteSectionLength($routeSectionId)
    {
        $connection = $this->om->getConnection()->getWrappedConnection();
        $stmt = $connection->prepare('
            select ST_Length(the_geom) from route_section where id = :rsId::int
        ');
        $stmt->bindValue(':rsId', $routeSectionId, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchColumn();
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
            ->setParameter('route', $route);
        //convert associative array of in to array of strings
        $tmp = array_map('current', $query->getArrayResult());

        return array_map('strval', $tmp);
    }

    /**
     * Get Timetable Calendars
     *
     * @param int lineVersionId
     *
     * Creating an array with grouped Trips by Route with their calendar/trip_calendar.
     */
    public function getTimetableCalendars($lineVersionId)
    {
        $routes = $this->repository->findBy(array('lineVersion' => $lineVersionId));

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

    public function getSortedTypesOfGridMaskType(
        $included = null,
        $scenario = null,
        $types = array('Semaine', 'Samedi', 'Dimanche')
    ) {
        $queryBuilder = $this->om->createQueryBuilder('g');
        $expr = $queryBuilder->expr();

        $queryBuilder
            ->select('DISTINCT g.calendarType')
            ->from('Tisseo\EndivBundle\Entity\GridMaskType', 'g')
            ->where($expr->notIn('g.calendarType', ':types'))
            ->setParameter('types', $types);

        if (!$scenario) {
            $queryBuilder->andWhere($expr->isNull('g.scenario'));
        } else {
            $queryBuilder
                ->andWhere($expr->eq('g.scenario', ':scenario'))
                ->setParameter('scenario', $scenario);
        }

        if (!$included) {
            $queryBuilder->andWhere($expr->isNull('g.included'));
        } else {
            $queryBuilder
                ->andWhere($expr->eq('g.included', ':included'))
                ->setParameter('included', $included);
        }

        $queryBuilder->orderBy('g.calendarType', 'ASC');

        $result = $queryBuilder->getQuery()->getArrayResult();
        $result = array_map('current', $result);

        foreach (array_reverse($types) as $type) {
            array_unshift($result, $type);
        }

        return $result;
    }

    public function getSortedPeriodsOfGridMaskType(
        $included = null,
        $scenario = null,
        $periods = array('Base', 'Vacances', 'Ete')
    ) {
        $queryBuilder = $this->om->createQueryBuilder('g');
        $expr = $queryBuilder->expr();

        $queryBuilder
            ->select('DISTINCT g.calendarPeriod')
            ->from('Tisseo\EndivBundle\Entity\GridMaskType', 'g')
            ->where($expr->notIn('g.calendarPeriod', ':periods'))
            ->setParameter('periods', $periods);

        if (!$scenario) {
            $queryBuilder->andWhere($expr->isNull('g.scenario'));
        } else {
            $queryBuilder
                ->andWhere($expr->eq('g.scenario', ':scenario'))
                ->setParameter('scenario', $scenario);
        }

        if (!$included) {
            $queryBuilder->andWhere($expr->isNull('g.included'));
        } else {
            $queryBuilder
                ->andWhere($expr->eq('g.included', ':included'))
                ->setParameter('included', $included);
        }

        $queryBuilder->orderBy('g.calendarPeriod', 'ASC');

        $result = $queryBuilder->getQuery()->getArrayResult();
        $result = array_map('current', $result);

        foreach (array_reverse($periods) as $period) {
            array_unshift($result, $period);
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
        foreach ($datas as $data) {
            $tripCalendar = null;
            $queryBuilder = $this->om->createQueryBuilder();
            $expr = $queryBuilder->expr();

            $queryBuilder
                ->select('gmt')
                ->from('Tisseo\EndivBundle\Entity\GridMaskType', 'gmt')
                ->where($expr->eq('gmt.calendarPeriod', ':period'))
                ->andWhere($expr->eq('gmt.calendarType', ':type'))
                ->setParameter('period', $data['calendarPeriod'])
                ->setParameter('type', $data['calendarType']);

            if (!array_key_exists('scenario', $data) || !$data['scenario']) {
                $queryBuilder->andWhere($expr->isNull('gmt.scenario'));
            } else {
                $queryBuilder
                    ->andWhere($expr->eq('gmt.scenario', ':scenario'))
                    ->setParameter('scenario', $data['scenario']);
            }

            if (!array_key_exists('included', $data) || !$data['included']) {
                $queryBuilder->andWhere($expr->isNull('gmt.included'));
            } else {
                $queryBuilder
                    ->andWhere($expr->eq('gmt.included', ':included'))
                    ->setParameter('included', $data['included']);
            }

            $gridMaskType = $queryBuilder->getQuery()->getOneOrNullResult();

            if (!empty($gridMaskType) && !empty($data['tripCalendar'])) {
                $pattern = implode(array_values($data['days']));

                $queryBuilder = $this->om->createQueryBuilder();
                $expr = $queryBuilder->expr();

                // Doctrine CAST(x, y) is transformed into (x || y). x and y have to be varchars.
                $tripCalendar = $queryBuilder
                    ->select('tc')
                    ->from('Tisseo\EndivBundle\Entity\TripCalendar', 'tc')
                    ->where($expr->eq('tc.gridMaskType', ':gridMaskType'))
                    ->andWhere($expr->eq('CONCAT(CAST(CAST(tc.monday AS INTEGER) AS CHAR), CAST(CAST(tc.tuesday AS INTEGER) AS  CHAR), CAST(CAST(tc.wednesday AS INTEGER) AS CHAR), CAST(CAST(tc.thursday AS INTEGER) AS CHAR), CAST(CAST(tc.friday AS INTEGER) AS CHAR), CAST(CAST(tc.saturday AS INTEGER) AS CHAR), CAST(CAST(tc.sunday AS INTEGER) AS CHAR))', ':pattern'))
                    ->setParameter('gridMaskType', $gridMaskType)
                    ->setParameter('pattern', $pattern)
                    ->getQuery()
                    ->getOneOrNullResult();

                if (!empty($tripCalendar)) {
                    $queryBuilder = $this->om->createQueryBuilder();
                    $expr = $queryBuilder->expr();

                    $oldTripCalendar = $queryBuilder
                        ->select('tc')
                        ->from('Tisseo\EndivBundle\Entity\TripCalendar', 'tc')
                        ->where($expr->eq('tc.id', ':tripCalendar'))
                        ->setParameter('tripCalendar', $data['tripCalendar'])
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
            }

            if (empty($tripCalendar)) {
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

            $queryBuilder = $this->om->createQueryBuilder();
            $expr = $queryBuilder->expr();
            $trips = $queryBuilder
                ->select('t')
                ->from('Tisseo\EndivBundle\Entity\Trip', 't')
                ->where($expr->in('t.id', ':trips'))
                ->setParameter('trips', $tripIds)
                ->getQuery()
                ->getResult();

            foreach ($trips as $trip) {
                $trip->setTripCalendar($tripCalendar);
                $this->om->persist($trip);
            }

            $this->om->flush();
        }
    }

    public function updateExportDestinations($route, $exportDestinations)
    {
        $sync = false;
        foreach ($route->getRouteExportDestinations() as $routeExportDestination) {
            if (!($exportDestinations->contains($routeExportDestination->getExportDestination()))) {
                $sync = true;
                $route->removeRouteExportDestination($routeExportDestination);
                $this->om->remove($routeExportDestination);
            }
        }

        foreach ($exportDestinations as $exportDestination) {
            if (!($route->getExportDestinations()->contains($exportDestination))) {
                $routeExportDestination = new RouteExportDestination();
                $routeExportDestination->setExportDestination($exportDestination);
                $routeExportDestination->setRoute($route);
                $sync = true;
                $route->addRouteExportDestination($routeExportDestination);
                $this->om->persist($routeExportDestination);
            }
        }
        if ($sync) {
            $this->om->flush();
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
                $rank = $routeStop->getRank();
                if ($routeStop->isOdtAreaRouteStop()) {
                    $odtArea = $routeStop->getWaypoint()->getOdtArea();
                    foreach ($odtArea->getOpenedOdtStops() as $odtStop) {
                        $stops[] = array(
                            'stop' => $odtStop->getStop(),
                            'rank' => $rank,
                            'odt_area' => $odtArea->getId()
                        );
                    }
                } else {
                    $stops[] = array(
                        'stop' => $routeStop->getWaypoint()->getStop(),
                        'rank' => $rank
                    );
                }
            }

            $data = $this->stopManager->getStopJsonWithRankAndOdtArea($stops, true);
        } //otherwise, we return a list of stops with their WKT
        else {
            $connection = $this->om->getConnection()->getWrappedConnection();

            $query = 'SELECT DISTINCT s.id as id, s.master_stop_id as master_stop_id, sh.short_name as name, sd.code as code, rs.rank as rank, ST_X(ST_Transform(sh.the_geom, 4326)) as x, ST_Y(ST_Transform(sh.the_geom, 4326)) as y, ST_AsGeoJSON(ST_Transform(rsec.the_geom, 4326)) as geom
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
                ORDER BY rs.rank';
            $stmt = $connection->prepare($query);
            $stmt->bindValue(':route_id', $route->getId());
            $stmt->execute();

            $data = $stmt->fetchAll();
        }

        return $this->setShades($data);
    }

    /**
     * @param $stops
     * @param array $odtStopsByArea
     *
     * @return array
     */
    private function setShades($stops)
    {
        $ranks = array();
        foreach ($stops as $stop) {
            if (!in_array($stop['rank'], $ranks, true)) {
                $ranks[] = $stop['rank'];
            }
        }

        sort($ranks);
        $num = count($ranks);
        $shadeList = array();
        for ($i = 1; $i < ($num + 1); ++$i) {
            $shadeList[$ranks[$i - 1]] = round($i * (1 / $num), 4);
        }

        foreach ($stops as $k => $stop) {
            $stops[$k]['shade'] = $shadeList[$stop['rank']];
        }

        return $stops;
    }

    // TODO: CHANGE THIS
    public function duplicate($route, $lineVersion, $userName)
    {
        $query = $this->om->createQuery("
            SELECT ds FROM Tisseo\EndivBundle\Entity\Datasource ds
            WHERE ds.name = ?1
        ")->setParameter(1, 'Service Données');

        $datasource = $query->getOneOrNullResult();

        $newRoute = new Route();
        $newRoute->setWay($route->getWay());
        $newRoute->setName($route->getName().' (Copie)');
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
            $this->om->persist($newRS);

            $route_stops[$rs->getId()] = $newRS;
        }

        $services_patterns = $route->getTrips()->filter(function ($t) {
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

                $newST->setRouteStop($route_stops[$st->getRouteStop()->getId()]);
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
                $newTrip->addTripDatasource($tripDatasource);
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
