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

/*
use Tisseo\EndivBundle\Entity\LineVersion;
*/

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

    public function findById($id)
    {
        return $this->repository->find($id);
    }

    public function save(Route $route)
    {
        $this->om->persist($route);
        $this->om->flush();
    }

    public function remove(Route $route)
    {
        $this->om->remove($route);
        $this->om->flush();
    }


    public function saveRouteStopsAndServices(Route $route, $route_stops, $services)
    {
        if( !( isset($route) && isset($route_stops) && isset($services)) )  return;

        $rank = 1;
        $doctrine_route_stops = array();
        $route_stop_ids = array();
        foreach ($route_stops as $route_stop) {
            if( isset($route_stop['id']) ) {
                //update existing route stop
                $route_stop_id = $route_stop['id'];
                $route_stop_ids[] = $route_stop_id;
                $filtered_collection = $route->getRouteStops()->filter( function($rs) use ($route_stop_id) {
                    return $rs->getId() == $route_stop_id;
                });
                $doctrine_route = $filtered_collection->first();
            } else {
                //new route stop
                $doctrine_route = new RouteStop();
                $waypoint = $this->om
                            ->getRepository('TisseoEndivBundle:Waypoint')
                            ->find($route_stop['waypoint_id']);
                $doctrine_route->setWaypoint($waypoint);
                $route->addRouteStops($doctrine_route);
            }
            $doctrine_route->setRank($rank);
            $doctrine_route->setDropOff( isset($route_stop['dropOff']) );
            $doctrine_route->setPickup( isset($route_stop['pickUp']) );
            $doctrine_route->setScheduledStop( isset($route_stop['scheduled']) );
            $doctrine_route->setInternalService( ( $route->getWay() == 'Zonal' && isset($route_stop['internal']) ) );
            $this->om->persist($doctrine_route);
            $doctrine_route_stops[] = $doctrine_route;

            $rank += 1;
        }

/*
        //deleted route stops case
        foreach ($route->getRouteStops() as $rs) {
            if( !in_array($rs->getId(), $route_stop_ids) ) {
                fwrite($fp, "1.1\n");
                $route->removeRouteStops($rs);
                $this->om->remove($rs);
            }
        }
*/

        $trip_ids = array();
        foreach ($services as $service) {
            if( isset($service['id']) ) {
                //update existing service
                $trip_id = $service['id'];
                $trip_ids[] = $trip_id;
                unset($service['id']);
                $filtered_route_trips = $route->getTrips()->filter( function($t) use ($trip_id) {
                    return $t->getId() == $trip_id;
                });
                $doctrine_trip = $filtered_route_trips->first();
            } else {
                //new service
                $doctrine_trip = new Trip();
                $doctrine_trip->setRoute($route);
                $route->addTrip($doctrine_trip);
            }

            $trip_name = $service['name'];
            unset($service['name']);
            $doctrine_trip->setName($trip_name);
            $doctrine_trip->setIsPattern(true);

            $stop_time_index = 0;
            foreach ($service as $stop_time) {

                if( isset($stop_time['stop_time_id']) ) {
                    //update stop time
                    $stop_time_id = $stop_time['stop_time_id'];
                    $filtered_trip_stop_time = $doctrine_trip->getStopTimes()->filter( function($st) use ($stop_time_id) {
                        return $st->getId() == $stop_time_id;
                    });
                    $doctrine_stop_time = $filtered_trip_stop_time->first();
                } else {
                    //new stop time
                    $doctrine_stop_time = new StopTime();
                    $doctrine_trip->addStopTime($doctrine_stop_time);
                }

                $date = \DateTime::createFromFormat("Y-m-d H:i:s", "1970-01-01 ".$stop_time['time'].":00");
                $doctrine_stop_time->setRouteStop($doctrine_route_stops[$stop_time_index]);
                $doctrine_stop_time->setTrip($doctrine_trip);
                $doctrine_stop_time->setArrivalTime($date->getTimestamp());
                $doctrine_stop_time->setDepartureTime($date->getTimestamp());
                $this->om->persist($doctrine_stop_time);

                $stop_time_index += 1;
            }

            $this->om->persist($doctrine_trip);
        }

/*
        //deleted trips case
        foreach ($route->getTrips() as $t) {
            if( !in_array($t->getId(), $trip_ids) ) {
                fwrite($fp, "3.1\n");
                $route->removeTrip($t);
                $this->om->remove($t);
            }
        }
*/

        fclose($fp);
        $this->save($route);
    }

    public function getRoutesByLine($lineVersionId)
    {
        $query = $this->om->createQuery("
            SELECT r
            FROM Tisseo\EndivBundle\Entity\Route r
            JOIN r.lineVersion lv
            WHERE lv.id = :id
        ")
        ->setParameter("id", $lineVersionId);

        return $query->getResult();
    }

    public function getRouteStops($RouteId)
    {
        $query = $this->om->createQuery("
            SELECT rs
            FROM Tisseo\EndivBundle\Entity\RouteStop rs
            WHERE rs.route = :id
            ORDER BY rs.rank
        ")
        ->setParameter("id", $RouteId);

        return $query->getResult();
    }

    public function getServiceTemplates($RouteId)
    {
        $query = $this->om->createQuery("
            SELECT t.id as trip_id, 
                t.name as name, 
                rs.id as route_stop_id, 
                st.id as stop_time_id, 
                st.arrivalTime, 
                st.departureTime
            FROM Tisseo\EndivBundle\Entity\Trip t
            JOIN t.stopTimes st
            JOIN st.routeStop rs
            WHERE t.route = :id
            AND t.isPattern = TRUE
            ORDER BY t.id, rs.rank
        ")
        ->setParameter("id", $RouteId);
        $datas = $query->getResult();

        $result = array();
        foreach ($datas as $data) {
            if( !array_key_exists($data["trip_id"], $result) )
                $result[ $data["trip_id"] ] = array();
            $result[ $data["trip_id"] ][] = array_slice($data, -5, 5, true);
        }

        return $result;
    }





/*
    public function findAllByLine($id)
    {
        $query = $this->repository->createQueryBuilder('r')
                                   ->leftJoin("r.lineVersion","line")
                                   ->where("line.id = :id")
                                   ->setParameter("id",$id)
                                   ->getQuery();

        return $query->getResult();
    }




    public function checkZoneStop(Route $route)
    {
        $id = $route->getId();
        $query = $this->om->createQuery("
                SELECT wp.id as waypoint
                FROM Tisseo\EndivBundle\Entity\RouteStop rs
                JOIN rs.waypoint wp
                WHERE rs.route = :id
                ")
                ->setParameter("id",$id)
                ->setMaxResults(1);

        foreach($query->getResult() as $result) {

            $idWaypoint = $result["waypoint"];

            $query = $this->om->createQuery('
            SELECT COUNT(odt.id)
            FROM Tisseo\EndivBundle\Entity\OdtArea odt
            WHERE odt.id = :id
            ')
                ->setParameter('id',$idWaypoint)
                ;

            $area = $query->getResult();
            $zoneWP = $area[0][1];
            if($zoneWP === 0) {
                return false;
            }

            else {
                return true;
            }
        }
    }

    public function getTrips($id)
    {
            $route = $this->om
                ->getRepository('TisseoEndivBundle:Route')
                ->find($id);

            $trips = $route->getTrips();
            return $trips;
    }

    public function hasTrips($id)
    {
         $query = $this->om->createQuery('
            SELECT COUNT(trip.route)
            FROM Tisseo\EndivBundle\Entity\Trip trip
            WHERE trip.route = :id
            ')
                ->setParameter('id',$id)
                ;

                $hasServices = $query->getResult();

                if($hasServices[0][1] === 0) {
                    return false;

                }
                else{
                    return true;
                }
    }
*/
}