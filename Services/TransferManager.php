<?php

namespace Tisseo\EndivBundle\Services;

use Doctrine\Common\Persistence\ObjectManager;
use Tisseo\EndivBundle\Entity\Transfer;
use Tisseo\EndivBundle\Services\StopAreaManager;
use Tisseo\EndivBundle\Services\StopManager;

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
     * @return an array with all Transfer entities for this stopArea
     * Transfers that don't already exist are created
     */
    public function getInternalTransfers($stopArea) {
        $sql =
            "SELECT t
            FROM Tisseo\EndivBundle\Entity\Transfer t
            JOIN t.startStop ss
            JOIN t.endStop es
            WHERE ss.stopArea = :sa
            AND es.stopArea = :sa";

        $query = $this->om->createQuery($sql)
        ->setParameter('sa', $stopArea);
        $existingTransfers = $query->getResult();

        $stops = (new StopAreaManager($this->om))->getStopsOrderedByCode($stopArea);
        $transfers = array();
        foreach ($stops as $startStop)
        {
            foreach ($stops as $endStop)
            {
                $existing = array_filter(
                    $existingTransfers,
                    function ($object) use ($startStop, $endStop) {
                        return ($object->getStartStop() == $startStop and $object->getEndStop() == $endStop);
                    }
                );
                if (empty($existing))
                {
                    $transfer = new Transfer();
                    $transfer->setStartStop($startStop);
                    $transfer->setEndStop($endStop);
                    $transfers[] = $transfer;
                }
                else
                {
                    $transfers[] = array_values($existing)[0];
                }
            }
        }
        return $transfers;
    }

    /**
     * @return array of tranfers
     */
    public function getExternalTransfers($StopArea) {
        $query = $this->om->createQuery(
            "SELECT t
            FROM Tisseo\EndivBundle\Entity\Transfer t
            JOIN t.startStop ss
            JOIN t.endStop es
            JOIN ss.stopHistories sssh
            JOIN es.stopHistories essh
            JOIN ss.stopDatasources sssd
            JOIN es.stopDatasources essd
            WHERE (ss.stopArea = :sa AND es.stopArea != :sa)
            OR (ss.stopArea != :sa AND es.stopArea = :sa)
            ORDER BY sssd.code, essd.code
        ")
        ->setParameter('sa', $StopArea);
        $transfers = $query->getResult();

        return $transfers;
    }

    public function createExternalTransfers($data, $stopArea) {
        $duration = $data['duration'];
        $distance = $data['distance'];
        $startStopId = $data['startStopId'];
        $endStopId = $data['endStopId'];
        $startStopType = $data['startStopType'];
        $endStopType = $data['endStopType'];

        if (empty($duration) || !is_numeric($duration) || $duration < 0 || ($duration > 60 && $duration != 99)){
            throw new \Exception("a transfer's duration must be a positive integer <= 60 or == 99 ");
        }
        if (!empty($distance) && (!is_numeric($distance) || $distance < 0)){
            throw new \Exception("a transfer's distance must be a positive integer");
        }
        if (empty($startStopId) || empty($endStopId) || empty($startStopType) || empty($endStopType))
        {
            throw new \Exception("error: stop's id and type are needed");
        }
        $stopAreaManager = (new StopAreaManager($this->om));
        $stopManager = (new StopManager($this->om));
        $startIsStopArea = ($data['startStopType'] == 'sa');
        $endIsStopArea = ($data['endStopType'] == 'sa');
        $startIsInternal = false;
        $endIsInternal = false;

        if ($startIsStopArea){
            $startStopArea = $stopAreaManager->find($data['startStopId']);
            if (is_null($startStopArea))
                throw new \Exception('stop area with id ' . $endStopId . ' not found');
            if ($startStopArea == $stopArea)
                $startIsInternal = true;
            $startStops = $stopAreaManager->getStopsOrderedByCode($startStopArea);
        }
        else{
            $stop = $stopManager->find($data['startStopId']);
            if (is_null($stop))
                throw new \Exception('stop with id ' . $endStopId . ' not found');
            if ($stop->getStopArea() == $stopArea)
                $startIsInternal = true;
            $startStops = array();
            $startStops[] = $stop;
        }

        if ($endIsStopArea){
            $endStopArea = $stopAreaManager->find($data['endStopId']);
            if (is_null($endStopArea))
                throw new \Exception('stop area with id ' . $endStopId . ' not found');
            if ($endStopArea == $stopArea)
                $endIsInternal = true;
            $endStops = $stopAreaManager->getStopsOrderedByCode($endStopArea);
        }
        else{
            $stop = $stopManager->find($data['endStopId']);
            if (is_null($stop))
                throw new \Exception('stop with id ' . $endStopId . ' not found');
            if ($stop->getStopArea() == $stopArea)
                $endIsInternal = true;
            $endStops = array();
            $endStops[] = $stop;
        }
        if (!($startIsInternal xor $endIsInternal))
            throw new \Exception("only start OR end stop must belong to stop_area for an external transfer");

        $transfers = array();
        foreach ($startStops as $startStop)
        {
            foreach ($endStops as $endStop)
            {
                $transfer = new Transfer();
                $transfer->setStartStop($startStop);
                $transfer->setEndStop($endStop);
                if (!empty($data['duration']))
                    $transfer->setDuration($data['duration'] * 60);
                if (!empty($data['distance']))
                    $transfer->setDistance($data['distance']);
                if (!empty($data['longName']))
                    $transfer->setLongName($data['longName']);

                $inversedTransfer = clone $transfer;
                $inversedTransfer->setStartStop($endStop);
                $inversedTransfer->setEndStop($startStop);

                $transfers[] = $transfer;
                $transfers[] = $inversedTransfer;
            }
        }

        return $transfers;
    }

    public function saveInternalTransfers($transfers, $stopArea)
    {
        $this->validateTransfers($transfers, $stopArea, false);
        $sql =
            "SELECT t
            FROM Tisseo\EndivBundle\Entity\Transfer t
            JOIN t.startStop ss
            JOIN t.endStop es
            WHERE ss.stopArea = :sa
            AND es.stopArea = :sa";

        $query = $this->om->createQuery($sql)
        ->setParameter('sa', $stopArea);
        $existingTransfers = $query->getResult();

        $this->updateTransfers($existingTransfers, $transfers);
    }

    public function saveExternalTransfers($transfers, $stopArea)
    {
        $this->validateTransfers($transfers, $stopArea, true);
        $existingTransfers = $this->getExternalTransfers($stopArea);
        $this->updateTransfers($existingTransfers, $transfers);
    }

    public function updateTransfers($existingTransfers, $transfers) {
        $sync = false;
        foreach ($existingTransfers as $transfer)
        {

            $existing = array_filter(
                $transfers,
                function ($object) use ($transfer) {
                    return (!empty($object['id']) && $object['id'] == $transfer->getId());
                }
            );

            if (empty($existing))
            {
                $sync = true;
                $this->om->remove($transfer);
            }
        }

        $stopManager = new StopManager($this->om);
        foreach ($transfers as $transfer)
        {
            if (empty($transfer['id']))
            {
                $sync = true;
                $newTransfer = new Transfer();
                $newTransfer->setDuration($transfer['duration'] * 60);
                if (!empty($transfer['distance']))
                    $newTransfer->setDistance($transfer['distance']);
                if (!empty($transfer['longName']))
                    $newTransfer->setLongName($transfer['longName']);
                $startStop = $stopManager->find($transfer['startStopId']);
                $endStop = $stopManager->find($transfer['endStopId']);
                $newTransfer->setStartStop($startStop);
                $newTransfer->setEndStop($endStop);
                $this->om->persist($newTransfer);
            }
            else {
                $existingTransfer = $this->find($transfer['id']);
                $duration = null;
                $distance = null;
                $longName = null;
                if (!empty($transfer['duration']))
                    $duration = $transfer['duration'] * 60;
                if (!empty($transfer['distance']))
                    $distance = (int)$transfer['distance'];
                if (!empty($transfer['longName']))
                    $longName = $transfer['longName'];
                if (!is_null($duration) && $existingTransfer->getDuration() !== $duration){
                    $sync = true;
                    $existingTransfer->setDuration($duration);
                }
                if ($existingTransfer->getDistance() !== $distance){
                    $sync = true;
                    $existingTransfer->setDistance($distance);
                }
                if ($existingTransfer->getLongName() !== $longName){
                    $sync = true;
                    $existingTransfer->setLongName($longName);
                }
            }
        }

        if ($sync)
            $this->om->flush();
    }

    public function validateTransfers($transfers, $stopArea, $isExternal)
    {
        $stopManager = new StopManager($this->om);
        foreach ($transfers as $transfer)
        {
            $duration = $transfer['duration'];
            $distance = $transfer['distance'];
            $startStopId = $transfer['startStopId'];
            $endStopId = $transfer['endStopId'];

            if (empty($duration) || !is_numeric($duration) || $duration < 0 || ($duration > 60 && $duration != 99)){
                throw new \Exception("a transfer's duration must be a positive integer <= 60 or == 99 ");
            }
            if (!empty($distance) && (!is_numeric($distance) || $distance < 0)){
                throw new \Exception("a transfer's distance must be a positive integer");
            }
            if (empty($startStopId) || empty($endStopId))
            {
                throw new \Exception("error: stop's id and type are needed");
            }

            $startIsInternal = false;
            $startStop = $stopManager->find($startStopId);
            if (is_null($startStop))
                throw new \Exception('stop with id ' . $startStopId . ' not found');
            if ($startStop->getStopArea() == $stopArea)
                $startIsInternal = true;

            $endIsInternal = false;
            $endStop = $stopManager->find($endStopId);
            if (is_null($endStop))
                throw new \Exception('stop with id ' . $endStopId . ' not found');
            if ($endStop->getStopArea() == $stopArea)
                $endIsInternal = true;

            if ($isExternal && !($startIsInternal xor $endIsInternal))
                throw new \Exception("only start OR end stop must belong to stop_area for external transfer");
            if (!$isExternal && !($startIsInternal && $endIsInternal))
                throw new \Exception("both start and end stop must belong to stop_area for internal transfer");
        }
    }
}
