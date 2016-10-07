<?php

namespace Tisseo\EndivBundle\Services;

use Doctrine\ORM\Query\ResultSetMapping;
use Tisseo\EndivBundle\Entity\Route;
use Tisseo\EndivBundle\Entity\RouteStop;

class RouteStopManager extends AbstractManager
{
    public function findByWaypoint($waypoint, $routeId)
    {
        $query = $this->getObjectManager->createQuery(
            "
            SELECT rs.id
            FROM Tisseo\EndivBundle\Entity\RouteStop rs
            JOIN rs.waypoint wp
            JOIN rs.route r
            WHERE wp= :waypoint AND r= :route"
        )
            ->setParameters(array('waypoint' => $waypoint, 'route' => $routeId));

        return $query->getResult();
    }

    public function findStopMinRankByRouteId($routeId, $stopAreaId)
    {
        $query = $this->getObjectManager->createQueryBuilder()
            ->select('rs.rank')
            ->from('Tisseo\EndivBundle\Entity\RouteStop', 'rs')
            ->join('rs.route', 'r')
            ->join('rs.waypoint', 'w')
            ->join('w.stop', 's')
            ->join('s.stopArea', 'sa')
            ->where('r.id = ?1 AND sa.id = ?2')
            ->orderBy('rs.rank', 'ASC')
            ->setMaxResults(1)
            ->setParameters(
                array(
                1 => $routeId,
                2 => $stopAreaId
                )
            );

        return $query->getQuery()->getSingleScalarResult();
    }

    public function findStopMaxRankByRouteId($routeId, $stopAreaId)
    {
        $query = $this->getObjectManager->createQueryBuilder()
            ->select('rs.rank')
            ->from('Tisseo\EndivBundle\Entity\RouteStop', 'rs')
            ->join('rs.route', 'r')
            ->join('rs.waypoint', 'w')
            ->join('w.stop', 's')
            ->join('s.stopArea', 'sa')
            ->where('r.id = ?1 AND sa.id = ?2')
            ->orderBy('rs.rank', 'DESC')
            ->setMaxResults(1)
            ->setParameters(
                array(
                1 => $routeId,
                2 => $stopAreaId
                )
            );

        return $query->getQuery()->getSingleScalarResult();
    }

    public function getStoptimes($trip)
    {
        $query = $this->getObjectManager->createQuery(
            "
            SELECT st.departureTime, st.arrivalTime, IDENTITY(st.routeStop) as routestop
            FROM Tisseo\EndivBundle\Entity\StopTime st
            WHERE st.trip =:trip"
        )->setParameter("trip", $trip);

        return $query->getResult();
    }

    public function getRouteStopsSectionByMinMaxRank($routeId, $minRank, $maxRank)
    {
        $query = $this->getObjectManager->createQueryBuilder()
            ->select('rs')
            ->from('Tisseo\EndivBundle\Entity\RouteStop', 'rs')
            ->join('rs.route', 'r')
            ->where('r.id = ?1 AND rs.rank BETWEEN ?2 AND ?3')
            ->orderBy('rs.rank', 'ASC')
            ->setParameters(array(
                1 => $routeId,
                2 => $minRank,
                3 => $maxRank
            ));

        return $query->getQuery()->getResult();
    }

    public function save(RouteStop $routeStop)
    {
        $this->om->persist($routeStop);
        $this->om->flush();
    }

    /**
     * Update RouteStops
     *
     * @param array $routeStops
     * @param Route $route
     */
    public function updateRouteStops($routeStops, Route $route)
    {
        $sync = false;
        $objectManager = $this->getObjectManager();

        foreach ($route->getRouteStops() as $routeStop) {
            $existing = array_filter(
                $routeStops,
                function ($object) use ($routeStop) {
                    return ($object['id'] == $routeStop->getId());
                }
            );

            if (empty($existing)) {
                $sync = true;
                $route->removeRouteStop($routeStop);
            }
        }

        foreach ($routeStops as $routeStop) {
            if (empty($routeStop['id'])) {
                $sync = true;
                $routeStop = $this->getSerializer()->deserialize(json_encode($routeStop), 'Tisseo\EndivBundle\Entity\RouteStop', 'json');
                $waypoint = $objectManager->createQuery(
                    "
                    SELECT w FROM Tisseo\EndivBundle\Entity\Waypoint w
                    WHERE w.id = :waypoint
                "
                )
                    ->setParameter('waypoint', $routeStop->getWaypoint()->getId())
                    ->getOneOrNullResult();

                if ($waypoint === null) {
                    throw new \Exception("Can't create a new RouteStop because provided Waypoint with id: ".$routeStop->getWaypoint()->getId()." can't be found.");
                }

                $routeStop->setWaypoint($waypoint);
                $routeStop->setRoute($route);
                $objectManager->persist($routeStop);
            }
            // TODO: find a better way
            else {
                $realRouteStop = $this->find($routeStop['id']);

                if ($this->updateRouteStop($realRouteStop, $routeStop)) {
                    $sync = true;
                    $objectManager->merge($realRouteStop);
                }
            }
        }

        if ($sync) {
            $objectManager->flush();
            $this->updateRouteSection($route);
        }
    }

    /**
     * Update route sections for the modified route
     *
     * @param Route $route
     */
    private function updateRouteSection(Route $route)
    {
        $rsm = new ResultSetMapping();
        $query = $this->getObjectManager()->createNativeQuery('SELECT update_route_section_of_route(?)', $rsm);
        $query->setParameter(1, intval($route->getId()));
        $query->getResult();
    }

    // TODO: improvement required here
    private function updateRouteStop(RouteStop $routeStop, $data)
    {
        $merged = false;

        if ($routeStop->getRank() !== $data['rank']) {
            $routeStop->setRank($data['rank']);
            $merged = true;
        }
        if ($routeStop->getScheduledStop() !== $data['scheduledStop']) {
            $routeStop->setScheduledStop($data['scheduledStop']);
            $merged = true;
        }
        if ($routeStop->getPickup() !== $data['pickup']) {
            $routeStop->setPickup($data['pickup']);
            $merged = true;
        }
        if ($routeStop->getDropOff() !== $data['dropOff']) {
            $routeStop->setDropOff($data['dropOff']);
            $merged = true;
        }
        if (array_key_exists('internalService', $data) && $routeStop->getInternalService() !== $data['internalService']) {
            $routeStop->setInternalService($data['internalService']);
            $merged = true;
        }

        return $merged;
    }
}
