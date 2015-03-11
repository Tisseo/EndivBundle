<?php

namespace Tisseo\EndivBundle\Services;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Collections\Collection;

use Tisseo\EndivBundle\Entity\GridCalendar;
use Tisseo\EndivBundle\Entity\GridLinkCalendarMaskType;

class GridCalendarManager extends SortManager
{
    private $om = null;
    private $repository = null;

    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
        $this->repository = $om->getRepository('TisseoEndivBundle:GridCalendar');
    }

    public function findAll()
    {
        return ($this->repository->findAll());
    }

    public function find($gridCalendarId)
    {
        return empty($gridCalendarId) ? null : $this->repository->find($gridCalendarId);
    }

    public function findOne($gridCalendarId)
    {
        return empty($gridCalendarId) ? null : $this->repository->findOne($gridCalendarId);
    }

    /**
     * findRelatedGridMaskTypes
     * @param Collection $gridCalendars
     * @param inetger $lineVersionId
     * @return array()
     *
     * Retrieve all GridMaskType linked to a specific LineVersion and 
     * GridCalendars.
     */
    public function findRelatedGridMaskTypes(Collection $gridCalendars, $lineVersionId)
    {
        $result = array();
        foreach($gridCalendars as $gridCalendar)
        {
            $query = $this->om->createQuery("
                SELECT gmt FROM Tisseo\EndivBundle\Entity\GridMaskType gmt
                JOIN gmt.gridLinkCalendarMaskTypes glcmt
                JOIN glcmt.gridCalendar gc
                JOIN Tisseo\EndivBundle\Entity\Route r WITH r.lineVersion = gc.lineVersion
                WHERE IDENTITY(r.lineVersion) = ?1
                AND gc.id = ?2
                GROUP BY gmt.id
                ORDER BY gmt.id
            ");

            $query->setParameter(1, $lineVersionId);
            $query->setParameter(2, $gridCalendar->getId());

            $gmts = $query->getResult();
            $relatedGridMaskTypes = array();
            $cpt = 0;

            foreach($gmts as $gmt)
            {
                $relatedGridMaskTypes[$cpt] = array($gmt, array());
                $query = $this->om->createQuery("
                    SELECT tc, count(t) FROM Tisseo\EndivBundle\Entity\TripCalendar tc
                    JOIN tc.trips t
                    JOIN t.route r
                    JOIN r.lineVersion lv
                    JOIN tc.gridMaskType gmt
                    JOIN gmt.gridLinkCalendarMaskTypes glcmt
                    JOIN glcmt.gridCalendar gc
                    WHERE lv.id = ?1
                    AND gc.id = ?2
                    AND gmt.id = ?3
                    GROUP BY tc.id
                ");
                
                $query->setParameter(1, $lineVersionId);
                $query->setParameter(2, $gridCalendar->getId());
                $query->setParameter(3, $gmt->getId());

                $tripCalendars = $query->getResult();
                foreach($tripCalendars as $tripCalendar)
                {
                    $relatedGridMaskTypes[$cpt][1][] = $tripCalendar;
                }
                $cpt++;
            }
            $result[] = array($gridCalendar, $relatedGridMaskTypes);
        }
        return $result;
    }

    /*
     * attachGridCalendars
     * @param array $data
     *
     * Link all GridCalendar to their GridMaskType according to data passed as 
     * parameter which contains all ids.
     */
    public function attachGridCalendars($data)
    {
        $gridMaskTypeRepository = $this->om->getRepository('TisseoEndivBundle:GridMaskType');
        foreach($data as $gridCalendarId => $gridMaskTypeIds)
        {
            $gridCalendar = $this->repository->find($gridCalendarId);
            if (empty($gridMaskTypeIds))
            {
                if (!$gridCalendar->getGridLinkCalendarMaskTypes()->isEmpty())
                {
                    foreach($gridCalendar->getGridLinkCalendarMaskTypes() as $gridLinkCalendarMaskType)
                        $gridCalendar->removeGridLinkCalendarMaskType($gridLinkCalendarMaskType);
                    $this->om->persist($gridCalendar);
                }
            }
            else
            {
                if ($gridCalendar->updateLinks($gridMaskTypeIds))
                    $this->om->persist($gridCalendar);

                foreach($gridMaskTypeIds as $gridMaskTypeId)
                {
                    if ($gridCalendar->hasLinkToGridMaskType($gridMaskTypeId))
                        continue;

                    $gridMaskType = $gridMaskTypeRepository->find($gridMaskTypeId);
                    $gridLinkCalendarMaskType = new GridLinkCalendarMaskType($gridCalendar, $gridMaskType, true);
                    $this->om->persist($gridLinkCalendarMaskType);
                } 
            }
        }
        $this->om->flush();
    }

    /*
     * save
     * @param GridCalendar $gridCalendar
     * @return boolean
     */
    public function save(GridCalendar $gridCalendar)
    {
        $this->om->persist($gridCalendar);
        $this->om->flush();

        return true;
    }
}
