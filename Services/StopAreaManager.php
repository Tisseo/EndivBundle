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
    private $em = null;
    private $repository = null;

    public function __construct(EntityManager $em)
    {   
        $this->em = $em;
        $this->repository = $em->getRepository('TisseoEndivBundle:StopArea');
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
		$this->em->persist($StopArea);
        $this->em->flush();
    }

    public function saveAliases(StopArea $StopArea, $originalAliases)
    {
		foreach ($originalAliases as $alias) {
			if (false === $StopArea->getAlias()->contains($alias)) {
				$StopArea->removeAlias($alias);
				$this->em->remove($alias);
			}
		}

		$this->em->persist($StopArea);
        $this->em->flush();
    }

	
	public function findStopAreasLike( $term, $limit = 10 )
	{
		$specials = array("-", " ", "'");
		$cleanTerm = str_replace($specials, "_", $term);
		
		$connection = $this->em->getConnection()->getWrappedConnection();
		$stmt = $connection->prepare("
			SELECT DISTINCT sa.short_name as name, c.name as city, sa.id as id
			FROM stop_area sa
			JOIN city c on c.id = sa.city_id
			LEFT JOIN alias a on a.stop_area_id = sa.id
			WHERE UPPER(unaccent(sa.short_name)) LIKE UPPER(unaccent(:term))
			OR UPPER(unaccent(sa.long_name)) LIKE UPPER(unaccent(:term))
			OR UPPER(unaccent(a.name)) LIKE UPPER(unaccent(:term))
			ORDER BY sa.short_name, c.name
		");
		$stmt->bindValue(':term', '%'.$cleanTerm.'%');
		$stmt->execute();
		$shs = $stmt->fetchAll();
		
		$array = array();
		foreach($shs as $sh) {
			$label = $sh["name"]." ".$sh["city"];
			$array[] = array("name"=>$label, "id"=>$sh["id"]);
		}
		
		return $array;
	}	
	
    public function getStopsOrderedByCode($StopArea) {
        $query = $this->em->createQuery("
               SELECT s
               FROM Tisseo\EndivBundle\Entity\Stop s
			   JOIN s.stopDatasources sd
               WHERE s.stopArea = :sa
			   ORDER BY sd.code
        ")
		->setParameter('sa', $StopArea);

        return $query->getResult();
    }	

    public function getCurrentStops($StopArea) {
        $query = $this->em->createQuery("
               SELECT s
               FROM Tisseo\EndivBundle\Entity\Stop s
			   JOIN s.stopDatasources sd
			   JOIN s.stopHistories sh
               WHERE s.stopArea = :sa
			   AND (sh.startDate <= CURRENT_DATE()
			   AND (sh.endDate IS NULL or sh.endDate >= CURRENT_DATE()))
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
		
        $query = $this->em->createQuery("
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
	
	public function getLines($stopArea)
	{
        $query = $this->em->createQuery("
               SELECT l.number as number , bc.html as bgColor, fc.html as fgColor, s.id
               FROM Tisseo\EndivBundle\Entity\LineVersion lv
			   JOIN lv.line l
			   JOIN lv.bgColor bc
			   JOIN lv.fgColor fc
			   JOIN lv.routes r
			   JOIN r.routeStops rs
			   JOIN rs.waypoint w
			   JOIN w.stop s
               WHERE (lv.endDate IS NULL or lv.endDate >= CURRENT_DATE())
			   AND s.stopArea = :sa
        ")
		->setParameter('sa', $stopArea);
		
		$array = $query->getResult();
		$result = array();
		foreach($array as $item) {
			$line = [
					"number"=> $item["number"],
					"bgColor" => $item["bgColor"],
					"fgColor" => $item["fgColor"]
			];
			
			if( !isset($result[$item["id"]]) || !in_array($line, $result[$item["id"]]) ) 
				$result[$item["id"]][] = $line;
		}
		return $result;
	}
}
