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


class RouteStopManager extends SortManager {

    private $om= null;
    private $repository= null;

    public function __construct(ObjectManager $om) {

        $this->om = $om;
        $this->repository = $om->getRepository("TisseoEndivBundle:RouteStop");
    }

    public function findAll(){

        return $this->repository->findAll();
    }



    public function findById($id) {
        return $this->repository->find($id);
    }

    public function findByWaypoint($waypoint,$idRoute) {

        $query = $this->om->createQuery("
        SELECT rs.id
        FROM Tisseo\EndivBundle\Entity\RouteStop rs
        JOIN rs.waypoint wp
        JOIN rs.route r
        WHERE wp= :waypoint AND r= :route ")
        ->setParameters(array('waypoint' => $waypoint, 'route' => $idRoute));

        return $query->getResult();

    }

    public function getStoptimes($trip) {

        $query = $this->om->createQuery("
        SELECT st.departureTime, st.arrivalTime, IDENTITY(st.routeStop) as routestop
        FROM Tisseo\EndivBundle\Entity\StopTime st
        WHERE st.trip =:trip")
            ->setParameter("trip",$trip);

        return $query->getResult();
    }

    public function save(RouteStop $Stop)
    {
        if(!$Stop->getId()) {
            // new stop + new stop_history
           // $routeStop=new RouteStop();
            $this->om->persist($Stop);
            $this->om->flush();
            $this->om->refresh($Stop);
            $newId = $Stop->getId();
            $Stop->setId($newId);

        }

        $this->om->persist($Stop);
        $this->om->flush();
        $this->om->refresh($Stop);
    }

    public function remove(RouteStop $routestop)

    {
        if($routestop->getId()){

            $this->om->remove($routestop);

            try{
                $this->om->flush();

            }
            catch(\ErrorException $err) {
                var_dump($err);
            }
        }
    }



}