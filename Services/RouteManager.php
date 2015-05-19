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

use Tisseo\EndivBundle\Entity\LineVersion;
use Tisseo\EndivBundle\Services\TripManager;

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






    public function findAllByLine($id)
    {
        $query = $this->repository->createQueryBuilder('r')
                                   ->leftJoin("r.lineVersion","line")
                                   ->where("line.id = :id")
                                   ->setParameter("id",$id)
                                   ->getQuery();

        return $query->getResult();
    }

    public function findById($id)
    {
        return $this->repository->find($id);
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


    public function save(Route $route)
    {
        $this->om->persist($route);
        try{
            $this->om->flush();

        }
        catch(\ErrorException $err) {
            var_dump($err);
        }
    }

    public function removeRoute(Route $route)
    {
        $this->om->remove($route);

        try{
            $this->om->flush();

        }
        catch(\ErrorException $err) {
            var_dump($err);
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

}