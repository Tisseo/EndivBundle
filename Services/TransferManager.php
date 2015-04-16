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
