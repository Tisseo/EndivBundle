<?php

namespace Tisseo\EndivBundle\Services;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\EntityManager;

use Tisseo\EndivBundle\Entity\Stop;
use Tisseo\EndivBundle\Entity\StopHistory;
use Tisseo\EndivBundle\Entity\StopAccessibility;
use Tisseo\EndivBundle\Entity\Waypoint;
use CrEOF\Spatial\PHP\Types\Geometry\Point;

class StopManager extends SortManager
{
    private $em = null;
    private $repository = null;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->repository = $em->getRepository('TisseoEndivBundle:Stop');
    }

    public function findAll()
    {
        return ($this->repository->findAll());
    }

    public function find($stopId)
    {
        return empty($stopId) ? null : $this->repository->find($stopId);
    }

    public function create(Stop $stop)
    {
        $connection = $this->em->getConnection()->getWrappedConnection();
        $stmt = $connection->prepare("INSERT INTO waypoint(id) VALUES (nextval('waypoint_id_seq')) RETURNING waypoint.id");
        $stmt->execute();
        $stop->setId($stmt->fetch(\PDO::FETCH_ASSOC)["id"]);

        $this->em->persist($stop);
        $this->em->flush();

        return $stop->getId();
    }

    public function save(Stop $stop)
    {
        $this->em->persist($stop);
        $this->em->flush();
    }

    // TODO: Does this belong here ?
    public function getRouteStopsByRoute($routeId) {
        $query = $this->em->createQuery("
            SELECT rs.id, rs.rank, rs.pickup, rs.dropOff, wp.id as waypoint
            FROM Tisseo\EndivBundle\Entity\RouteStop rs
            JOIN rs.waypoint wp
            WHERE rs.route = :id
            ORDER BY rs.rank
        ")
        ->setParameter('id', $routeId);

        return $query->getResult();
    }

    // TODO: Investigate this function
    public function getStops($waypointId) {
        $query = $this->em->createQuery("
            SELECT st.id, ar.shortName, ar.id as zone, c.name as city, sd.code as code
            FROM Tisseo\EndivBundle\Entity\Stop st
            JOIN st.stopArea ar
            JOIN ar.city c
            JOIN st.stopDatasources sd
            WHERE st.id = :id
        ")
        ->setParameter('id', $waypointId);

        return $query->getResult();
    }

    // TODO: WTF ?? route ??
    public function getStopArea($route) {
        $query = $this->em->createQuery("
            SELECT st.id, ar.shortName, ar.id as zone,  c.name as city
            FROM Tisseo\EndivBundle\Entity\Stop st
            JOIN st.stopArea ar
            JOIN ar.city c
            WHERE st.id = :id
        ")->setParameter('id', $route);

        return $query->getResult();
    }

    // TODO: AAAAAAAAAAAAH !!
    public function getStopLabel( Stop $stop )
    {
        $query = $this->em->createQuery("
            SELECT sh.shortName as name, c.name as city,  sd.code as code, s.id as id
            FROM Tisseo\EndivBundle\Entity\StopHistory sh
            JOIN sh.stop s
            JOIN s.stopArea sa
            JOIN sa.city c
            JOIN s.stopDatasources sd
            WHERE sh.stop = :stop
            AND sh.startDate <= CURRENT_DATE()
            AND (sh.endDate IS NULL or sh.endDate >= CURRENT_DATE())
        ")
        ->setParameter('stop', $stop);

        $sh = $query->getResult();
        if( !$sh ) return "";

        $label = $sh[0]["name"]." ".$sh[0]["city"]." (".$sh[0]["code"].")";
        return $label;
    }

    //
    // VERIFIED USEFUL FUNCTIONS
    //

    /**
     * TODO: REPLACE BY CRITERIA IN STOP ENTITY
     * OR USE A DOCTRINE RULE IN MAPPING FILE
     */
    public function getOrderedStopHistories($stop)
    {
        $query = $this->em->createQuery("
            SELECT sh FROM Tisseo\EndivBundle\Entity\StopHistory sh
            JOIN sh.stop s
            WHERE sh.stop = :stop
            ORDER BY sh.startDate DESC
        ");
        $query->setParameter('stop', $stop);

        return $query->getResult();
    }

    /**
     * TODO: COMMENT
     * TODO: Investigate why use of connection + is this function good ?
     * TODO: THERE IS A JSONCONTROLLER WHICH CALL FUNCTIONS LIKE THIS ONE:
     *       JSONMANAGER CENTRALIZATION THEN ? CAPSLOCK IS GOOD.
     */
    public function findStopsLike($term)
    {
        $specials = array("-", " ", "'");
        $cleanTerm = str_replace($specials, "_", $term);

        $connection = $this->em->getConnection()->getWrappedConnection();
        $stmt = $connection->prepare("
            SELECT sh.short_name as name, c.name as city, sd.code as code, s.id as id
            FROM stop_history sh
            JOIN stop s on sh.stop_id = s.id
            LEFT JOIN stop_area sa on sa.id = s.stop_area_id
            LEFT JOIN city c on c.id = sa.city_id
            JOIN stop_datasource sd on sd.stop_id = s.id
            WHERE (UPPER(unaccent(sh.short_name)) LIKE UPPER(unaccent(:term))
            OR UPPER(unaccent(sh.long_name)) LIKE UPPER(unaccent(:term))
            OR UPPER(sd.code) LIKE UPPER(:term))
            ORDER BY sh.short_name, c.name, sd.code
        ");
        $stmt->bindValue(':term', '%'.$cleanTerm.'%');
        $stmt->execute();
        $stopHistories = $stmt->fetchAll();

        $result = array();
        foreach ($stopHistories as $stopHistory) {
            $result[] = array(
                "name" => $stopHistory["name"]." ".$stopHistory["city"]." (".$stopHistory["code"].")",
                "id" => $stopHistory["id"]
            );
        }

        return $result;
    }

    /**
     * TODO: CREATE A NEW SPECIFIC MANAGER ?
     * StopAccessibility functions below
     */

    /**
     * Save
     * @param integer $stopId
     * @param StopAccessibility $stopAccessibility
     *
     * TODO: COMMENT
     */
    public function saveStopAccessibility($stopId, StopAccessibility $stopAccessibility)
    {
        $stop = $this->find($stopId);

        if (empty($stop))
            throw new \Exception("Can't save accessibility for the related stop with ID: ".$stopId." because it doesn't exist.");

        $stopAccessibility->setStop($stop);
        $stop->addStopAccessibility($stopAccessibility);

        $this->em->persist($stopAccessibility);
        $this->em->persist($stop);
        $this->em->flush();
    }

    /**
     * Delete
     * @param integer $stopId
     * @param integer $stopAccessibilityId
     *
     * TODO: COMMENT
     */
    public function deleteStopAccessibility($stopId, $stopAccessibilityId)
    {
        $stop = $this->find($stopId);

        if (empty($stop))
            throw new \Exception("Can't delete accessibility for the related stop with ID: ".$stopId." because it doesn't exist.");

        $stopAccessibility = $stop->findStopAccessibility($stopAccessibilityId);

        if (empty($stopAccessibility))
            throw new \Exception("Can't delete accessibility with ID: ".$stopAccessibilityId." because it doesn't exist.");

        $stop->removeStopAccessibility($stopAccessibility);
        $this->em->remove($stopAccessibility);
        $this->em->persist($stop);
        $this->em->flush();
    }

    /**
     * TODO: CREATE A NEW SPECIFIC MANAGER ?
     * StopHistory functions below
     */

    /**
     * Save
     * @param StopHistory $stopHistory
     * @param StopHistory $latestStopHistory
     *
     * TODO: COMMENT
     */
    public function createStopHistory(StopHistory $stopHistory, StopHistory $latestStopHistory)
    {
        // the new startDate is before some StopHistories startDate, delete them
        if ($latestStopHistory->getStartDate() >= $stopHistory->getStartDate())
        {
            $youngerStopHistories = $stopHistory->getStop()->getYoungerStopHistories($stopHistory->getStartDate());
            foreach ($youngerStopHistories as $youngerStopHistory)
                $this->em->remove($youngerStopHistory);

            $this->em->flush();

            // updating end date
            $latestStopHistory = $stopHistory->getStop()->getLatestStopHistory();
            if ($latestStopHistory && $latestStopHistory->getEndDate() >= $stopHistory->getStartDate())
            {
                $latestStopHistory->closeDate($stopHistory->getStartDate());
                $this->em->persist($latestStopHistory);
            }
        }
        else if ($latestStopHistory->getEndDate() === null)
        {
            $latestStopHistory->closeDate($stopHistory->getStartDate());
            $this->em->persist($latestStopHistory);
        }

        $this->em->persist($stopHistory);
        $this->em->persist($stopHistory->getStop());
        $this->em->flush();

        $phantoms = $stopHistory->getStop()->getPhantoms();

        foreach ($phantoms as $phantom) {
            $phantomHistory = clone $stopHistory;
            $phantomHistory->setStop($phantom);
            $this->createStopHistory($phantomHistory, $this->getLatestStopHistory($phantom->getId()));
        }
    }

    /**
     * Close
     * @param StopHistory $stopHistory
     *
     * TODO: COMMENT
     */
    public function closeStopHistory(StopHistory $stopHistory)
    {
         $this->em->persist($stopHistory);

         $phantoms = $stopHistory->getStop()->getPhantoms();

         foreach ($phantoms as $phantom) {
             $phantomHistory = $this->getLatestStopHistory($phantom->getId());
             $phantomHistory->setEndDate($stopHistory->getEndDate());
             $this->em->persist($phantomHistory);
         }

         $this->em->flush();
    }

    /**
     * Delete
     * @param StopHistory $stopHistory
     *
     * TODO: COMMENT
     */
    public function deleteStopHistory(StopHistory $stopHistory)
    {
        $phantoms = $stopHistory->getStop()->getPhantoms();

        foreach ($phantoms as $phantom) {
             $phantomHistory = $this->getLatestStopHistory($phantom->getId());
             $this->em->remove($phantomHistory);
        }

        $this->em->remove($stopHistory);
        $this->em->flush();
    }

    /**
     * Detach
     * @param integer $stopId
     *
     * Close last Stop's history and delete its link with its StopArea.
     */
    public function detach($stopId)
    {
        $stop = $this->find($stopId);

        if (empty($stop))
            throw new \Exception("Can't find the stop with ID: ".$stopId);

        $now = new \Datetime();

        $youngerStopHistories = $stop->getYoungerStopHistories($now);
        foreach ($youngerStopHistories as $youngerStopHistory)
            $this->em->remove($youngerStopHistory);

        $this->em->flush();

        $stopHistory = $stop->getLatestStopHistory();
        $stopHistory->closeDate(new \Datetime());
        $this->em->persist($stopHistory);

        $stopAreaId = $stop->getStopArea()->getId();
        $stop->setStopArea();
        $this->em->persist($stop);
        $this->em->flush();

        return $stopAreaId;
    }
}
