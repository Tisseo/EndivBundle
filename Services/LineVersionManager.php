<?php

namespace Tisseo\EndivBundle\Services;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Translation\TranslatorInterface;
use Tisseo\EndivBundle\Entity\GridCalendar;
use Tisseo\EndivBundle\Entity\LineVersion;

class LineVersionManager extends SortManager
{
    private $om = null;
    /** @var \Doctrine\ORM\EntityRepository $repository */
    private $repository = null;
    private $calendarManager = null;
    private $translator = null;

    public function __construct(ObjectManager $om, CalendarManager $calendarManager, TranslatorInterface $translator)
    {
        $this->om = $om;
        $this->repository = $om->getRepository('TisseoEndivBundle:LineVersion');
        $this->calendarManager = $calendarManager;
        $this->translator = $translator;
    }

    public function findAll()
    {
        return $this->repository->findAll();
    }

    public function find($lineVersionId)
    {
        return empty($lineVersionId) ? null : $this->repository->find($lineVersionId);
    }

    public function findAllSortedByLineNumber()
    {
        return $this->sortLineVersionsByNumber($this->repository->findAll());
    }

    /**
     * Find the line versions who are active during $date (month)
     *
     * @param \Datetime $date
     *
     * @return mixed
     */
    public function findLineVersionSortedByLineNumber(\Datetime $date = null, $excludedPhysicalMode = array())
    {
        if (is_null($date)) {
            $date = new \DateTime('now');
        }

        $endDate = clone $date;
        $endDate->modify('+1 month -1 day');

        $qb = $this->repository->createQueryBuilder('lv');

        $qb->select('lv')
            ->where(':startDate >=  lv.startDate')
            ->andWhere(':endDate <= coalesce(lv.endDate, lv.plannedEndDate)');

        if (!empty($excludedPhysicalMode)) {
            $qb->join('lv.line', 'l')
                ->join('l.physicalMode', 'pm')
                ->andWhere($qb->expr()->notIn('pm.id', ':physicalMode'))
                ->setParameter('physicalMode', $excludedPhysicalMode);
        }

        $query = $qb->setParameter('startDate', $date->format('Y-m-d'))
            ->setParameter('endDate', $endDate->format('Y-m-d'))
            ->getQuery();

        try {
            $lineVersions = $query->getResult();
        } catch (\Exception $e) {
            return null;
        }

        return $this->sortLineVersionsByNumber($lineVersions);
    }

    /**
     * findLastLineVersionOfLine
     *
     * @param int $lineId
     *
     * @return LineVersion or null
     *
     * Return the last version of LineVersion associated to the Line
     */
    public function findLastLineVersionOfLine($lineId)
    {
        $finalResult = null;

        if ($lineId == null) {
            return $finalResult;
        }

        $query = $this->repository->createQueryBuilder('lv')
            ->where('lv.line = :line')
            ->setParameter('line', $lineId)
            ->getQuery();

        try {
            $results = $query->getResult();
        } catch (\Exception $e) {
            return $finalResult;
        }

        foreach ($results as $result) {
            if ($result->getEndDate() === null) {
                return $result;
            } else {
                if ($finalResult !== null && ($finalResult->getEndDate() > $result->getEndDate())) {
                    continue;
                }
            }
            $finalResult = $result;
        }

        return $finalResult;
    }

    /**
     * Find Previous LineVersion
     *
     * @param LineVersion $lineVersion
     *
     * @return LineVersion or null
     *
     * Finding an hypothetical previous version of the LineVersion passed in
     * parameter.
     */
    public function findPreviousLineVersion(LineVersion $lineVersion)
    {
        $query = $this->om->createQuery("
            SELECT lv FROM Tisseo\EndivBundle\Entity\LineVersion lv
            JOIN lv.line l
            WHERE l.number = ?1
            AND lv.id != ?2
        ");
        $query->setParameter(1, $lineVersion->getLine()->getNumber());
        $query->setParameter(2, $lineVersion->getId());

        $lineVersions = $query->getResult();

        $result = null;
        foreach ($lineVersions as $lv) {
            if ($lv->getEndDate() === null) {
                continue;
            }

            if (($lineVersion->getEndDate() !== null && $lv->getEndDate() > $lineVersion->getEndDate()) ||
                (!empty($result) && $result->getEndDate() > $lv->getEndDate())
            ) {
                continue;
            }

            $result = $lv;
        }

        return $result;
    }

    /**
     * findWithPreviousCalendars
     *
     * @param int $lineVersionId
     *
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
                $this->om->persist($lineVersion);
                $this->om->flush();
            }
        }

        return $lineVersion;
    }

    /**
     * findActiveLineVersions
     *
     * @param Datetime $now                 TODO: remove
     * @param string   $filter              default null
     * @param bool     $splitByPhysicalMode default false
     * @param int SORT_BY_LINE_NUMBER SORT_BY_PRIORITY_AND_START_OFFER
     *
     * @return Collection $lineVersions
     *
     * Find LineVersion which are considered as active according to the current
     * date passed as parameter.
     */
    public function findActiveLineVersions(\Datetime $now, $filter = '', $splitByPhysicalMode = false, $sortBy = self::SORT_BY_NUMBER)
    {
        $query = $this->repository->createQueryBuilder('lv')
            ->where('lv.endDate is null OR (lv.endDate + 1) > :now')
            ->setParameter('now', $now);

        if ($filter === 'grouplines') {
            $query->groupBy('lv.line, lv.id')->orderBy('lv.line');
        } elseif ($filter === 'schematic') {
            $query->leftJoin('lv.schematic', 'sc');
        }

        if ($sortBy === self::SORT_BY_NUMBER) {
            $result = $this->sortLineVersionsByNumber(
                $query->getQuery()->getResult()
            );
        } elseif ($sortBy === self::SORT_BY_PRIORITY_NUMBER_AND_START_OFFER) {
            $result = $this->sortByPriorityNumberStartOffer(
                $query->getQuery()->getResult()
            );
        }

        if ($splitByPhysicalMode) {
            $query = $this->om->createQuery("
                SELECT p.name FROM Tisseo\EndivBundle\Entity\PhysicalMode p
            ");
            $physicalModes = $query->getResult();

            $result = $this->splitByPhysicalMode($result, $physicalModes);
        }

        return $result;
    }

    /**
     * @param \Datetime $date
     * @param string    $filter
     * @param bool      $splitByPhysicalMode
     *
     * @return array|mixed|null
     */
    public function findInactiveLineVersions(\DateTime $date, $filter = '', $splitByPhysicalMode = false)
    {
        $query = $this->repository->createQueryBuilder('lv');
        $expr = $query->expr();

        $query
            ->select('lv', 'sc', 'l', 'c', 'c_2', 'ld', 'pm')
            ->join('lv.line', 'l')
            ->join('lv.fgColor', 'c')
            ->join('lv.bgColor', 'c_2')
            ->join('l.lineDatasources', 'ld')
            ->join('l.physicalMode', 'pm')
            ->leftJoin('lv.schematic', 'sc')
            ->where(
                $expr->andX(
                    $expr->lt('lv.endDate', ':now'),
                    $expr->isNotNull('lv.endDate')
                )
            )
            ->setParameter('now', $date);

        if ($filter === 'grouplines') {
            $query->groupBy('lv.line, lv.id')->orderBy('lv.line');
        }
        $result = $this->sortLineVersionsByNumber($query->getQuery()->getResult());

        if ($splitByPhysicalMode) {
            $query = $this->om->createQuery("SELECT p.name FROM Tisseo\EndivBundle\Entity\PhysicalMode p");
            $physicalModes = $query->getResult();

            $result = $this->splitByPhysicalMode($result, $physicalModes);
        }

        return $result;
    }

    /**
     * Get active line version by line number
     */
    public function findActiveLineVersionByLineNumber($lineNumber)
    {
        $query = $this->om->createQuery("
            SELECT lv.id FROM Tisseo\EndivBundle\Entity\LineVersion lv
            JOIN lv.line line
            WHERE line.number = ?1
        	   AND (lv.endDate is null OR (lv.endDate + 1) > CURRENT_TIMESTAMP())
        	   AND lv.startDate <= CURRENT_TIMESTAMP()");

        $query->setParameter(1, $lineNumber);

        return $query->getResult();
    }

    /**
     * Find line names from line version ids
     *
     * @param array $ids
     */
    public function findLineNames($ids)
    {
        $queryBuilder = $this->om->createQueryBuilder()
            ->select('lv.id', 'lv.name')
            ->from('Tisseo\EndivBundle\Entity\LineVersion', 'lv');
        $queryBuilder->where($queryBuilder->expr()->in('lv.id', $ids));

        $results = $queryBuilder->getQuery()->getResult();

        return $results;
    }

    /*
     * findUnlinkedGridMaskTypes
     * @param LineVersion $lineVersion
     *
     * Find GridMaskTypes and their TripCalendars/Trips related to one
     * LineVersion.
     */
    public function findUnlinkedGridMaskTypes(LineVersion $lineVersion)
    {
        /* if no gridCalendars linked to this lineVersion, search only for all related gridMaskTypes */
        $notLinked = true;
        foreach ($lineVersion->getGridCalendars() as $gridCalendar) {
            if (!$gridCalendar->getGridLinkCalendarMaskTypes()->isEmpty()) {
                $notLinked = false;
                break;
            }
        }
        if ($notLinked) {
            $query = $this->om->createQuery("
                SELECT gmt FROM Tisseo\EndivBundle\Entity\GridMaskType gmt
                JOIN gmt.tripCalendars tc
                JOIN tc.trips t
                JOIN t.route r
                JOIN r.lineVersion lv
                WHERE lv.id = ?1
                GROUP BY gmt.id
                ORDER BY gmt.id
            ");
        } /* else, search for all related gridMaskTypes which aren't already linked to a gridCalendar */
        else {
            $query = $this->om->createQuery("
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
            ");
        }
        $query->setParameter(1, $lineVersion->getId());

        $gmts = $query->getResult();
        $result = array();
        $cpt = 0;

        foreach ($gmts as $gmt) {
            $result[$cpt] = array($gmt, array());

            $query = $this->om->createQuery("
                SELECT tc, count(t) FROM Tisseo\EndivBundle\Entity\TripCalendar tc
                JOIN tc.trips t
                JOIN t.route r
                JOIN r.lineVersion lv
                JOIN tc.gridMaskType gmt
                WHERE lv.id = ?1
                AND gmt.id = ?2
                GROUP BY tc.id
            ");

            $query->setParameter(1, $lineVersion->getId());
            $query->setParameter(2, $gmt->getId());

            $tripCalendars = $query->getResult();
            foreach ($tripCalendars as $tripCalendar) {
                $result[$cpt][1][] = $tripCalendar;
            }
            ++$cpt;
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
            if (strpos($id, 'new') !== false) {
                $sync = true;
                $gridCalendar = new GridCalendar();
                $gridCalendar->setDays($gridCalendarJson['days']);
                $gridCalendar->setName($gridCalendarJson['name']);
                $gridCalendar->setLineVersion($lineVersion);
                $this->om->persist($gridCalendar);
                $this->om->flush();

                $newGridCalendars[$gridCalendar->getId()] = $gridCalendarJson['gmt'];
            } else {
                $newGridCalendars[$id] = $gridCalendarJson['gmt'];
            }
        }

        if ($sync) {
            $this->om->persist($lineVersion);
        }

        return $newGridCalendars;
    }

    /*
     * create
     * @param LineVersion $lineVersion
     *
     * Save a LineVersion after :
     *  - closing previous LineVersion if it exists (using current LineVersion
     *  startDate
     *  - deleting all trips which don't belong anymore to the previous
     *  LineVersion
     */
    public function create(LineVersion $lineVersion)
    {
        $oldLineVersion = $this->findLastLineVersionOfLine($lineVersion->getLine()->getId());

        if ($oldLineVersion) {
            if ($oldLineVersion->getEndDate() === null) {
                /*
                 *  Previous line version haven't close date, so we close it 1 day before the the new
                 *  lineversion startDate.
                 *  Check the days interval between old line version start date and and end date, it must be greater than 1
                 */
                $endDate = clone $lineVersion->getStartDate();
                $interval = $oldLineVersion->getStartDate()->diff($endDate);
                if ($interval->days >= 1) {
                    $oldLineVersion->closeDate($endDate);
                } else {
                    throw new \Exception($this->translator->trans('tisseo.paon.exception.line_version.min_duration'));
                }
            } else {
                if ($lineVersion->getStartDate() <= $oldLineVersion->getEndDate()) {
                    throw new \Exception(
                        $this->translator->trans(
                            'tisseo.paon.exception.line_version.min_interval',
                            ['%previous_close_date%' => $oldLineVersion->getEndDate()->format('d/m/Y')]
                        )
                    );
                } else {
                    $endDate = clone $lineVersion->getStartDate();
                    $interval = $oldLineVersion->getStartDate()->diff($endDate);
                    if ($interval->days >= 1) {
                        $oldLineVersion->closeDate($endDate);
                    } else {
                        throw new \Exception($this->translator->trans('tisseo.paon.exception.line_version.min_duration'));
                    }
                }
            }
            $this->om->persist($oldLineVersion);
        }

        foreach ($lineVersion->getLineGroupContents() as $lineGroupContent) {
            $this->om->persist($lineGroupContent->getLineGroup());
        }

        $this->om->flush();

        /* trick here, in order to resolve potential modifications
         * stape1: get resolved modifications from the new LineVersion
         * stape2: unlink these modifications from the new LineVersion
         * stape3: resolve all modifications with the new LineVersion
         */
        $modifications = $lineVersion->getModifications();
        $lineVersion->setModifications(new ArrayCollection());
        $this->om->persist($lineVersion);

        foreach ($modifications as $modification) {
            $modification->setResolvedIn($lineVersion);
            $this->om->persist($modification->getLineVersion());
        }

        foreach ($lineVersion->getLineGroupContents() as $lineGroupContent) {
            $this->om->persist($lineGroupContent);
        }

        $this->om->flush();
    }

    /*
     * save
     * @param LineVersion $lineVersion
     *
     * Persist and save a LineVersion into database.
     */
    public function save(LineVersion $lineVersion)
    {
        $this->om->persist($lineVersion);
        $this->om->flush();
    }

    /*
     * delete
     * @param integer $lineVersionId
     *
     * delete line version
     */
    public function delete($lineVersionId)
    {
        $lineVersion = $this->find($lineVersionId);

        if (empty($lineVersion)) {
            throw new \Exception('The LineVersion with id: '.$lineVersionId." can't be found.");
        }

        $previousLineVersion = $this->findPreviousLineVersion($lineVersion);

        if ($previousLineVersion !== null) {
            $previousLineVersion->setEndDate(null);
            $this->om->persist($previousLineVersion);
        }

        /* Calendars are just isolated not deleted */
        foreach ($lineVersion->getCalendars() as $calendar) {
            $calendar->setLineVersion(null);
            $this->om->persist($calendar);
        }
        $this->om->flush();

        /* Doctrine won't delete trips in a good way if parent/pattern relations exist
         * TODO: See if Doctrine can be configured to detect priority in Trip deletion
         * in order to avoid this kind of actions
         */
        foreach ($lineVersion->getRoutes() as $route) {
            foreach ($route->getTripsHavingParent() as $trip) {
                $this->om->remove($trip);
            }

            foreach ($route->getTripsHavingPattern() as $trip) {
                $this->om->remove($trip);
            }
        }
        $this->om->flush();

        $this->om->remove($lineVersion);
        $this->om->flush();
    }

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
                } else {
                    if (!in_array($routeStop->getWaypoint()->getStop(), $stops)) {
                        $stops[] = $routeStop->getWaypoint()->getStop();
                    }
                }
            }

            foreach ($stops as $stop) {
                $accessibilityCalendar = $stop->getAccessibilityCalendar();
                if (!empty($accessibilityCalendar)) {
                    $bitmask = $this->calendarManager->getCalendarBitmask($accessibilityCalendar->getId(), $startDate,
                        $now);
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
                } else {
                    if (!in_array($routeStop->getWaypoint()->getStop()->getStopArea(), $stopAreas)) {
                        $stopAreas[] = $routeStop->getWaypoint()->getStop()->getStopArea();
                    }
                }
            }
        }

        usort($stopAreas, array($this, 'usortStopArea'));

        $stopAreaIds = array();
        foreach ($stopAreas as $stopArea) {
            $stopAreaIds[] = $stopArea->getId();
        }
        $connection = $this->om->getConnection();
        $query = 'SELECT DISTINCT p.name as poi_name, p.id as poi_id, s.stop_area_id as stop_area_id
            FROM poi p
            JOIN poi_stop ps ON ps.poi_id = p.id
            JOIN stop s ON ps.stop_id = s.id
            WHERE s.stop_area_id IN (?)
            AND p.on_schema IS TRUE
            ORDER BY p.name
        ';
        $stmt = $connection->executeQuery($query, array($stopAreaIds),
            array(\Doctrine\DBAL\Connection::PARAM_INT_ARRAY));
        $poiArray = $stmt->fetchAll();

        $poiRepository = $this->om->getRepository('TisseoEndivBundle:Poi');

        foreach ($stopAreas as $stopArea) {
            $stopAreaPois = array();
            foreach ($poiArray as $poi) {
                if ($poi['stop_area_id'] == $stopArea->getId()) {
                    $stopAreaPois[] = $poiRepository->find($poi['poi_id']);
                }
            }
            $item = array(
                'stopArea' => $stopArea,
                'poiArray' => $stopAreaPois
            );

            $result[] = $item;
        }

        return $result;
    }

    // used in 'getPoiByStop' method to sort an array of stops by their labels
    public function usortStopArea($a, $b)
    {
        return strcasecmp($a->getCity()->getName().$a->getShortName(), $b->getCity()->getName().$b->getShortName());
    }
}
