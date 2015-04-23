<?php

namespace Tisseo\EndivBundle\Services;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\EntityManager;

use Doctrine\Common\Collections\ArrayCollection;
use  Doctrine\Common\Collections\Collection;

use Tisseo\EndivBundle\Entity\StopArea;
use Tisseo\EndivBundle\Entity\Transfer;


class StopAreaManager extends SortManager
{
    private $om = null;
    private $repository = null;

    public function __construct(ObjectManager $om)
    {   
        $this->om = $om;
        $this->repository = $om->getRepository('TisseoEndivBundle:StopArea');
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

    public function saveAliases(StopArea $StopArea)
    {
		$Aliases = new ArrayCollection();
		foreach ($StopArea->getAlias() as $alias) {
			$Aliases->add($alias);
		}
		
		$emptyCollection = new ArrayCollection();
		$StopArea->setAlias($emptyCollection);
		$this->om->persist($StopArea);
		$this->om->flush();
		
		foreach ($Aliases as $alias) {
			$StopArea->addAlias($alias);
		}
		
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
		")
		->setParameter('term', '%'.$term.'%');
	 
		$shs = $query->getResult();
		$array = array();
		foreach($shs as $sh) {
			$label = $sh["name"]." ".$sh["city"];
			$array[] = array("name"=>$label, "id"=>$sh["id"]);
		}
		
		return $array;
	}	
	
    public function getStopsOrderedByCode($StopArea) {
        $query = $this->om->createQuery("
               SELECT s
               FROM Tisseo\EndivBundle\Entity\Stop s
			   JOIN s.stopDatasources sd
               WHERE s.stopArea = :sa
			   ORDER BY sd.code
        ")
		->setParameter('sa', $StopArea);

        return $query->getResult();
    }	
	
    /**
     * getMainStopCityName
     * @param entity $StopArea => stop_area to check
     * @ return the name of the (first) city founded, an empty string if none
     */
    public function getMainStopCityName($StopArea) {
		if( !$StopArea->getId() ) return "";
		
        $query = $this->om->createQuery("
               SELECT c.name
               FROM Tisseo\EndivBundle\Entity\City c
               WHERE c.mainStopArea = :sa
        ")
		->setParameter('sa', $StopArea)
		->setMaxResults(1);

		$result = $query->getResult();
		if( $result ) return $result[0]["name"];
		return "";
	}
}
