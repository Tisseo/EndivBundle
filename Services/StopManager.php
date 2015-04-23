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
	
	public function closeStop(Stop $Stop, $closingDate)
	{
		//"close" current stop_history
		$currentStopHistory = $this->getCurrentStopHistory($Stop);
		$closingDateObject = \DateTime::createFromFormat('d/m/Y', $closingDate);
		$endDate = $currentStopHistory->getEndDate();
		if( $endDate == null or strtotime($endDate->format('Ymd')) > strtotime($closingDateObject->format('Ymd')) ) {
			$currentStopHistory->setEndDate($closingDateObject);
			$this->om->persist($currentStopHistory);
		}
		
		//remove future stop_history
		$futureStopHistories = $this->getFutureStopHistories($Stop, $closingDateObject->format("Y-m-d H:i:s"));
		foreach($futureStopHistories as $sh) {
			$this->om->remove($sh);
		}
		$this->om->flush();
	}


	public function addStopHistory(Stop $Stop, StopHistory $StopHistory, $x, $y, $srid)
	{
		$currentStopHistory = $this->getCurrentStopHistory($Stop);
		$lastDay = clone  $StopHistory->getStartDate();
		$lastDay->sub(new \DateInterval('P1D'));

		$stopHistories = $Stop->getStopHistories();
		$iter = $stopHistories->getIterator();
		//reverse sort for iteration
		$iter->uasort(function($a, $b) {
			$startDate_a = $a->getStartDate();
			$startDate_b = $b->getStartDate();
			return $startDate_a == $startDate_b ? 0 : strtotime($startDate_a->format('Ymd')) < strtotime($startDate_b->format('Ymd')) ? 1 : - 1;
		});
		// update if necessary previous stop history (end date => previous day)
		foreach ($iter as $sh) {
			$startDate = $sh->getStartDate();
			if( strtotime($startDate->format('Ymd')) <= strtotime($lastDay->format('Ymd')) ) {
				$endDate = $sh->getEndDate();
				if( $endDate == null ) {
						$sh->setEndDate($lastDay);
						$this->om->persist($sh);
						break;
				} else {
					if( strtotime($endDate->format('Ymd')) >= strtotime($lastDay->format('Ymd')) ) {
						$sh->setEndDate($lastDay);
						$this->om->persist($sh);
						break;
					} else {
						// end date already lower
						break;
					}
				}
			}
		}
/*		
		//"close" current stop_history
		$currentStopHistory = $this->getCurrentStopHistory($Stop);
		$lastDay = clone  $StopHistory->getStartDate();
		$lastDay->sub(new \DateInterval('P1D'));
		$endDate = $currentStopHistory->getEndDate(); 
		if( $endDate == null or strtotime($endDate->format('Ymd')) > strtotime($lastDay->format('Ymd')) ) {
			$currentStopHistory->setEndDate($lastDay);
			$this->om->persist($currentStopHistory);
		}
*/		
		//remove future stop_history
		$futureStopHistories = $this->getFutureStopHistories($Stop, $StopHistory->getStartDate()->format("Y-m-d H:i:s"));
		foreach($futureStopHistories as $sh) {
			$this->om->remove($sh);
		}

		$StopHistory->setStop($Stop);
		$StopHistory->setTheGeom(new Point($x, $y, $srid));
		$Stop->addStopHistory($StopHistory);
		$this->om->persist($StopHistory);
		$this->om->persist($Stop);
		$this->om->flush();
	}

	
	public function removeStopHistory(Stop $stop, $StopHistoryId)
	{
		
		$query = $this->om->createQuery("
			SELECT sh
			FROM Tisseo\EndivBundle\Entity\StopHistory sh
			WHERE sh.id = :id
		")
		->setParameter('id', $StopHistoryId);
		$datas = $query->getResult();
		if($datas) {
			$stopHistory = $datas[0];
			$newLastDate = clone $stopHistory->getStartDate();
			$newLastDate->sub(new \DateInterval('P1D'));	//get previous day
			$stop->removeStopHistory($stopHistory);
			$this->om->remove($stopHistory);
			
			$stopHistories = $stop->getStopHistories();
			$iter = $stopHistories->getIterator();
			//reverse sort for iteration
			$iter->uasort(function($a, $b) {
				$startDate_a = $a->getStartDate();
				$startDate_b = $b->getStartDate();
				return $startDate_a == $startDate_b ? 0 : strtotime($startDate_a->format('Ymd')) < strtotime($startDate_b->format('Ymd')) ? 1 : - 1;
			});
			
			// update if necessary previous stop history (end date => null)
			foreach ($iter as $sh) {
				$startDate = $sh->getStartDate();
				if( strtotime($startDate->format('Ymd')) <= strtotime($newLastDate->format('Ymd')) ) {
					$endDate = $sh->getEndDate();
					if( $endDate !== null ) {
						if( strtotime($endDate->format('Ymd')) <= strtotime($newLastDate->format('Ymd')) ) {
							$sh->setEndDate(null);
							$this->om->persist($sh);
							break;
						}
					} else {
						// stop history endDate already to null
						break;
					}
				}
			}
			
			$this->om->flush();
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
			WHERE (UPPER(sh.shortName) LIKE UPPER(:term)
			OR UPPER(sh.longName) LIKE UPPER(:term)
			OR UPPER(sd.code) LIKE UPPER(:term))
			AND sh.startDate <= CURRENT_DATE()
			AND (sh.endDate IS NULL or sh.endDate >= CURRENT_DATE())
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

    public function getStopsByRoute($id) {

            $query = $this->om->createQuery("
               SELECT rs.id, rs.rank, rs.pickup, rs.dropOff, wp.id as waypoint
               FROM Tisseo\EndivBundle\Entity\RouteStop rs
               JOIN rs.waypoint wp
               WHERE rs.route = :id
               ORDER BY rs.rank
        ")->setParameter('id' , $id);


        return $query->getResult();

    }

    public function getStops($idWaypoint){

        $query = $this->om->createQuery("
               SELECT st.id, ar.shortName, ar.id as zone,  c.name as city
               FROM Tisseo\EndivBundle\Entity\Stop st
               JOIN st.stopArea ar
               JOIN ar.city c
               WHERE st.id = :id
        ")->setParameter('id', $idWaypoint);


        return $query->getResult();
    }


    public function getStopArea($id) {
        $query = $this->om->createQuery("
               SELECT ar.id, ar.rank, wp.id as waypoint, c.id as city
               FROM Tisseo\EndivBundle\Entity\StopArea ar
               JOIN ar.waypoint wp
               JOIN ar.city c
               WHERE ar.route = :id
        ")->setParameter('id', $id);

        return $query->getResult();

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
			AND sh.startDate <= CURRENT_DATE()
			AND (sh.endDate IS NULL or sh.endDate >= CURRENT_DATE())
		")
		->setParameter('stop', $stop);
	 
		$sh = $query->getResult();
		$label = $sh[0]["name"]." ".$sh[0]["city"]." (".$sh[0]["code"].")";
		return $label;
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
		
		//stops have ONLY ONE current stop history!
		return $query->getResult()[0];
	}

	public function getFutureStopHistories( $stop, $startDate )
	{		
		$query = $this->om->createQuery("
			SELECT sh
			FROM Tisseo\EndivBundle\Entity\StopHistory sh
			JOIN sh.stop s
			WHERE sh.stop = :stop
			AND sh.startDate > :date
		");
		$query->setParameter('stop', $stop);
		$query->setParameter('date', $startDate);
		
		return $query->getResult();
	}
	
	public function getStopHistoriesOrderByDate( $stop )
	{		
		$query = $this->om->createQuery("
			SELECT sh
			FROM Tisseo\EndivBundle\Entity\StopHistory sh
			JOIN sh.stop s
			WHERE sh.stop = :stop
			ORDER BY sh.startDate
		");
		$query->setParameter('stop', $stop);
		
		return $query->getResult();
	}	
}
