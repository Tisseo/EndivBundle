<?php

namespace Tisseo\EndivBundle\Services;

use Doctrine\Common\Collections\Collection;
use Tisseo\EndivBundle\Entity\GridLinkCalendarMaskType;

class GridCalendarManager extends AbstractManager
{
    /**
     * findRelatedGridMaskTypes
     *
     * @param  Collection $gridCalendars
     * @param  inetger    $lineVersionId
     * @return array()
     *
     * Retrieve all GridMaskType linked to a specific LineVersion and
     * GridCalendars.
     */
    public function findRelatedGridMaskTypes(Collection $gridCalendars, $lineVersionId)
    {
        $result = array();
        $objectManager = $this->getObjectManager();
        foreach ($gridCalendars as $gridCalendar) {
            $query = $objectManager->createQuery(
                "
                SELECT gmt FROM Tisseo\EndivBundle\Entity\GridMaskType gmt
                JOIN gmt.gridLinkCalendarMaskTypes glcmt
                JOIN glcmt.gridCalendar gc
                JOIN Tisseo\EndivBundle\Entity\Route r WITH r.lineVersion = gc.lineVersion
                WHERE IDENTITY(r.lineVersion) = ?1
                AND gc.id = ?2
                GROUP BY gmt.id
                ORDER BY gmt.id
            "
            );

            $query->setParameter(1, $lineVersionId);
            $query->setParameter(2, $gridCalendar->getId());

            $gmts = $query->getResult();
            $relatedGridMaskTypes = array();
            $cpt = 0;

            foreach ($gmts as $gmt) {
                $relatedGridMaskTypes[$cpt] = array($gmt, array());
                $query = $objectManager->createQuery(
                    "
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
                "
                );

                $query->setParameter(1, $lineVersionId);
                $query->setParameter(2, $gridCalendar->getId());
                $query->setParameter(3, $gmt->getId());

                $tripCalendars = $query->getResult();
                foreach ($tripCalendars as $tripCalendar) {
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
        $gmtRepository = $this->getRepository('TisseoEndivBundle:GridMaskType');
        $objectManager = $this->getObjectManager();

        foreach ($data as $gridCalendarId => $gridMaskTypeIds) {
            $gridCalendar = $this->find($gridCalendarId);
            if (empty($gridMaskTypeIds)) {
                if (!$gridCalendar->getGridLinkCalendarMaskTypes()->isEmpty()) {
                    foreach ($gridCalendar->getGridLinkCalendarMaskTypes() as $glcmt) {
                        $gridCalendar->removeGridLinkCalendarMaskType($glcmt);
                    }
                    $objectManager->persist($gridCalendar);
                }
            } else {
                if ($gridCalendar->updateLinks($gridMaskTypeIds)) {
                    $objectManager->persist($gridCalendar);
                }

                foreach ($gridMaskTypeIds as $gridMaskTypeId) {
                    if ($gridCalendar->hasLinkToGridMaskType($gridMaskTypeId)) {
                        continue;
                    }

                    $gridMaskType = $gmtRepository->find($gridMaskTypeId);
                    $glcmt = new GridLinkCalendarMaskType($gridCalendar, $gridMaskType, true);
                    $objectManager->persist($glcmt);
                }
            }
        }
        $objectManager->flush();
    }

    /*
     * Find trips
     * @param Collection $gridCalendars
     *
     * Find all trips linked to each GridCalendar in collection.
     */
    public function findRelatedTrips(Collection $gridCalendars)
    {
        $result = array();
        $objectManager = $this->getObjectManager();
        foreach ($gridCalendars as $gridCalendar) {
            $query = $objectManager->createQuery(
                "
                SELECT t FROM Tisseo\EndivBundle\Entity\Trip t
                JOIN t.route r
                JOIN Tisseo\EndivBundle\Entity\GridCalendar gc WITH gc.lineVersion = r.lineVersion
                JOIN gc.gridLinkCalendarMaskTypes glcmt
                JOIN Tisseo\EndivBundle\Entity\TripCalendar tc WITH t.tripCalendar = tc AND tc.gridMaskType = glcmt.gridMaskType
                WHERE gc.id = ?1
                ORDER BY t.id
            "
            );
            $query->setParameter(1, $gridCalendar->getId());
            $data = $query->getResult();

            $trips = array();
            foreach ($data as $trip) {
                $trips[$trip->getId()] = array("trip" => $trip);
            }

            $query = $objectManager->createQuery(
                "
                SELECT t.id, rs.rank, sh.shortName, st.departureTime FROM Tisseo\EndivBundle\Entity\Trip t
                JOIN t.stopTimes st
                JOIN st.routeStop rs
                JOIN Tisseo\EndivBundle\Entity\Stop s WITH s.id = rs.waypoint
                JOIN Tisseo\EndivBundle\Entity\StopHistory sh WITH sh.stop = s.id OR sh.stop = s.masterStop
                WHERE t.id IN (?1)
                AND (rs.rank = (
                    SELECT max(sub_rs.rank) FROM Tisseo\EndivBundle\Entity\RouteStop sub_rs
                    WHERE sub_rs.route = rs.route
                )
                OR rs.rank = 1)
                ORDER BY t.id, rs.rank
            "
            );
            $query->setParameter(1, array_keys($trips));
            $data = $query->getResult();

            foreach ($data as $tripData) {
                if ($tripData['rank'] === 1) {
                    $trips[$tripData['id']]['start_name'] = $tripData['shortName'];
                    $trips[$tripData['id']]['start_time'] = $this->secondsToTime(intval($tripData['departureTime']));
                } else {
                    $trips[$tripData['id']]['end_name'] = $tripData['shortName'];
                    $trips[$tripData['id']]['end_time'] = $this->secondsToTime(intval($tripData['departureTime']));
                }
            }

            $result[] = array($gridCalendar, $trips);
        }
        return $result;
    }

    private function secondsToTime($seconds)
    {
        $hours = (string)floor(($seconds % 86400) / 3600);
        $minutes = (string)floor(($seconds % 86400 % 3600) / 60);

        if (strlen($hours) === 1) {
            $hours = "0".$hours;
        }
        if (strlen($minutes) === 1) {
            $minutes = "0".$minutes;
        }

        return $hours.":".$minutes;
    }
}
