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
    private $em = null;
    private $repository = null;

    public function __construct(EntityManager $em)
    {   
        $this->em = $em;
        $this->repository = $em->getRepository('TisseoEndivBundle:Stop');
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
		if( !$Stop->getId() ) {
			// new waypoint + new stop + new stop_history
			$connection = $this->em->getConnection()->getWrappedConnection();
			$stmt = $connection->prepare("
				INSERT INTO waypoint(id) VALUES (nextval('waypoint_id_seq')) RETURNING waypoint.id
			");
			$stmt->execute();
			$newId = $stmt->fetch(\PDO::FETCH_ASSOC)["id"];

			$Stop->setId($newId);
			$Stop->getStopHistories()[0]->setTheGeom(new Point($x, $y, $srid));
		}

		$masterStop = $Stop->getMasterStop();
		if( $masterStop && $Stop->getId() ) {
			// supprimer les stop_accessibility
			foreach($Stop->getStopAccessibilities() as $sa) {
				$this->em->remove($sa);
			}
			// close stop histories
			$date = new \DateTime('NOW');
			$this->closeStopHistories($Stop, $date);
			//clone current master stop stop history 
			$currentSH = $this->getCurrentStopHistory($masterStop);
			if ( $currentSH ) {
				$newStartDate = clone $date;
				$newStartDate->add(new \DateInterval('P1D'));
				
				$newSH = new StopHistory();
				$newSH->setStartDate($newStartDate);
				$newSH->setEndDate($currentSH->getEndDate());
				$newSH->setShortName($currentSH->getShortName());
				$newSH->setLongName($currentSH->getLongName());
				$newSH->setTheGeom($currentSH->getTheGeom());
				$newSH->setStop($Stop);
				$Stop->addStopHistory($newSH);
				$this->em->persist($newSH);
			}

			//clone master stop future stop histories 
			$futureStopHistories = $this->getFutureStopHistories($masterStop, $date);
			foreach($futureStopHistories as $sh) {
				$newSH = new StopHistory();
				$newSH->setStartDate($sh->getStartDate());
				$newSH->setEndDate($sh->getEndDate());
				$newSH->setShortName($sh->getShortName());
				$newSH->setLongName($sh->getLongName());
				$newSH->setTheGeom($sh->getTheGeom());
				$newSH->setStop($Stop);
				$Stop->addStopHistory($newSH);
				$this->em->persist($newSH);
			}
		}
			
		$this->em->persist($Stop);
        $this->em->flush();
    }
	
	public function closeStopHistories(Stop $Stop, $closingDate)
	{
		
		if( gettype($closingDate) == "string" )
			$closingDateObject = \DateTime::createFromFormat('d/m/Y', $closingDate);
		else
			$closingDateObject = \DateTime::createFromFormat('d/m/Y', $closingDate->format('d/m/Y'));
			
		//"close" current stop_history
		$currentStopHistory = $this->getCurrentStopHistory($Stop);
		$endDate = $currentStopHistory->getEndDate();
		if( $endDate == null or strtotime($endDate->format('Ymd')) > strtotime($closingDateObject->format('Ymd')) ) {
			$currentStopHistory->setEndDate($closingDateObject);
			$this->em->persist($currentStopHistory);
		}
		
		//remove future stop_history
		$futureStopHistories = $this->getFutureStopHistories($Stop, $closingDateObject->format("Y-m-d H:i:s"));
		foreach($futureStopHistories as $sh) {
			$this->em->remove($sh);
		}
	}

	public function closeStop(Stop $Stop, $closingDate)
	{
		$this->closeStopHistories($Stop, $closingDate);
		$this->em->flush();
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
						$this->em->persist($sh);
						break;
				} else {
					if( strtotime($endDate->format('Ymd')) >= strtotime($lastDay->format('Ymd')) ) {
						$sh->setEndDate($lastDay);
						$this->em->persist($sh);
						break;
					} else {
						// end date already lower
						break;
					}
				}
			}
		}

		//remove future stop_history
		$futureStopHistories = $this->getFutureStopHistories($Stop, $StopHistory->getStartDate()->format("Y-m-d H:i:s"));
		foreach($futureStopHistories as $sh) {
			$this->em->remove($sh);
		}

		$StopHistory->setStop($Stop);
		$StopHistory->setTheGeom(new Point($x, $y, $srid));
		$Stop->addStopHistory($StopHistory);
		$this->em->persist($StopHistory);
		$this->em->persist($Stop);
		$this->em->flush();
	}

	
	public function removeStopHistory(Stop $stop, $StopHistoryId)
	{
		
		$query = $this->em->createQuery("
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
			$this->em->remove($stopHistory);
			
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
							$this->em->persist($sh);
							break;
						}
					} else {
						// stop history endDate already to null
						break;
					}
				}
			}
			
			$this->em->flush();
		}
	}
	
	public function addAccessibility(Stop $Stop, StopAccessibility $StopAccessibility)
	{
		$StopAccessibility->setStop($Stop);
		$Stop->addStopAccessibility($StopAccessibility);
		$this->em->persist($StopAccessibility);
		$this->em->persist($Stop);
		$this->em->flush();
	}

	public function removeStopAccessibility(Stop $Stop, $StopAccessibilityId)
	{
		foreach ($Stop->getStopAccessibilities() as $sa) {
			if($sa->getId() == $StopAccessibilityId) {
				$Stop->removeStopAccessibility($sa);
				$this->em->remove($sa);
				$this->em->persist($Stop);
				$this->em->flush();
				break;
			}			
		}
	}
	
	public function findStopsLike( $term, $limit = 10 )
	{
		$connection = $this->em->getConnection()->getWrappedConnection();
		$stmt = $connection->prepare("
			SELECT sh.short_name as name, c.name as city,  sd.code as code, s.id as id
			FROM stop_history sh
			JOIN stop s on sh.stop_id = s.id
			JOIN stop_area sa on sa.id = s.stop_area_id
			JOIN city c on c.id = sa.city_id
			JOIN stop_datasource sd on sd.stop_id = s.id
			WHERE (UPPER(unaccent(sh.short_name)) LIKE UPPER(unaccent(:term))
			OR UPPER(unaccent(sh.long_name)) LIKE UPPER(unaccent(:term))
			OR UPPER(unaccent(sd.code)) LIKE UPPER(unaccent(:term)))
			AND sh.start_date <= current_date
			AND (sh.end_date IS NULL or sh.end_date >= current_date)
			ORDER BY sh.short_name, c.name, sd.code		
		");
		$stmt->bindValue(':term', '%'.$term.'%');
		$stmt->execute();
		$shs = $stmt->fetchAll();

		$array = array();
		foreach($shs as $sh) {
			$label = $sh["name"]." ".$sh["city"]." (".$sh["code"].")";
			$array[] = array("name"=>$label, "id"=>$sh["id"]);
		}
		
		return $array;
	}

    public function getStopsByRoute($id) {

            $query = $this->em->createQuery("
               SELECT rs.id, rs.rank, rs.pickup, rs.dropOff, wp.id as waypoint
               FROM Tisseo\EndivBundle\Entity\RouteStop rs
               JOIN rs.waypoint wp
               WHERE rs.route = :id
               ORDER BY rs.rank
        ")->setParameter('id' , $id);


        return $query->getResult();

    }

    public function getStops($idWaypoint){

        $query = $this->em->createQuery("
               SELECT st.id, ar.shortName, ar.id as zone,  c.name as city
               FROM Tisseo\EndivBundle\Entity\Stop st
               JOIN st.stopArea ar
               JOIN ar.city c
               WHERE st.id = :id
        ")->setParameter('id', $idWaypoint);


        return $query->getResult();
    }


    public function getStopArea($id) {
        $query = $this->em->createQuery("
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
		$query = $this->em->createQuery("
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
		if( !$sh ) return "";
		
		$label = $sh[0]["name"]." ".$sh[0]["city"]." (".$sh[0]["code"].")";
		return $label;
	}

	public function getCurrentStopHistory( $stop )
	{		
		$query = $this->em->createQuery("
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
		$query = $this->em->createQuery("
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
		$query = $this->em->createQuery("
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
