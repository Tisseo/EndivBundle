<?php

namespace Tisseo\EndivBundle\Services;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\EntityManager;

use Tisseo\EndivBundle\Entity\Stop;
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

    public function save(Stop $Stop)
    {
		if(!$Stop->getId()) {
			$waypoint=new Waypoint();
			$this->om->persist($waypoint);
			$this->om->flush();
			$this->om->refresh($waypoint);
			$newId = $waypoint->getId();
			$Stop->setId($newId);
			$Stop->getStopHistories()[0]->setTheGeom(new Point(1, 1, 3943));
		}
		
		$this->om->persist($Stop);
        $this->om->flush();
		$this->om->refresh($Stop);
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

	public function getStopLabel( $stop )
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
}
