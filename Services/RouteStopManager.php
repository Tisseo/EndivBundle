<?php

namespace Tisseo\EndivBundle\Services;

use Doctrine\Common\Persistence\ObjectManager;
use JMS\Serializer\Serializer;
use Doctrine\ORM\Query\ResultSetMapping;
use Tisseo\EndivBundle\Entity\Route;
use Tisseo\EndivBundle\Entity\RouteStop;

class RouteStopManager extends SortManager
{
    private $om = null;
    private $repository = null;
    private $serializer = null;

    public function __construct(ObjectManager $om, Serializer $serializer)
    {
        $this->om = $om;
        $this->repository = $om->getRepository('TisseoEndivBundle:RouteStop');
        $this->serializer = $serializer;
    }

    public function findAll()
    {
        return $this->repository->findAll();
    }

    public function find($routeStopId)
    {
        return empty($routeStopId) ? null : $this->repository->find($routeStopId);
    }

    public function findByWaypoint($waypoint, $routeId)
    {
        $query = $this->om->createQuery("
            SELECT rs.id
            FROM Tisseo\EndivBundle\Entity\RouteStop rs
            JOIN rs.waypoint wp
            JOIN rs.route r
            WHERE wp= :waypoint AND r= :route")
        ->setParameters(array('waypoint' => $waypoint, 'route' => $routeId));

        return $query->getResult();
    }

    public function findStopMinRankByRouteId($routeId, $stopAreaId)
    {
        $qb = $this->om->createQueryBuilder()
                       ->select('rs.rank')
                       ->from('Tisseo\EndivBundle\Entity\RouteStop', 'rs')
                       ->join('rs.route', 'r')
                       ->join('rs.waypoint', 'w')
                       ->join('w.stop', 's')
                       ->join('s.stopArea', 'sa')
                       ->where('r.id = ?1 AND sa.id = ?2')
                       ->orderBy('rs.rank', 'ASC')
                       ->setMaxResults(1)
                       ->setParameters(array(
                           1 => $routeId,
                           2 => $stopAreaId
                       ))
            ;

        return $qb
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findStopMaxRankByRouteId($routeId, $stopAreaId)
    {
        $qb = $this->om->createQueryBuilder()
                       ->select('rs.rank')
                       ->from('Tisseo\EndivBundle\Entity\RouteStop', 'rs')
                       ->join('rs.route', 'r')
                       ->join('rs.waypoint', 'w')
                       ->join('w.stop', 's')
                       ->join('s.stopArea', 'sa')
                       ->where('r.id = ?1 AND sa.id = ?2')
                       ->orderBy('rs.rank', 'DESC')
                       ->setMaxResults(1)
                       ->setParameters(array(
                           1 => $routeId,
                           2 => $stopAreaId
                       ))
            ;

        return $qb
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getStoptimes($trip)
    {
        $query = $this->om->createQuery("
            SELECT st.departureTime, st.arrivalTime, IDENTITY(st.routeStop) as routestop
            FROM Tisseo\EndivBundle\Entity\StopTime st
            WHERE st.trip =:trip"
        )->setParameter('trip', $trip);

        return $query->getResult();
    }

    public function getRouteStopsSectionByMinMaxRank($routeId, $minRank, $maxRank)
    {
        $qb = $this->om->createQueryBuilder()
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

        return $qb
            ->getQuery()
            ->getResult()
            ;
    }

    public function save(RouteStop $routeStop)
    {
        $this->om->persist($routeStop);
        $this->om->flush();
    }

    public function remove(RouteStop $routeStop)
    {
        $this->om->remove($routeStop);
        $this->om->flush();
    }

    /**
     * VERIFIED USEFUL FUNCTIONS
     */

    /**
     * Update RouteStops
     *
     * @param array $routeStops
     * @param Route $route
     *
     * Creating, updating, deleting RouteStop entities.
     * @usedBy BOABundle
     */
    public function updateRouteStops($routeStops, Route $route)
    {
        $sync = false;
        foreach ($route->getRouteStops() as $routeStop) {
            $existing = array_filter(
                $routeStops,
                function ($object) use ($routeStop) {
                    return $object['id'] == $routeStop->getId();
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
                $routeStop = $this->serializer->deserialize(json_encode($routeStop), 'Tisseo\EndivBundle\Entity\RouteStop', 'json');
                $waypoint = $this->om->createQuery("
                    SELECT w FROM Tisseo\EndivBundle\Entity\Waypoint w
                    WHERE w.id = :waypoint
                ")
                ->setParameter('waypoint', $routeStop->getWaypoint()->getId())
                ->getOneOrNullResult();

                if ($waypoint === null) {
                    throw new \Exception("Can't create a new RouteStop because provided Waypoint with id: ".$routeStop->getWaypoint()->getId()." can't be found.");
                }
                $routeStop->setWaypoint($waypoint);
                $routeStop->setRoute($route);
                $this->om->persist($routeStop);
            }
            // TODO: that's ugly, try using serializer in a better way
            else {
                $realRouteStop = $this->find($routeStop['id']);

                if ($this->updateRouteStop($realRouteStop, $routeStop)) {
                    $sync = true;
                    $this->om->merge($realRouteStop);
                }
            }
        }

        if ($sync) {
            $this->om->flush();
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
        $query = $this->om->createNativeQuery('SELECT update_route_section_of_route(?)', $rsm);
        $query->setParameter(1, intval($route->getId()));
        $query->getResult();
    }

    // TODO: find something better, this is really bad
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
        if (array_key_exists('internalService', $data) and $routeStop->getInternalService() !== $data['internalService']) {
            $routeStop->setInternalService($data['internalService']);
            $merged = true;
        }

        return $merged;
    }
}
