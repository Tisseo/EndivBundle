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
        // delete non pattern trips first to garanty referential integrity when deleting
        foreach ($route->getTrips() as $trip) {
            if(!$trip->getIsPattern())
                $this->om->remove($trip);
        }

        $this->om->remove($route);
        $this->om->flush();
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

    public function saveRouteStopsAndServices(Route $route, $route_stops, $services)
    {
        if( !( isset($route) && isset($route_stops)) )  return;

        $rank = 1;
        $doctrine_route_stops = array();
        $route_stop_ids = array();
        foreach ($route_stops as $route_stop) {
            if( isset($route_stop['id']) ) {
                //update existing route stop
                $route_stop_id = $route_stop['id'];
                $filtered_collection = $route->getRouteStops()->filter( function($rs) use ($route_stop_id) {
                    return $rs->getId() == $route_stop_id;
                });
                $doctrine_route_stop = $filtered_collection->first();
            } else {
                //new route stop
                $doctrine_route_stop = new RouteStop();
                $waypoint = $this->om
                            ->getRepository('TisseoEndivBundle:Waypoint')
                            ->find($route_stop['waypoint_id']);
                $doctrine_route_stop->setWaypoint($waypoint);
                $route->addRouteStops($doctrine_route_stop);
            }
            $doctrine_route_stop->setRank($rank);
            $doctrine_route_stop->setDropOff( isset($route_stop['dropOff']) );
            $doctrine_route_stop->setPickup( isset($route_stop['pickUp']) );
            $doctrine_route_stop->setScheduledStop( isset($route_stop['scheduled']) );
            $doctrine_route_stop->setInternalService( ( $route->getWay() == 'Zonal' && isset($route_stop['internal']) ) );
            $this->om->persist($doctrine_route_stop);
            $route_stop_ids[] = $doctrine_route_stop->getId();
            $doctrine_route_stops[] = $doctrine_route_stop;

            $rank += 1;
        }

        //set route_section on route_stops
        $this->updateRouteSections($doctrine_route_stops, $route);

        //get ratios
        $ratios = $this->getNotScheduledStopRatios($doctrine_route_stops);

        //deleted route stops case
        foreach ($route->getRouteStops() as $rs) {
            if( !in_array($rs->getId(), $route_stop_ids) ) {
                $route->removeRouteStops($rs);
                $this->om->remove($rs);
            }
        }

        $trip_ids = array();
        if( $services ) {
            foreach ($services as $service) {
                if( isset($service['id']) ) {
                    //update existing service
                    $trip_id = $service['id'];
                    //$trip_ids[] = $trip_id;
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

                    $time_array = explode(":", $stop_time['time']);
                    $time = $time_array[0]*3600 + $time_array[1]*60;

                    $doctrine_stop_time->setRouteStop($doctrine_route_stops[$stop_time_index]);
                    $doctrine_stop_time->setTrip($doctrine_trip);
                    $doctrine_stop_time->setArrivalTime($time);
                    $doctrine_stop_time->setDepartureTime($time);
                    $this->om->persist($doctrine_stop_time);

                    $stop_time_index += 1;

                }
                $this->om->persist($doctrine_trip);
                $trip_ids[] = $doctrine_trip->getId();
            }

        }

        $this->updateNotScheduledStopTimes($route->getPatternTrips(), $ratios);

        //deleted trips case
        foreach ($route->getPatternTrips() as $t) {
            if( !in_array($t->getId(), $trip_ids) ) {
                $route->removeTrip($t);
                $this->om->remove($t);
            }
        }

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

    public function getRouteStopsWithoutRouteSection($routeStops) {
        $i = 0;
        $warnings = array();
        foreach ($routeStops as $rs) {
            if( !$rs->getRouteSection() ) {
                if( $i < count($routeStops) ) {
                    $stop = $rs->getWaypoint()->getStop()->getStopArea()->getShortName();
                    $stop .= ' ('.$rs->getWaypoint()->getStop()->getStopDatasources()[0]->getCode().')';
                    $warnings[] = $stop;
                }                
            }
            $i += 1;
        }

        return $warnings;
    }
}