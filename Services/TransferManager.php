<?php

namespace Tisseo\EndivBundle\Services;

use Doctrine\Common\Persistence\ObjectManager;
use Tisseo\EndivBundle\Entity\Transfer;

class TransferManager extends SortManager
{
    private $om = null;
    private $repository = null;

    public function __construct(ObjectManager $om)
    {   
        $this->om = $om;
        $this->repository = $om->getRepository('TisseoEndivBundle:Transfer');
    }   

    public function findAll()
    {   
        return ($this->repository->findAll());
    }   

    public function find($TransferId)
    {   
        return empty($TransferId) ? null : $this->repository->find($TransferId);
    }

    public function save(Transfer $transfer)
    {
        $this->om->persist($transfer);
        $this->om->flush();
    }

    /**
     * @return array of tranfers ["startStopId.endStopId" => transferEntity, ...]
     */
    public function getInternalTransfer($StopArea, $startStop = null, $endStop = null) {
        $sql = 
			"SELECT
				t.id,
				ss.id as startStopId,
				es.id as endStopId,
				t.duration,
				t.distance,
				t.longName,
				t.theGeom
			FROM Tisseo\EndivBundle\Entity\Transfer t
			JOIN t.startStop ss
			JOIN t.endStop es			   
			WHERE ss.stopArea = :sa
			AND es.stopArea = :sa";
			   
		$query = $this->om->createQuery($sql)
		->setParameter('sa', $StopArea);
		$transfers = $query->getResult();
		
		$result = array();
		foreach($transfers as $transfer) {
			$key = $transfer["startStopId"].".".$transfer["endStopId"] ;
			$result[$key] = $transfer;
		}
       return $result;
    }	
	
    /**
     * @return array of tranfers ["startStopId.endStopId" => transferEntity, ...]
     */
    public function getExternalTransfer($StopArea) {
        $query = $this->om->createQuery("
               SELECT t
               FROM Tisseo\EndivBundle\Entity\Transfer t
			   JOIN t.startStop ss
			   JOIN t.endStop es			   
               WHERE (ss.stopArea = :sa AND es.stopArea != :sa)
			   OR (ss.stopArea != :sa AND es.stopArea = :sa)
        ")
		->setParameter('sa', $StopArea);
		
		$transfers = $query->getResult();
		
		$result = array();
		foreach($transfers as $transfer) {
			$key = $transfer->getStartStop()->getId().".".$transfer->getEndStop()->getId() ;
			$result[$key] = $transfer;
		}
		
       return $result;
    }	
	
    public function saveTransfers($transfers) {	
		$empty_transfert = function ($transfer) {
			return empty($transfer["duration"]) && empty($transfer["distance"]) &&
				empty($transfer["longName"]);
				//empty($transfer["longName"]) && empty($transfer["theGeom"]);
		};

		$empty_line = function ($transfer) use ($empty_transfert) {
			return empty($transfer["id"]) && $empty_transfert($transfer);
		};

		foreach($transfers as $transfer) {
			$persist = true;
			if(!$empty_line($transfer)) {
				if(!empty($transfer["id"])) {
					$transferEntity = $this->find($transfer["id"]);
					if($empty_transfert($transfer)) {
						//remove record
						$this->om->remove($transferEntity);
						$persist = false;
					}
				} else {
					//new record
					$transferEntity = new Transfer();
				}
				
				if($persist) {
					$startStop = $this->om->getRepository('TisseoEndivBundle:Stop')->find($transfer["startStopId"]);
					$endStop = $this->om->getRepository('TisseoEndivBundle:Stop')->find($transfer["endStopId"]);
					$transferEntity->setStartStop($startStop);
					$transferEntity->setEndStop($endStop);
					$transferEntity->setDuration($transfer["duration"]);
					$transferEntity->setDistance($transfer["distance"]);
					$transferEntity->setLongName($transfer["longName"]);
					//$transferEntity->setTheGeom();
					
					$this->om->persist($transferEntity);
				}
			}
		}
		$this->om->flush();
	}	
}
