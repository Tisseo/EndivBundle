<?php

namespace Tisseo\EndivBundle\Services;

use Tisseo\EndivBundle\Entity\OdtArea;
use Tisseo\EndivBundle\Entity\OdtStop;
use Tisseo\EndivBundle\Types\DateId;

class OdtStopManager extends AbstractManager
{
    /**
     * {inheritdoc}
     */
    public function find($odtStopId)
    {
        if (empty($odtStopId)) {
            return null;
        }

        $args = explode("/", $odtStopId);

        return $this->getRepository()->find(
            array(
            'startDate' => new DateId($args[0]),
            'stop' => $args[1],
            'odtArea' => $args[2]
            )
        );
    }

    /**
     * Update OdtStops
     *
     * @param  array   $odtStops
     * @param  OdtArea $odtArea
     *
     * Creating, deleting OdtStop entities.
     * @usedBy BOABundle
     */
    public function updateOdtStops($odtStops, OdtArea $odtArea)
    {
        $sync = false;
        $objectManager = $this->getObjectManager();
        foreach ($odtArea->getOdtStops() as $odtStop) {
            $existing = array_filter(
                $odtStops,
                function ($object) use ($odtStop) {
                    return ($object['id'] == $odtStop->getId());
                }
            );
            if (empty($existing)) {
                $today = (new DateId())->setTime(0, 0, 0);
                if ($odtStop->getStartDate() >= $today) {
                    $this->delete($odtStop);
                } else {
                    $odtStop->setEndDate((new DateId())->sub(new \DateInterval('P1D')));
                }
                $sync = true;
            }
        }
        foreach ($odtStops as $odtStopIter) {
            if (empty($odtStopIter['id'])) {
                $sync = true;
                $odtStop = new OdtStop();
                $startDateTime = \DateTime::createFromFormat('d/m/Y', $odtStopIter['startDate']);
                $odtStop->setStartDate(new DateId($startDateTime->format('Y-m-d')));
                $odtStop->setStop($this->getService('stop')->find($odtStopIter['stop']));
                $odtStop->setPickup($odtStopIter['pickup']);
                $odtStop->setDropOff($odtStopIter['dropOff']);
                if (strlen($odtStopIter['endDate']) > 0) {
                    $endDateTime = \DateTime::createFromFormat('d/m/Y', $odtStopIter['endDate']);
                    $odtStop->setEndDate(new DateId($endDateTime->format('Y-m-d')));
                }
                $odtStop->setOdtArea($odtArea);
                $objectManager->persist($odtStop);
            }
        }
        if ($sync) {
            $objectManager->flush();
        }
    }

    /**
     * get OdtStops
     *
     * @param  $data
     *
     * returns an array with odtStops
     * @usedBy BOABundle
     */
    public function getGroupedOdtStops($data, $odtArea)
    {
        $odtStops = array();
        $stopArea = $this->getService('stop_area')->find($data['boa_odt_stop[stop]']);
        if (!empty($stopArea)) {
            $stops = $stopArea->getStops();
            foreach ($stops as $stop) {
                $odtStop = new OdtStop();
                $odtStop->setStop($stop);
                $startDateTime = \DateTime::createFromFormat('d/m/Y', $data['boa_odt_stop[startDate]']);
                $odtStop->setStartDate(new DateId($startDateTime->format('Y-m-d')));
                $odtStop->setPickup($data['boa_odt_stop[pickup]']);
                $odtStop->setDropOff($data['boa_odt_stop[dropOff]']);
                if (strlen($data['boa_odt_stop[endDate]']) > 0) {
                    $endDateTime = \DateTime::createFromFormat('d/m/Y', $data['boa_odt_stop[endDate]']);
                    $odtStop->setEndDate(new DateId($endDateTime->format('Y-m-d')));
                }
                $odtStop->setOdtArea($odtArea);
                $odtStops[] = $odtStop;
            }
            return $odtStops;
        }
    }
}
