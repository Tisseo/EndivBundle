<?php

namespace Tisseo\EndivBundle\Services;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\EntityManager;

use Tisseo\EndivBundle\Entity\City;


class CityManager extends SortManager
{
    private $om = null;
    private $repository = null;

    public function __construct(ObjectManager $om)
    {   
        $this->om = $om;
        $this->repository = $om->getRepository('TisseoEndivBundle:City');
    }   

    public function findAll()
    {   
        return ($this->repository->findAll());
    }   

    public function find($CityId)
    {   
        return empty($CityId) ? null : $this->repository->find($CityId);
    }

    public function save(City $City)
    {
		$this->om->persist($City);
        $this->om->flush();
    }
	
	public function findCityLike( $term )
	{
		$query = $this->om->createQuery("
			SELECT c.name as name, c.insee as insee, c.id as id
			FROM Tisseo\EndivBundle\Entity\City c
			WHERE UPPER(c.name) LIKE UPPER(:term)
			OR UPPER(c.insee) LIKE UPPER(:term)
			ORDER BY c.name
		");
	 
		$query->setParameter('term', '%'.$term.'%');
	 
		$shs = $query->getResult();
		$array = array();
		foreach($shs as $sh) {
			$label = $sh["name"]." (".$sh["insee"].")";
			$array[] = array("name"=>$label, "id"=>$sh["id"]);
		}
		
		return $array;
	}	
	
}
