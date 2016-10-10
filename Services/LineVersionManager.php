<?php

namespace Tisseo\EndivBundle\Services;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Collections\ArrayCollection;
use Tisseo\EndivBundle\Utils\Sorting;
use Tisseo\EndivBundle\Entity\LineVersion;
use Tisseo\EndivBundle\Entity\GridCalendar;

class LineVersionManager extends AbstractManager
{
    /**
     * Find all line versions and sort them
     * using line priority and natural sort on line number
     *
     * @return Collection
     */
    public function findAllSortedByLineNumber()
    {
        return Sorting::sortLineVersionsByNumber($this->getRepository()->findAll());
    }

    /**
     * Find Previous LineVersion
     *
     * @param  LineVersion $lineVersion
     * @return LineVersion or null
     *
     * Finding an hypothetical previous version of the LineVersion passed in
     * parameter.
     */
    public function findPreviousLineVersion(LineVersion $lineVersion)
    {
        $query = $this->getObjectManager()->createQueryBuilder('lv')
            ->join('lv.line', 'l')
            ->where('l.id = :id')
            ->andWhere('lv.version = :version')
            ->setParameter('id', $lineVersion->getLine()->getId())
            ->setParameter('version', ($lineVersion->getVersion()-1));

        return $query->getQuery()->getSingleResult();
    }

    // NOTE: used by paon-bundle/Controller/CalendarController.php
    /**
     * findWithPreviousCalendars
     *
     * @param  integer $lineVersionId
     * @return LineVersion
     *
     * Find a LineVersion using lineVersionId.
     * If the returned LineVersion is new (i.e. no gridCalendars):
     *  - get its hypothetical previous LineVersion from database
     *  - if found, create new gridCalendars using previous LineVersion gridCalendars
     */
    public function findWithPreviousCalendars($lineVersionId)
    {
        $lineVersion = $this->find($lineVersionId);
        if ($lineVersion->isNew()) {
            $previousLineVersion = $this->findPreviousLineVersion($lineVersion);
            if ($previousLineVersion !== null) {
                $lineVersion->mergeGridCalendars($previousLineVersion);
                $objectManager->persist($lineVersion);
                $objectManager->flush();
            }
        }

        return $lineVersion;
    }

    /*
     * Find active line versions (current or future)
     *
     * @param boolean $mode
     * @return array
     *
     * Find LineVersions considered as active according to the current
     * date passed as parameter.
     */
    public function findActiveLineVersions($mode = false, $datasources = false)
    {
        $query = $this->getRepository()->createQueryBuilder('lv')
            ->select('lv')
            ->where('lv.endDate IS NULL OR lv.endDate > CURRENT_DATE()')
            ->join('lv.line', 'l')
            ->join('l.physicalMode', 'p')
            ->join('lv.fgColor', 'fg')
            ->join('lv.bgColor', 'bg')
            ->leftJoin('lv.printings', 'pr')
            ->addSelect('l, p, fg, bg, pr');

        if ($datasources === true) {
            $query
                ->join('l.lineDatasources', 'ld')
                ->join('ld.datasource', 'd')
                ->addSelect('ld, d');
        }

        $result = Sorting::sortLineVersionsByNumber($query->getQuery()->getResult());

        if ($mode === true) {
            $modes = $this->getRepository('TisseoEndivBundle:PhysicalMode')->createQueryBuilder('p')
                ->select('p.name')->getQuery()->getScalarResult();
            $modes = array_map('current', $modes);
            $modeNames = array();
            foreach ($modes as $mode) {
                $modeNames[$mode] = array();
            }

            $result = Sorting::splitByPhysicalMode($result, Sorting::SPLIT_LINE_VERSION, $modeNames);
        }

        return $result;
    }

    /*
     * Find GridMaskTypes not linked to the LineVersion (also the trips/tripCalendars)
     *
     * @param LineVersion $lineVersion
     */
    public function findUnlinkedGridMaskTypes(LineVersion $lineVersion)
    {
        /* if no gridCalendars linked to this lineVersion,
         * search only for all related GridMaskTypes */
        $notLinked = true;
        foreach ($lineVersion->getGridCalendars() as $gridCalendar) {
            if ($gridCalendar->getGridLinkCalendarMaskTypes()->count() > 0) {
                $notLinked = false;
                break;
            }
        }

        $objectManager = $this->getObjectManager();
        if ($notLinked) {
            $query = $objectManager->createQuery(
                "
                SELECT gmt FROM Tisseo\EndivBundle\Entity\GridMaskType gmt
                JOIN gmt.tripCalendars tc
                JOIN tc.trips t
                JOIN t.route r
                JOIN r.lineVersion lv
                WHERE lv.id = ?1
                GROUP BY gmt.id
                ORDER BY gmt.id
            "
            );
        }
        /* else, search for all related gridMaskTypes which aren't already linked to a gridCalendar */
        else {
            $query = $objectManager->createQuery(
                "
                SELECT gmt FROM Tisseo\EndivBundle\Entity\GridMaskType gmt
                JOIN gmt.tripCalendars tc
                JOIN tc.trips t
                JOIN t.route r
                WHERE IDENTITY(r.lineVersion) = ?1
                AND gmt.id NOT IN(
                    SELECT IDENTITY(glcmt.gridMaskType) FROM Tisseo\EndivBundle\Entity\GridLinkCalendarMaskType glcmt
                    JOIN glcmt.gridCalendar gc
                    WHERE IDENTITY(gc.lineVersion) = ?1
                )
                GROUP BY gmt.id
                ORDER BY gmt.id
            "
            );
        }
        $query->setParameter(1, $lineVersion->getId());

        $gmts = $query->getResult();
        $result = array();
        $cpt = 0;

        foreach ($gmts as $gmt) {
            $result[$cpt] = array($gmt, array());

            $query = $objectManager->createQuery(
                "
                SELECT tc, count(t) FROM Tisseo\EndivBundle\Entity\TripCalendar tc
                JOIN tc.trips t
                JOIN t.route r
                JOIN r.lineVersion lv
                JOIN tc.gridMaskType gmt
                WHERE lv.id = ?1
                AND gmt.id = ?2
                GROUP BY tc.id
            "
            );

            $query->setParameter(1, $lineVersion->getId());
            $query->setParameter(2, $gmt->getId());

            $tripCalendars = $query->getResult();
            foreach ($tripCalendars as $tripCalendar) {
                $result[$cpt][1][] = $tripCalendar;
            }
            $cpt++;
        }

        return $result;
    }

    /*
     * updateGridCalendars
     * @param array $gridCalendarIds
     * @param integer $lineVersionId
     *
     * Synchronize GridCalendars to a specific LineVersion according to values
     * returned from calendars form view. (i.e. delete GridCalendars if their id
     * is not present in $gridCalendarsIds and add new ones)
     */
    public function updateGridCalendars($gridCalendars, $lineVersionId)
    {
        $objectManager = $this->getObjectManager();
        $lineVersion = $this->find($lineVersionId);
        $sync = false;
        $newGridCalendars = array();

        // Detach removed GridCalendars
        foreach ($lineVersion->getGridCalendars() as $gridCalendar) {
            if (!in_array($gridCalendar->getId(), array_keys($gridCalendars))) {
                $sync = true;
                $lineVersion->removeGridCalendar($gridCalendar);
            }
        }

        // Create new GridCalendars
        foreach ($gridCalendars as $id => $gridCalendarJson) {
            if (strpos($id, "new") !== false) {
                $sync = true;
                $gridCalendar = new GridCalendar();
                $gridCalendar->setDays($gridCalendarJson['days']);
                $gridCalendar->setName($gridCalendarJson['name']);
                $gridCalendar->setLineVersion($lineVersion);
                $objectManager->persist($gridCalendar);
                $objectManager->flush($gridCalendar);

                $newGridCalendars[$gridCalendar->getId()] = $gridCalendarJson['gmt'];
            } else {
                $newGridCalendars[$id] = $gridCalendarJson['gmt'];
            }
        }

        if ($sync) {
            $objectManager->persist($lineVersion);
        }

        return $newGridCalendars;
    }

    /*
     * Create a new LineVersion
     * @param LineVersion $lineVersion
     *
     * Doing these things before :
     *  - closing previous LineVersion if it exists
     *  - deleting all trips which don't belong anymore to the previous LineVersion
     */
    public function create(LineVersion $lineVersion)
    {
        $objectManager = $this->getObjectManager();
        $oldLineVersion = $lineVersion->getLine()->getLastLineVersion();
        if ($oldLineVersion !== null) {
            if ($oldLineVersion->getEndDate() === null) {
                $oldLineVersion->closeDate($lineVersion->getStartDate());
            } elseif ($oldLineVersion->getEndDate() > $lineVersion->getStartDate()) {
                throw new \Exception(
                    sprintf(
                        "The start date %s of the new LineVersion can't be < to the end date %s of the last one",
                        $lineVersion->getStartDate(),
                        $oldLineVersion->getEndDate()
                    )
                );
            }
            $objectManager->persist($oldLineVersion);
        }

        foreach ($lineVersion->getLineGroupContents() as $lineGroupContent) {
            $objectManager->persist($lineGroupContent->getLineGroup());
        }

        $objectManager->flush();

        /* trick here, in order to resolve potential modifications
         * stape1: get resolved modifications from the new LineVersion
         * stape2: unlink these modifications from the new LineVersion
         * stape3: resolve all modifications with the new LineVersion
         */
        $modifications = $lineVersion->getModifications();
        $lineVersion->setModifications(new ArrayCollection());
        $objectManager->persist($lineVersion);

        foreach ($modifications as $modification) {
            $modification->setResolvedIn($lineVersion);
            $objectManager->persist($modification->getLineVersion());
        }

        foreach ($lineVersion->getLineGroupContents() as $lineGroupContent) {
            $objectManager->persist($lineGroupContent);
        }

        $objectManager->flush();
    }

    /*
     * delete
     * @param integer $lineVersionId
     *
     * delete line version
     */
    public function remove($lineVersionId)
    {
        $lineVersion = $this->find($lineVersionId);
        $objectManager = $this->getObjectManager();

        if (empty($lineVersion)) {
            throw new \Exception("The LineVersion with id: ".$lineVersionId." can't be found.");
        }

        $previousLineVersion = $this->findPreviousLineVersion($lineVersion);

        if ($previousLineVersion !== null) {
            $previousLineVersion->setEndDate(null);
            $objectManager->persist($previousLineVersion);
        }

        // Calendars are just isolated not deleted
        foreach ($lineVersion->getCalendars() as $calendar) {
            $calendar->setLineVersion(null);
            $objectManager->persist($calendar);
        }
        $objectManager->flush();

        // Doctrine won't delete trips in a good order if parent/pattern relations exist
        foreach ($lineVersion->getRoutes() as $route) {
            foreach ($route->getTripsHavingParent() as $trip) {
                $objectManager->remove($trip);
            }

            foreach ($route->getTripsHavingPattern() as $trip) {
                $objectManager->remove($trip);
            }
        }
        $objectManager->flush();

        $objectManager->remove($lineVersion);
        $objectManager->flush();
    }

    // NOTE : used by boa-bundle/Controller/AccessibilityMonitoringController.php
    public function getStopAccessibilityChangesByRoute($lineVersion, $startDate)
    {
        $result = array();
        $now = new \DateTime();
        foreach ($lineVersion->getRoutes() as $route) {
            $stops_data = array();
            $stops = array();

            foreach ($route->getRouteStops() as $routeStop) {
                if ($routeStop->isOdtAreaRouteStop()) {
                    $odtArea = $routeStop->getWaypoint()->getOdtArea();
                    foreach ($odtArea->getOdtStops() as $odtStop) {
                        if (!in_array($odtStop->getStop(), $stops)) {
                            $stops[] = $odtStop->getStop();
                        }
                    }
                } elseif (!in_array($routeStop->getWaypoint()->getStop(), $stops)) {
                    $stops[] = $routeStop->getWaypoint()->getStop();
                }
            }

            foreach ($stops as $stop) {
                $accessibilityCalendar = $stop->getAccessibilityCalendar();
                if (!empty($accessibilityCalendar)) {
                    $bitmask = $this->getService('calendar')->getCalendarBitmask($accessibilityCalendar->getId(), $startDate, $now);
                    $startBit = substr($bitmask, 0, 1);
                    $endBit = substr($bitmask, strlen($bitmask) - 1, 1);
                    if ($startBit != $endBit) {
                        $data = array();
                        $data['stop'] = $stop;
                        $data['accessible'] = ($endBit == '0') ? true : false;
                        $stops_data[] = $data;
                    }
                }
            }

            if (!empty($stops_data)) {
                $result[$route->getId()] = $stops_data;
            }
        }
        return $result;
    }

    // NOTE: used by boa-bundle/Controller/PoiMonitoringController.php
    public function getPoiByStopArea($lineVersion)
    {
        $result = array();
        $stopAreas = array();

        foreach ($lineVersion->getRoutes() as $route) {
            foreach ($route->getRouteStops() as $routeStop) {
                if ($routeStop->isOdtAreaRouteStop()) {
                    $odtArea = $routeStop->getWaypoint()->getOdtArea();
                    foreach ($odtArea->getOdtStops() as $odtStop) {
                        if (!in_array($odtStop->getStop()->getStopArea(), $stopAreas)) {
                            $stopAreas[] = $odtStop->getStop()->getStopArea();
                        }
                    }
                } elseif (!in_array($routeStop->getWaypoint()->getStop()->getStopArea(), $stopAreas)) {
                    $stopAreas[] = $routeStop->getWaypoint()->getStop()->getStopArea();
                }
            }
        }

        usort($stopAreas, array($this, "usortStopArea"));

        $stopAreaIds = array();
        foreach ($stopAreas as $stopArea) {
            $stopAreaIds[] = $stopArea->getId();
        }
        $connection = $objectManager->getConnection();
        $query = "SELECT DISTINCT p.name as poi_name, p.id as poi_id, s.stop_area_id as stop_area_id
            FROM poi p
            JOIN poi_stop ps ON ps.poi_id = p.id
            JOIN stop s ON ps.stop_id = s.id
            WHERE s.stop_area_id IN (?)
            AND p.on_schema IS TRUE
            ORDER BY p.name
        ";
        $stmt = $connection->executeQuery($query, array($stopAreaIds), array(\Doctrine\DBAL\Connection::PARAM_INT_ARRAY));
        $poiArray = $stmt->fetchAll();

        $poiRepository = $objectManager->getRepository('TisseoEndivBundle:Poi');

        foreach ($stopAreas as $stopArea) {
            $stopAreaPois = array();
            foreach ($poiArray as $poi) {
                if ($poi["stop_area_id"] == $stopArea->getId()) {
                    $stopAreaPois[] = $poiRepository->find($poi["poi_id"]);
                }
            }
            $item = array(
                "stopArea" => $stopArea,
                "poiArray" => $stopAreaPois
            );

            $result[] = $item;
        }

        return $result;
    }

    // used in 'getPoiByStop' method to sort an array of stops by their labels
    protected function usortStopArea($sa1, $sa2)
    {
        return strcasecmp($sa1->getCity()->getName() . $sa1->getShortName(), $sa2->getCity()->getName() . $sa2->getShortName());
    }

    /**
     * Build a default query for 'active' line versions (i.e. current/future)
     * looking at a date of reference
     * Join line/physicalMode/colors by default (always needed for display)
     *
     * @param  \Datetime $date
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function createQueryForActiveLineVersions()
    {
        return $this->getRepository()->createQueryBuilder('lv')
            ->select('lv, l, p, fg, bg')
            ->where('lv.endDate is null OR (lv.endDate + 1) > :date')
            ->join('lv.line', 'l')
            ->join('l.physicalMode', 'p')
            ->join('lv.fgColor', 'fg')
            ->join('lv.bgColor', 'bg')
            ->setParameter('date', $date);
    }
}
