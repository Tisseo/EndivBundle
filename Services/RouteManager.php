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

class RouteManager extends SortManager {

    private $om= null;
    private $repository= null;

    public function __construct(ObjectManager $om) {

        $this->om = $om;
        $this->repository = $om->getRepository("TisseoEndivBundle:Route");
    }

    public function findAll(){

        return $this->repository->findAll();
    }

    public function findAllByLine($id){

        $query = $this->repository->createQueryBuilder('r')
                                   ->leftJoin("r.lineVersion","line")
                                   ->where("line.id = :id")
                                   ->setParameter("id",$id)
                                   ->getQuery();

        return $query->getResult();
    }

    public function findById($id) {
        return $this->repository->find($id);
    }

    public function getWaypoints($id) {

        $query = $this->om->createQuery("
               SELECT rs.id, rs.rank, wp.id as waypoint
               FROM Tisseo\EndivBundle\Entity\RouteStop rs
               JOIN rs.waypoint wp
               WHERE rs.route = :id
        ");
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

    public function removeRoute(Route $route) {

        $this->om->remove($route);

        try{
            $this->om->flush();

        }
        catch(\ErrorException $err) {
            var_dump($err);
        }
    }
    public function getTrips($id){

            $route = $this->om
                ->getRepository('TisseoEndivBundle:Route')
                ->find($id);

            $trips = $route->getTrips();
            return $trips;
    }

}