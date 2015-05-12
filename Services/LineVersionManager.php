<?php

namespace Tisseo\EndivBundle\Services;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Collections\ArrayCollection;
use Tisseo\EndivBundle\Entity\LineVersion;
use Tisseo\EndivBundle\Entity\GridCalendar;
use Tisseo\EndivBundle\Entity\LineVersionDatasource;

class LineVersionManager extends SortManager
{
    private $om = null;
    /** @var \Doctrine\ORM\EntityRepository $repository */
    private $repository = null;

    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
        $this->repository = $om->getRepository('TisseoEndivBundle:LineVersion');
    }

    public function findAll()
    {
        return ($this->repository->findAll());
    }

    public function find($lineVersionId)
    {
        return empty($lineVersionId) ? null : $this->repository->find($lineVersionId);
    }

    /**
     * findLastLineVersionOfLine
     * @param integer $lineId
     * @return LineVersion or null
     *
     * Return the last version of LineVersion associated to the Line
     */
    public function findLastLineVersionOfLine($lineId) {
        $finalResult = null;

        if ($lineId == null)
            return $finalResult;

        $query = $this->repository->createQueryBuilder('lv')
            ->where('lv.line = :line')
            ->setParameter('line', $lineId)
            ->getQuery();

        try {
            $results = $query->getResult();
        } catch(\Exception $e) {
            return $finalResult;
        }

        foreach($results as $result)
        {
            if ($result->getEndDate() === null)
                return $result;
            else if ($finalResult !== null && ($finalResult->getEndDate() > $result->getEndDate()))
                continue;
            $finalResult = $result;
        }

        return $finalResult;
    }

    /**
     * findPreviousLineVersion
     * @param LineVersion $lineVersion
     * @return LineVersion or null
     *
     * Find an hypothetical previous version of the LineVersion passed in 
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
        foreach($lineVersions as $lv)
        {
            if ($lv->getEndDate() === null)
                continue;
            if (($lineVersion->getEndDate() !== null && $lv->getEndDate() > $lineVersion->getEndDate()) || (!empty($result) && $result->getEndDate() > $lv->getEndDate()))
                continue;
            $result = $lv;
        }
        
        return $result;
    }

    /**
     * findWithPreviousCalendars 
     * @param integer $lineVersionId
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
        if ($lineVersion->isNew())
        {
            $previousLineVersion = $this->findPreviousLineVersion($lineVersion);
            if ($previousLineVersion !== null)
            {
                $lineVersion->mergeGridCalendars($previousLineVersion);
                $this->om->persist($lineVersion);
                $this->om->flush();
            }
        }

        return $lineVersion;
    }

    /*
     * findActiveLineVersions
     * @param Datetime $now
     * @param string $filter default null
     * @param Boolean $splitByPhysicalMode default false
     * @return Collection $lineVersions
     *
     * Find LineVersion which are considered as active according to the current 
     * date passed as parameter.
     */
    public function findActiveLineVersions(\Datetime $now, $filter = '', $splitByPhysicalMode = false)
    {
        $query = $this->repository->createQueryBuilder('lv')
            ->where('lv.endDate is null OR lv.endDate > :now')
            ->setParameter('now', $now);

        if ($filter === 'grouplines')
            $query->groupBy('lv.line, lv.id')->orderBy('lv.line');

        if ($filter === 'schematic') {
            $query->leftJoin('lv.schematic', 'sc');
        }

        $result = $this->sortLineVersionsByNumber($query->getQuery()->getResult());

        if ($splitByPhysicalMode) {
            $query = $this->om->createQuery("
                    SELECT p.name FROM Tisseo\EndivBundle\Entity\PhysicalMode p
            ");
            $physicalModes = $query->getResult();

            $result = $this->splitByPhysicalMode($result, $physicalModes);
        }

        return $result;
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
        foreach($lineVersion->getGridCalendars() as $gridCalendar)
        {
            if (!$gridCalendar->getGridLinkCalendarMaskTypes()->isEmpty())
            {
                $notLinked = false;
                break;
            }
        }
        if ($notLinked)
        {
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
        }
        /* else, search for all related gridMaskTypes which aren't already linked to a gridCalendar */
        else
        {
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

        foreach($gmts as $gmt)
        {
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
            foreach($tripCalendars as $tripCalendar)
            {
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
        $lineVersion = $this->find($lineVersionId);
        $sync = false;
        $newGridCalendars = array();

        // Detach removed GridCalendars
        foreach($lineVersion->getGridCalendars() as $gridCalendar)
        {
            if (!in_array($gridCalendar->getId(), array_keys($gridCalendars)))
            {
                $sync = true;
                $lineVersion->removeGridCalendar($gridCalendar);
            }
        }

        // Create new GridCalendars
        foreach($gridCalendars as $id => $gridCalendarJson)
        {
            if (strpos($id, "new") !== false)
            {
                $sync = true;
                $gridCalendar = new GridCalendar();
                $gridCalendar->setDays($gridCalendarJson['days']);
                $gridCalendar->setName($gridCalendarJson['name']);
                $gridCalendar->setLineVersion($lineVersion);
                $this->om->persist($gridCalendar);
                $this->om->flush();

                $newGridCalendars[$gridCalendar->getId()] = $gridCalendarJson['gmt'];
            }
            else
            {
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
    public function create(LineVersion $lineVersion, $username, $childLine = null)
    {
        $oldLineVersion = $this->findLastLineVersionOfLine($lineVersion->getLine()->getId());
        if ($oldLineVersion)
        {
            if ($oldLineVersion->getEndDate() === null)
                $oldLineVersion->closeDate($lineVersion->getStartDate());
            else if ($oldLineVersion->getEndDate() > $lineVersion->getStartDate())
                return array(false,'line_version.closure_error');
            $this->om->persist($oldLineVersion);
        }

        foreach($lineVersion->getLineGroupContents() as $lineGroupContent)
            $this->om->persist($lineGroupContent->getLineGroup());
        
        $this->om->flush();

        /* trick here, in order to resolve potential modifications
         * stape1: get resolved modifications from the new LineVersion
         * stape2: unlink these modifications from the new LineVersion
         * stape3: resolve all modifications with the new LineVersion
         */
        $modifications = $lineVersion->getModifications();
        $lineVersion->setModifications(new ArrayCollection());
        $this->om->persist($lineVersion);

        foreach($modifications as $modification)
        {
            $modification->setResolvedIn($lineVersion);
            $this->om->persist($modification->getLineVersion());
        }
        
        foreach($lineVersion->getLineGroupContents() as $lineGroupContent)
            $this->om->persist($lineGroupContent);    

        $query = $this->om->createQuery("
            SELECT ds FROM Tisseo\EndivBundle\Entity\Datasource ds
            WHERE ds.name = ?1
        ")->setParameter(1, 'Information Voyageurs');
      
        $datasource = $query->getOneOrNullResult();
        if (!empty($datasource))
        {
            $lineVersionDatasource = new LineVersionDatasource();
            $lineVersionDatasource->setDatasource($datasource);
            $lineVersionDatasource->setLineVersion($lineVersion);
            $lineVersionDatasource->setCode($username);
        }

        $this->om->persist($lineVersionDatasource);
        $this->om->flush();

        return array(true, 'line_version.persisted');
    }

    /*
     * save
     * @param LineVersion $lineVersion
     * @return array(boolean, string)
     *
     * Persist and save a LineVersion into database.
     */
    public function save(LineVersion $lineVersion)
    {
        $this->om->persist($lineVersion);
        $this->om->flush();

        return array(true, 'line_version.persisted');
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
        if($lineVersion == null) {
            return null;
        }

        $previousLineVersion = $this->findPreviousLineVersion($lineVersion);
        if( $previousLineVersion !== null ) {
            $previousLineVersion->setEndDate(null);
            $this->om->persist($previousLineVersion);
        }
        $this->om->remove($lineVersion);
        $this->om->flush();

        return array(true, 'line_version.deleted');
    }    
}
