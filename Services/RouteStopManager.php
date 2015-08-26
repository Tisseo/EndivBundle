<?php

namespace Tisseo\EndivBundle\Services;

use Doctrine\Common\Persistence\ObjectManager;
use JMS\Serializer\Serializer;

use Tisseo\EndivBundle\Entity\Route;
use Tisseo\EndivBundle\Entity\RouteStop;

class RouteStopManager extends SortManager {

    private $om = null;
    private $repository = null;
    private $serializer = null;

    public function __construct(ObjectManager $om, Serializer $serializer) {
        $this->om = $om;
        $this->repository = $om->getRepository("TisseoEndivBundle:RouteStop");
        $this->serializer = $serializer;
    }

    public function findAll() {
        return $this->repository->findAll();
    }

    public function find($routeStopId) {
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

    public function getStoptimes($trip)
    {
        $query = $this->om->createQuery("
            SELECT st.departureTime, st.arrivalTime, IDENTITY(st.routeStop) as routestop
            FROM Tisseo\EndivBundle\Entity\StopTime st
            WHERE st.trip =:trip"
        )->setParameter("trip", $trip);

        return $query->getResult();
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
     * @param array $routeStops
     * @param Route $route
     *
     * Creating, updating, deleting RouteStop entities.
     * @usedBy BOABundle
     */
    public function updateRouteStops($routeStops, Route $route)
    {
        $sync = false;
        foreach ($route->getRouteStops() as $routeStop)
        {

            $existing = array_filter(
                $routeStops,
                function ($object) use ($routeStop) {
                    return ($object['id'] == $routeStop->getId());
                }
            );

            if (empty($existing))
            {
                $sync = true;
                $route->removeRouteStop($routeStop);
            }
        }

        foreach ($routeStops as $routeStop)
        {
            if (empty($routeStop['id']))
            {
                $sync = true;
                $routeStop = $this->serializer->deserialize(json_encode($routeStop), 'Tisseo\EndivBundle\Entity\RouteStop', 'json');
                $waypoint = $this->om->createQuery("
                    SELECT w FROM Tisseo\EndivBundle\Entity\Waypoint w
                    WHERE w.id = :waypoint
                ")
                ->setParameter('waypoint', $routeStop->getWaypoint()->getId())
                ->getOneOrNullResult();

                if ($waypoint === null)
                    throw new \Exception("Can't create a new RouteStop because provided Waypoint with id: ".$routeStop->getWaypoint()->getId()." can't be found.");

                $routeStop->setWaypoint($waypoint);
                $routeStop->setRoute($route);
                $this->om->persist($routeStop);
            }
            // TODO: that's ugly, try using serializer in a better way
            else
            {
                $realRouteStop = $this->find($routeStop['id']);

                if ($this->updateRouteStop($realRouteStop, $routeStop))
                {
                    $sync = true;
                    $this->om->merge($realRouteStop);
                }
            }
        }

        if ($sync)
            $this->om->flush();
    }

    // TODO: find something better, this is really bad
    private function updateRouteStop(RouteStop $routeStop, $data)
    {
        $merged = false;

        if ($routeStop->getRank() !== $data['rank'])
        {
            $routeStop->setRank($data['rank']);
            $merged = true;
        }
        if ($routeStop->getScheduledStop() !== $data['scheduledStop'])
        {
            $routeStop->setScheduledStop($data['scheduledStop']);
            $merged = true;
        }
        if ($routeStop->getPickup() !== $data['pickup'])
        {
            $routeStop->setPickup($data['pickup']);
            $merged = true;
        }
        if ($routeStop->getDropOff() !== $data['dropOff'])
        {
            $routeStop->setDropOff($data['dropOff']);
            $merged = true;
        }

        return $merged;
    }
}
