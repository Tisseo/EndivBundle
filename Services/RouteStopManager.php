<?php

namespace Tisseo\EndivBundle\Services;

use Doctrine\ORM\Query\ResultSetMapping;
use Tisseo\EndivBundle\Entity\Route;
use Tisseo\EndivBundle\Entity\RouteStop;

class RouteStopManager extends AbstractManager
{
    /**
     * Get the min or max RouteStop of a Route and a StopArea
     *
     * @param  integer   $routeId
     * @param  integer   $stopAreaId
     * @param  string    $rank
     * @return RouteStop
     */
    public function findMinOrMax($routeId, $stopAreaId, $rank = 'min')
    {
        if (!in_array($rank, array('min', 'max'))) {
            throw new \Exception("Rank must be set to 'min' or 'max'");
        }

        $order = $rank === 'max' ? 'DESC' : 'ASC';

        $query = $this->getRepository()->createQueryBuilder('rs')
            ->select('rs.rank')
            ->join('rs.route', 'r')
            ->join('rs.waypoint', 'w')
            ->join('w.stop', 's')
            ->join('s.stopArea', 'sa')
            ->where('r.id = ?1 AND sa.id = ?2')
            ->orderBy('rs.rank', $order)
            ->setMaxResults(1)
            ->setParameters(array(
                1 => $routeId,
                2 => $stopAreaId
            ))->getQuery();

        return $query->getSingleScalarResult();
    }

    /**
     * Find a section of route
     *
     * @param  integer $routeId
     * @param  integer $minRank
     * @param  integer $maxRank
     * @return Doctrine\Common\Collections\Collection
     */
    public function findSection($routeId, $minRank, $maxRank)
    {
        $query = $this->getRepository()->createQueryBuilder('rs')
            ->join('rs.route', 'r')
            ->where('r.id = ?1')
            ->andWhere('rs.rank between ?2 and ?3')
            ->setParameters(array(
                1 => $routeId,
                2 => $minRank,
                3 => $maxRank
            ))->getQuery();

        return $query->getResult();
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
    public function updateRouteStops(array $routeStops, Route $route)
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

        $waypointRepository = $this->getRepository('Tisseo\EndivBundle\Entity\Waypoint');
        foreach ($routeStops as $routeStop) {
            if (empty($routeStop['id'])) {
                $sync = true;
                $routeStop = $this->getSerializer()->deserialize(json_encode($routeStop), 'Tisseo\EndivBundle\Entity\RouteStop', 'json');
                $waypoint = $waypointRepository
                    ->createQueryBuilder('w')
                    ->where('w.id = :waypoint')
                    ->setParameter('waypoint', $routeStop->getWaypoint()->getId())
                    ->getOneOrNullResult();

                if ($waypoint === null) {
                    throw new \Exception("Can't create a new RouteStop because provided Waypoint with id: ".$routeStop->getWaypoint()->getId()." can't be found.");
                }

                $routeStop->setWaypoint($waypoint);
                $routeStop->setRoute($route);
                $objectManager->persist($routeStop);
            } else {
                $realRouteStop = $this->find($routeStop['id']);

                if ($this->updateRouteStop($realRouteStop, $routeStop)) {
                    $sync = true;
                    $objectManager->merge($realRouteStop);
                }
            }
        }

        if ($sync) {
            $objectManager->flush();
            $this->updateRouteSection($route->getId());
        }
    }

    /**
     * Update a RouteStop values comparing to datas received
     *
     * @param  RouteStop $routeStop
     * @param  array $datas
     * @return boolean
     */
    private function updateRouteStop(RouteStop $routeStop, $data)
    {
        $updated = false;

        if ($routeStop->getRank() !== $data['rank']) {
            $routeStop->setRank($data['rank']);
            $updated = true;
        }
        if ($routeStop->getScheduledStop() !== $data['scheduledStop']) {
            $routeStop->setScheduledStop($data['scheduledStop']);
            $updated = true;
        }
        if ($routeStop->getPickup() !== $data['pickup']) {
            $routeStop->setPickup($data['pickup']);
            $updated = true;
        }
        if ($routeStop->getDropOff() !== $data['dropOff']) {
            $routeStop->setDropOff($data['dropOff']);
            $updated = true;
        }
        if (array_key_exists('internalService', $data) && $routeStop->getInternalService() !== $data['internalService']) {
            $routeStop->setInternalService($data['internalService']);
            $updated = true;
        }

        return $updated;
    }

    /**
     * Update route sections for the modified route
     * This function calls a stored procedure which will
     * update all the RouteSection of the Route
     *
     * @param integer $routeId
     */
    private function updateRouteSection($routeId)
    {
        $query = $this->getObjectManager()
            ->createNativeQuery('SELECT update_route_section_of_route(?)', new ResultSetMapping())
            ->setParameter(1, intval($routeId));

        $query->getResult();
    }
}
