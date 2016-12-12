<?php

namespace Tisseo\EndivBundle\Services;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\EntityManager;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
use Tisseo\EndivBundle\Entity\Stop;
use Tisseo\EndivBundle\Entity\StopHistory;
use Tisseo\EndivBundle\Entity\StopAccessibility;
use Tisseo\EndivBundle\Entity\Waypoint;
use Tisseo\EndivBundle\Entity\AccessibilityMode;

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
    public function getRouteStopsByRoute($routeId)
    {
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
    public function getStops($waypointId)
    {
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
    public function getStopArea($route)
    {
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
    public function getStopLabel(Stop $stop)
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
        if (!$sh) {
            return "";
        }

        $label = $sh[0]["name"] . " " . $sh[0]["city"] . " (" . $sh[0]["code"] . ")";

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
            WHERE sh.stop = :stop
            ORDER BY sh.startDate DESC, sh.endDate DESC
        ");
        $query->setParameter('stop', $stop);

        return $query->getResult();
    }

    /**
     * TODO: COMMENT
     * TODO: Investigate why use of connection + is this function good ?
     * TODO: THERE IS A JSONCONTROLLER WHICH CALL FUNCTIONS LIKE THIS ONE:
     *       JSONMANAGER CENTRALIZATION THEN ? CAPSLOCK IS GOOD.
     *
     * if $stopAreaId argument is given, then stops that belong to this stopArea won't be returned
     */
    public function findStopsLike($term, $stopAreaId = null, $getPhantoms = false)
    {
        $specials = array("-", " ", "'");
        $cleanTerm = str_replace($specials, "_", $term);

        $connection = $this->em->getConnection()->getWrappedConnection();

        $query = "SELECT DISTINCT sh.short_name as name, c.name as city, sd.code as code, s.id as id
            FROM stop_history sh";
        if ($getPhantoms) {
            $query .= " JOIN stop s on (sh.stop_id = COALESCE(s.master_stop_id, s.id))";
        } else {
            $query .= " JOIN stop s on sh.stop_id = s.id ";
        }
        $query .= "LEFT JOIN stop_area sa on sa.id = s.stop_area_id
            LEFT JOIN city c on c.id = sa.city_id
            JOIN stop_datasource sd on sd.stop_id = s.id
            WHERE (UPPER(unaccent(sh.short_name)) LIKE UPPER(unaccent(:term))
            OR UPPER(unaccent(sh.long_name)) LIKE UPPER(unaccent(:term))
            OR UPPER(sd.code) LIKE UPPER(:term))";
        if (!is_null($stopAreaId)) {
            $query .= " AND (sa.id != :stop_area_id)";
        }
        $query .= " ORDER BY sh.short_name, c.name, sd.code";

        $stmt = $connection->prepare($query);
        $stmt->bindValue(':term', '%' . $cleanTerm . '%');
        if (!is_null($stopAreaId)) {
            $stmt->bindValue(':stop_area_id', $stopAreaId);
        }
        $stmt->execute();
        $stopHistories = $stmt->fetchAll();

        $result = array();
        foreach ($stopHistories as $stopHistory) {
            $result[] = array(
                "name" => $stopHistory["name"] . " " . $stopHistory["city"] . " (" . $stopHistory["code"] . ")",
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
     *
     * @param integer $stopId
     * @param StopAccessibility $stopAccessibility
     *
     * TODO: COMMENT
     */
    public function saveStopAccessibility($stopId, StopAccessibility $stopAccessibility)
    {
        $stop = $this->find($stopId);

        if (empty($stop)) {
            throw new \Exception("Can't save accessibility for the related stop with ID: " . $stopId . " because it doesn't exist.");
        }

        $stopAccessibility->setStop($stop);
        $stop->addStopAccessibility($stopAccessibility);

        $this->em->persist($stopAccessibility);
        $this->em->persist($stop);
        $this->em->flush();
    }

    /**
     * Delete
     *
     * @param integer $stopId
     * @param integer $stopAccessibilityId
     *
     * TODO: COMMENT
     */
    public function deleteStopAccessibility($stopId, $stopAccessibilityId)
    {
        $stop = $this->find($stopId);

        if (empty($stop)) {
            throw new \Exception("Can't delete accessibility for the related stop with ID: " . $stopId . " because it doesn't exist.");
        }

        $stopAccessibility = $stop->findStopAccessibility($stopAccessibilityId);

        if (empty($stopAccessibility)) {
            throw new \Exception("Can't delete accessibility with ID: " . $stopAccessibilityId . " because it doesn't exist.");
        }

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
     *
     * @param StopHistory $stopHistory
     * @param StopHistory $latestStopHistory
     *
     * TODO: COMMENT
     */
    public function createStopHistory(StopHistory $stopHistory, StopHistory $latestStopHistory)
    {
        // the new startDate is before some StopHistories startDate, delete them
        if ($latestStopHistory->getStartDate() >= $stopHistory->getStartDate()) {
            $youngerStopHistories = $stopHistory->getStop()->getYoungerStopHistories($stopHistory->getStartDate());
            foreach ($youngerStopHistories as $youngerStopHistory) {
                $this->em->remove($youngerStopHistory);
            }

            $this->em->flush();

            // updating end date
            $latestStopHistory = $stopHistory->getStop()->getLatestStopHistory();
            if ($latestStopHistory && $latestStopHistory->getEndDate() >= $stopHistory->getStartDate()) {
                $latestStopHistory->closeDate($stopHistory->getStartDate());
                $this->em->persist($latestStopHistory);
            }
        } else {
            if ($latestStopHistory->getEndDate() === null) {
                $latestStopHistory->closeDate($stopHistory->getStartDate());
                $this->em->persist($latestStopHistory);
            }
        }

        $this->em->persist($stopHistory);
        $this->em->persist($stopHistory->getStop());
        $this->em->flush();
    }

    /**
     * Save StopHistory
     *
     * @param StopHistory $stopHistory
     *
     * TODO: Create StopHistoryManager ?
     */
    public function saveStopHistory(StopHistory $stopHistory)
    {
        $this->em->persist($stopHistory);
        $this->em->flush();
    }

    /**
     * Delete StopHistory
     *
     * @param StopHistory $stopHistory
     *
     * TODO: Create StopHistoryManager ?
     */
    public function deleteStopHistory(StopHistory $stopHistory)
    {
        $this->em->remove($stopHistory);
        $this->em->flush();
    }

    /**
     * Detach
     *
     * @param integer $stopId
     *
     * Close last Stop's history and delete its link with its StopArea.
     */
    public function detach($stopId)
    {
        $stop = $this->find($stopId);

        if (empty($stop)) {
            throw new \Exception("Can't find the stop with ID: " . $stopId);
        }

        $now = new \Datetime();

        $youngerStopHistories = $stop->getYoungerStopHistories($now);
        foreach ($youngerStopHistories as $youngerStopHistory) {
            $this->em->remove($youngerStopHistory);
        }

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

    public function getStopsJson($stops, $getPhantoms = false)
    {
        $stopIds = array();
        $odtStops = array();

        foreach ($stops as $stop) {
            $stopIds[] = $stop->getId();
        }

        $connection = $this->em->getConnection();

        if ($getPhantoms) {
            $query = "SELECT DISTINCT s.id as id, s.master_stop_id as master_stop_id, sh.short_name as name, sd.code as code, ST_X(ST_Transform(sh.the_geom, 4326)) as x, ST_Y(ST_Transform(sh.the_geom, 4326)) as y
                FROM stop s
                JOIN stop_datasource sd on s.id = sd.stop_id
                JOIN stop_history sh on (sh.stop_id = COALESCE(s.master_stop_id, s.id))
                WHERE s.id IN (?)
                AND sh.start_date <= CURRENT_DATE
                AND (sh.end_date IS NULL OR sh.end_date > CURRENT_DATE)";
        } else {
            $query = "SELECT DISTINCT s.id as id, sh.short_name as name, sd.code as code, ST_X(ST_Transform(sh.the_geom, 4326)) as x, ST_Y(ST_Transform(sh.the_geom, 4326)) as y
                FROM stop s
                JOIN stop_datasource sd on sd.stop_id = s.id
                JOIN stop_history sh on sh.stop_id = s.id
                WHERE s.id IN (?)
                AND sh.start_date <= CURRENT_DATE
                AND (sh.end_date IS NULL OR sh.end_date > CURRENT_DATE)";
        }

        $stmt = $connection->executeQuery($query, array($stopIds), array(\Doctrine\DBAL\Connection::PARAM_INT_ARRAY));

        return $stmt->fetchAll();
    }

    /**
     * @param $stopId
     */
    public function getLinesByStop($stopId)
    {
        $query = $this->em->createQuery("
           SELECT l.id as line
           FROM Tisseo\EndivBundle\Entity\Line l
           JOIN l.lineVersions lv
           JOIN lv.routes r
           JOIN r.routeStops rs
           JOIN rs.waypoint w
           LEFT JOIN w.stop s
           LEFT JOIN w.odtArea oa
           LEFT JOIN oa.odtStops os WITH (os.endDate IS NULL OR os.endDate >= CURRENT_DATE())
           LEFT JOIN os.stop s2
           WHERE lv.startDate <= CURRENT_DATE() AND (lv.endDate IS NULL OR lv.endDate >= CURRENT_DATE())
           AND s.id = :sid OR s2.id = :sid
        ")->setParameter('sid', $stopId);

        $array = $query->getResult();
        $lines = array();
        foreach ($array as $item) {
            $lines[] = $item['line'];
        }

        $query = $this->em->createQuery("
            SELECT DISTINCT l
            FROM Tisseo\EndivBundle\Entity\Line l
            WHERE l.id IN (:lines)
        ")
            ->setParameter('lines', $lines);
        $linesResult = $query->getResult();

        $lines = array();
        foreach ($linesResult as $line) {
            $lines[$line->getId()] = $line;
        }

        return $lines;
    }

    /**
     * find locked stops
     */
    public function findLockedStops($lock = true)
    {
        return $this->repository->findBy(array('lock' => $lock));
    }

    /**
     * toggle lock stops
     */
    public function toggleLock(array $stopIds)
    {
        $stops = $this->repository->findById($stopIds);

        foreach ($stops as $stop) {
            $stop->setLock(!$stop->getLock());
            $this->em->persist($stop);
        }

        $this->em->flush();
    }
}
