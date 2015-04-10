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
use Proxies\__CG__\Tisseo\EndivBundle\Entity\Route;
use Tisseo\EndivBundle\Entity\LineVersion;
use Tisseo\EndivBundle\Services\TripManager;

class RouteManager extends SortManager {

    private $om;
    private $repository;

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
                                   ->where("line = :id")
                                   ->setParameter("id",$id)
                                   ->getQuery();

        return $query->getResult();
    }

    public function getTrips($id){

            $route = $this->om
                ->getRepository('TisseoEndivBundle:Route')
                ->find($id);

            $trips = $route->getTrips();
            return $trips;
    }

}