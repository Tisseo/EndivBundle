<?php

namespace Tisseo\EndivBundle\Services;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\EntityManager;

use Tisseo\EndivBundle\Entity\Stop;
use Tisseo\EndivBundle\Entity\StopHistory;
use Tisseo\EndivBundle\Entity\StopAccessibility;
use Tisseo\EndivBundle\Entity\Waypoint;
use CrEOF\Spatial\PHP\Types\Geometry\Point;


class StopManager extends SortManager
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

    public function find($StopId)
    {   
        return empty($StopId) ? null : $this->repository->find($StopId);
    }

    public function save(Stop $Stop, $x = null, $y = null, $srid = null)
    {
		if(!$Stop->getId()) {
			// new stop + new stop_history
			$waypoint=new Waypoint();
			$this->om->persist($waypoint);
			$this->om->flush();
			$this->om->refresh($waypoint);
			$newId = $waypoint->getId();
			$Stop->setId($newId);
			$Stop->getStopHistories()[0]->setTheGeom(new Point($x, $y, $srid));
		}
		
		$this->om->persist($Stop);
        $this->om->flush();
		$this->om->refresh($Stop);
    }
	
	public function addStopHistory(Stop $Stop, StopHistory $StopHistory, $x, $y, $srid)
	{
		$StopHistory->setStop($Stop);
		$StopHistory->setTheGeom(new Point($x, $y, $srid));
		$Stop->addStopHistory($StopHistory);
		$this->om->persist($StopHistory);
		$this->om->persist($Stop);
		$this->om->flush();
	}

	
	public function removeStopHistory(Stop $Stop, $StopHistoryId)
	{
		foreach ($Stop->getStopHistories() as $sh) {
			if($sh->getId() == $StopHistoryId) {
				$Stop->removeStopHistory($sh);
				$this->om->remove($sh);
				$this->om->persist($Stop);
				$this->om->flush();
				break;
			}
		}
	}
	
	public function addAccessibility(Stop $Stop, StopAccessibility $StopAccessibility)
	{
		$StopAccessibility->setStop($Stop);
		$Stop->addStopAccessibility($StopAccessibility);
		$this->om->persist($StopAccessibility);
		$this->om->persist($Stop);
		$this->om->flush();
	}

	public function removeStopAccessibility(Stop $Stop, $StopAccessibilityId)
	{
		foreach ($Stop->getStopAccessibilities() as $sa) {
			if($sa->getId() == $StopAccessibilityId) {
				$Stop->removeStopAccessibility($sa);
				$this->om->remove($sa);
				$this->om->persist($Stop);
				$this->om->flush();
				break;
			}			
		}
	}
	
	public function findStopsLike( $term, $limit = 10 )
	{
		$query = $this->om->createQuery("
			SELECT sh.shortName as name, c.name as city,  sd.code as code, s.id as id
			FROM Tisseo\EndivBundle\Entity\StopHistory sh
			JOIN sh.stop s
			JOIN s.stopArea sa
			JOIN sa.city c
			JOIN s.stopDatasources sd
			WHERE UPPER(sh.shortName) LIKE UPPER(:term)
			OR UPPER(sh.longName) LIKE UPPER(:term)
			OR UPPER(sd.code) LIKE UPPER(:term)
			ORDER BY sh.shortName, c.name, sd.code
		");
	 
		$query->setParameter('term', '%'.$term.'%');
	 
		$shs = $query->getResult();
		$array = array();
		foreach($shs as $sh) {
			$label = $sh["name"]." ".$sh["city"]." (".$sh["code"].")";
			$array[] = array("name"=>$label, "id"=>$sh["id"]);
		}
		
		return $array;
	}	

	public function getStopLabel( Stop $stop )
	{
		$query = $this->om->createQuery("
			SELECT sh.shortName as name, c.name as city,  sd.code as code, s.id as id
			FROM Tisseo\EndivBundle\Entity\StopHistory sh
			JOIN sh.stop s
			JOIN s.stopArea sa
			JOIN sa.city c
			JOIN s.stopDatasources sd
			WHERE sh.stop = :stop
		");
	 
		$query->setParameter('stop', $stop);
	 
		$sh = $query->getResult();
		$label = $sh[0]["name"]." ".$sh[0]["city"]." (".$sh[0]["code"].")";
		return $label;
	}

    public function getStopsByRoute($id) {
        $query = $this->om->createQuery("
               SELECT rs.id, rs.rank, wp.id as waypoint
               FROM Tisseo\EndivBundle\Entity\RouteStop rs
               JOIN rs.waypoint wp
               WHERE rs.route = :id
        ")->setParameter('id', $id);

        return $query->getResult();

    }



	public function getCurrentStopHistory( $stop )
	{
		$query = $this->om->createQuery("
			SELECT sh
			FROM Tisseo\EndivBundle\Entity\StopHistory sh
			JOIN sh.stop s
			WHERE sh.stop = :stop
			AND sh.startDate <= CURRENT_DATE()
			AND (sh.endDate IS NULL or sh.endDate >= CURRENT_DATE())
		");
		$query->setParameter('stop', $stop);
		
		return $query->getResult()[0];
	}
}
