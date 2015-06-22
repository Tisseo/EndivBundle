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
    public function getInternalTransfer($StopArea) {
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
            $key = $transfer["startStopId"].".".$transfer["endStopId"];
            $result[$key] = $transfer;
        }
       return $result;
    }

    /**
     * @return array of tranfers ["startStopId.endStopId" => transferEntity, ...]
     */
    public function getExternalTransfer($StopArea) {
        $stopManager = $this->om->getRepository('TisseoEndivBundle:Stop');
        $query = $this->om->createQuery(
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
            WHERE (ss.stopArea = :sa AND es.stopArea != :sa)
            OR (ss.stopArea != :sa AND es.stopArea = :sa)
        ")
        ->setParameter('sa', $StopArea);
        $transfers = $query->getResult();

        $result = array();
        foreach($transfers as $transfer) {
            $key = $transfer["startStopId"].".".$transfer["endStopId"];
            $startStop = $stopManager->find($transfer["startStopId"]);
            $transfer["startStopLabel"] = $startStop->getStopLabel();
            $endStop = $stopManager->find($transfer["endStopId"]);
            $transfer["endStopLabel"] = $endStop->getStopLabel();
            $result[$key] = $transfer;
        }

       return $result;
    }

    public function saveInternalTransfers($transfers) {
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
                    $this ->saveStopTransfer($transferEntity, $transfer);
                }
            }
        }
        $this->om->flush();
    }

    public function saveExternalTransfers($currentStopArea, $transfers) {
        $empty_transfert = function ($transfer) {
            return empty($transfer["duration"]) && empty($transfer["distance"]) &&
                empty($transfer["longName"]);
        };
        $empty_line = function ($transfer) use ($empty_transfert) {
            return empty($transfer["id"]) && $empty_transfert($transfer);
        };
        $stopAreaManager = $this->om->getRepository('TisseoEndivBundle:StopArea');

        $existingTransfers = $this->getExternalTransfer($currentStopArea);

        $transfers_to_save = array();
        foreach( $transfers as $transfer ) {
            if( !$empty_line($transfer) ) {
                if( empty($transfer["startStopId"]) ) {
                    if( $transfer["endStopType"] == "stop" ) {
                        //stop_area -> stop
                        foreach($currentStopArea->getStops() as $stop) {
                            $transfer_tmp = $transfer;
                            $transfer_tmp["startStopId"] = $stop->getId();
                            $transfers_to_save[] = $transfer_tmp;
                        }
                    } else {
                        //stop_area -> stop_area
                        foreach($currentStopArea->getStops() as $startStop) {
                            $endStopArea = $stopAreaManager->find($transfer["endStopId"]);
                            foreach($endStopArea->getStops() as $endStop) {
                                $transfer_tmp = $transfer;
                                $transfer_tmp["startStopId"] = $startStop->getId();
                                $transfer_tmp["endStopId"] = $endStop->getId();
                                $transfers_to_save[] = $transfer_tmp;
                            }
                        }
                    }
                } else {
                    if( $transfer["endStopType"] != "stop" ) {
                        //stop -> stop_area
                        $stopArea = $stopAreaManager->find($transfer["endStopId"]);
                        foreach($stopArea->getStops() as $endStop) {
                            $transfer_tmp = $transfer;
                            $transfer_tmp["endStopId"] = $endStop->getId();
                            $transfers_to_save[] = $transfer_tmp;
                        }
                    } else {
                        //stop -> stop
                        $transfer_tmp = $transfer;
                        $transfers_to_save[] = $transfer_tmp;
                    }
                }
            }
        }

        $ignored_transfers = 0;
        foreach( $transfers_to_save as $transfer ) {
            if( empty($transfer["id"]) ) {
                //new transfer
                if (array_key_exists($transfer["startStopId"].".".$transfer["endStopId"], $existingTransfers)) {
                    $ignored_transfers += 1;
                } else {
                    $this ->saveNewExternalTransfer($transfer);
                }
            } else {
                //updating existing transfer
                $entity = $this->find($transfer["id"]);
                $this ->saveStopTransfer($entity, $transfer);
            }
        }

        $this->om->flush();
        return $ignored_transfers;
    }

    /**
     * saveNewExternalTransfer
     * @param associative array $transfer => datas
     *
     * save a stop to stop transfer => save transfer in both direction
     * persist but doesn't flush
     */
    public function saveNewExternalTransfer($transfer) {
        $transferAller = new Transfer();
        $this ->saveStopTransfer($transferAller, $transfer);

        $transferRetour = new Transfer();
        $tmp = $transfer["startStopId"];
        $transfer["startStopId"] = $transfer["endStopId"];
        $transfer["endStopId"] =$tmp;
        $this ->saveStopTransfer($transferRetour, $transfer);
    }

    /**
     * saveStopTransfer
     * @param entity $entity => Transfer entity to save
     * @param associative array $transfer => datas
     *
     * save a stop to stop transfer
     * persist but doesn't flush
     */
    public function saveStopTransfer($entity, $transfer) {
        $startStop = $this->om->getRepository('TisseoEndivBundle:Stop')->find($transfer["startStopId"]);
        $endStop = $this->om->getRepository('TisseoEndivBundle:Stop')->find($transfer["endStopId"]);
        $entity->setStartStop($startStop);
        $entity->setEndStop($endStop);

        $transfer["duration"] ? $value = $transfer["duration"] : $value = null;
        $entity->setDuration($value);
        $transfer["distance"] ? $value = $transfer["distance"] : $value = null;
        $entity->setDistance($value);
        $entity->setLongName($transfer["longName"]);
        //$entity->setTheGeom($transfer["theGeom"]);

        $this->om->persist($entity);
    }
}
