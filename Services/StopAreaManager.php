<?php

namespace Tisseo\EndivBundle\Services;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\EntityManager;

use Tisseo\EndivBundle\Entity\StopArea;


class StopAreaManager extends SortManager
{
    private $om = null;
    private $repository = null;

    public function __construct(ObjectManager $om)
    {   
        $this->om = $om;
        $this->repository = $om->getRepository('TisseoEndivBundle:Stop');
    }   

    public function findAll()
    {   
        return ($this->repository->findAll());
    }   

    public function find($StopAreaId)
    {   
        return empty($StopAreaId) ? null : $this->repository->find($StopAreaId);
    }

    public function save(StopArea $StopArea)
    {
		$this->om->persist($StopArea);
        $this->om->flush();
    }
	
	public function findStopAreasLike( $term, $limit = 10 )
	{
		$query = $this->om->createQuery("
			SELECT sa.shortName as name, c.name as city, sa.id as id
			FROM Tisseo\EndivBundle\Entity\StopArea sa
			JOIN sa.city c
			WHERE UPPER(sa.shortName) LIKE UPPER(:term)
			OR UPPER(sa.longName) LIKE UPPER(:term)
			ORDER BY sa.shortName, c.name
		");
	 
		$query->setParameter('term', '%'.$term.'%');
	 
		$shs = $query->getResult();
		$array = array();
		foreach($shs as $sh) {
			$label = $sh["name"]." ".$sh["city"];
			$array[] = array("name"=>$label, "id"=>$sh["id"]);
		}
		
		return $array;
	}	
	
}
