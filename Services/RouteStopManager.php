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

    public function save(RouteStop $Stop)
    {
        if(!$Stop->getId()) {
            // new stop + new stop_history
            $routeStop=new RouteStop();
            $this->om->persist($routeStop);
            $this->om->flush();
            $this->om->refresh($routeStop);
            $newId = $routeStop->getId();
            $Stop->setId($newId);

        }

        $this->om->persist($Stop);
        $this->om->flush();
        $this->om->refresh($Stop);
    }



}