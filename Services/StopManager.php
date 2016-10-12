<?php

namespace Tisseo\EndivBundle\Services;

use Tisseo\EndivBundle\Entity\Stop;
use Tisseo\EndivBundle\Entity\StopAccessibility;

class StopManager extends AbstractManager
{
    public function create(Stop $stop)
    {
        $objectManager = $this->getObjectManager();

        $connection = $objectManager->getConnection()->getWrappedConnection();
        $stmt = $connection->prepare("INSERT INTO waypoint(id) VALUES (nextval('waypoint_id_seq')) RETURNING waypoint.id");
        $stmt->execute();
        $stop->setId($stmt->fetch(\PDO::FETCH_ASSOC)["id"]);

        $objectManager->persist($stop);
        $objectManager->flush();

        return $stop->getId();
    }

    public function save(Stop $stop)
    {
        $objectManager->persist($stop);
        $objectManager->flush();
    }

    public function getRouteStopsByRoute($routeId)
    {
        $query = $this->getObjectManager()->createQuery(
            "
            SELECT rs.id, rs.rank, rs.pickup, rs.dropOff, wp.id as waypoint
            FROM Tisseo\EndivBundle\Entity\RouteStop rs
            JOIN rs.waypoint wp
            WHERE rs.route = :id
            ORDER BY rs.rank
        "
        )
            ->setParameter('id', $routeId);

        return $query->getResult();
    }

    public function getStops($waypointId)
    {
        $query = $this->getObjectManager()->createQuery(
            "
            SELECT st.id, ar.shortName, ar.id as zone, c.name as city, sd.code as code
            FROM Tisseo\EndivBundle\Entity\Stop st
            JOIN st.stopArea ar
            JOIN ar.city c
            JOIN st.stopDatasources sd
            WHERE st.id = :id
        "
        )
            ->setParameter('id', $waypointId);

        return $query->getResult();
    }

    public function getStopArea($route)
    {
        $query = $this->getObjectManager()->createQuery(
            "
            SELECT st.id, ar.shortName, ar.id as zone,  c.name as city
            FROM Tisseo\EndivBundle\Entity\Stop st
            JOIN st.stopArea ar
            JOIN ar.city c
            WHERE st.id = :id
        "
        )->setParameter('id', $route);

        return $query->getResult();
    }

    public function getStopLabel(Stop $stop)
    {
        $query = $this->getObjectManager()->createQuery(
            "
            SELECT sh.shortName as name, c.name as city,  sd.code as code, s.id as id
            FROM Tisseo\EndivBundle\Entity\StopHistory sh
            JOIN sh.stop s
            JOIN s.stopArea sa
            JOIN sa.city c
            JOIN s.stopDatasources sd
            WHERE sh.stop = :stop
            AND sh.startDate <= CURRENT_DATE()
            AND (sh.endDate IS NULL or sh.endDate >= CURRENT_DATE())
        "
        )
            ->setParameter('stop', $stop);

        $sh = $query->getResult();
        if (!$sh) {
            return "";
        }

        $label = $sh[0]["name"]." ".$sh[0]["city"]." (".$sh[0]["code"].")";
        return $label;
    }

    /**
     * if $stopAreaId argument is given, then stops that belong to this stopArea won't be returned
     */
    public function findStopsLike($term, $stopAreaId = null, $getPhantoms = false)
    {
        $specials = array("-", " ", "'");
        $cleanTerm = str_replace($specials, "_", $term);

        $connection = $this->getObjectManager()->getConnection()->getWrappedConnection();

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
        $stmt->bindValue(':term', '%'.$cleanTerm.'%');
        if (!is_null($stopAreaId)) {
            $stmt->bindValue(':stop_area_id', $stopAreaId);
        }
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
     * Add a new StopAccecssibility to a Stop
     *
     * @param integer           $stopId
     * @param StopAccessibility $stopAccessibility
     */
    public function saveStopAccessibility($stopId, StopAccessibility $stopAccessibility)
    {
        $stop = $this->find($stopId);

        if (empty($stop)) {
            throw new \Exception("Can't save accessibility for the related stop with ID: ".$stopId." because it doesn't exist.");
        }

        $stopAccessibility->setStop($stop);
        $stop->addStopAccessibility($stopAccessibility);

        $objectManager->persist($stopAccessibility);
        $objectManager->persist($stop);

        $objectManager->flush();
    }

    /**
     * Delete a StopAccessibility of a Stop
     *
     * @param integer $stopId
     * @param integer $stopAccessibilityId
     */
    public function deleteStopAccessibility($stopId, $stopAccessibilityId)
    {
        $stop = $this->find($stopId);

        if (empty($stop)) {
            throw new \Exception("Can't delete accessibility for the related stop with ID: ".$stopId." because it doesn't exist.");
        }

        $stopAccessibility = $stop->findStopAccessibility($stopAccessibilityId);

        if (empty($stopAccessibility)) {
            throw new \Exception("Can't delete accessibility with ID: ".$stopAccessibilityId." because it doesn't exist.");
        }

        $stop->removeStopAccessibility($stopAccessibility);
        $objectManager->remove($stopAccessibility);
        $objectManager->persist($stop);

        $objectManager->flush();
    }

    /**
     * Close last StopHistory of a Stop and unlink its StopArea.
     *
     * @param  integer $stopId
     * @return integer
     */
    public function detach($stopId)
    {
        $stop = $this->find($stopId);

        if (empty($stop)) {
            throw new \Exception("Can't find the stop with ID: ".$stopId);
        }

        $now = new \Datetime();

        $youngerStopHistories = $stop->getYoungerStopHistories($now);
        foreach ($youngerStopHistories as $youngerStopHistory) {
            $objectManager->remove($youngerStopHistory);
        }

        $objectManager->flush();

        $stopHistory = $stop->getLatestStopHistory();
        $stopHistory->closeDate($now);
        $objectManager->persist($stopHistory);

        $stopAreaId = $stop->getStopArea()->getId();
        $stop->setStopArea();
        $objectManager->persist($stop);
        $objectManager->flush();

        return $stopAreaId;
    }

    public function getStopsJson($stops, $getPhantoms = false)
    {
        $stopIds = array();
        foreach ($stops as $stop) {
            $stopIds[] = $stop->getId();
        }
        $connection = $this->getObjectManager()->getConnection();

        if ($getPhantoms) {
            $query="SELECT DISTINCT s.id as id, s.master_stop_id as master_stop_id, sh.short_name as name, sd.code as code, ST_X(ST_Transform(sh.the_geom, 4326)) as x, ST_Y(ST_Transform(sh.the_geom, 4326)) as y
                FROM stop s
                JOIN stop_datasource sd on s.id = sd.stop_id
                JOIN stop_history sh on (sh.stop_id = COALESCE(s.master_stop_id, s.id))
                WHERE s.id IN (?)
                AND sh.start_date <= CURRENT_DATE
                AND (sh.end_date IS NULL OR sh.end_date > CURRENT_DATE)";
        } else {
            $query="SELECT DISTINCT s.id as id, sh.short_name as name, sd.code as code, ST_X(ST_Transform(sh.the_geom, 4326)) as x, ST_Y(ST_Transform(sh.the_geom, 4326)) as y
                FROM stop s
                JOIN stop_datasource sd on sd.stop_id = s.id
                JOIN stop_history sh on sh.stop_id = s.id
                WHERE s.id IN (?)
                AND sh.start_date <= CURRENT_DATE
                AND (sh.end_date IS NULL OR sh.end_date > CURRENT_DATE)";
        }
        $stmt = $connection->executeQuery($query, array($stopIds), array(\Doctrine\DBAL\Connection::PARAM_INT_ARRAY));
        $result = $stmt->fetchAll();

        return $result;
    }

    /**
     * @param $stopId
     */
    public function getLinesByStop($stopId)
    {
        $query = $this->getObjectManager()->createQuery(
            "
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
        "
        )->setParameter('sid', $stopId);

        $array = $query->getResult();
        $lines = array();
        foreach ($array as $item) {
            $lines[] = $item['line'];
        }

        $query = $this->getObjectManager()->createQuery(
            "
            SELECT DISTINCT l
            FROM Tisseo\EndivBundle\Entity\Line l
            WHERE l.id IN (:lines)
        "
        )
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
        return $this->getRepository()->findBy(array('lock' => $lock));
    }

    /**
     * toggle lock stops
     */
    public function toggleLock(array $stopIds)
    {
        $stops = $this->getRepository()->findById($stopIds);

        foreach ($stops as $stop) {
            $stop->setLock(!$stop->getLock());
            $objectManager->persist($stop);
        }

        $objectManager->flush();
    }

    /**
     * Get all related stops of a stopArea ordered by code
     *
     * @param  integer $stopAreaId
     * @param  boolean $getPhantoms
     * @return Doctrine\Common\Collections\Collection
     */
    public function getStopsOrderedByCode($stopAreaId, $getPhantoms = false)
    {
        $query = $this->getRepository()->createQueryBuilder('s')
            ->join('s.stopDatasources', 'sd')
            ->where('s.stopArea = :sa')
            ->orderBy('sd.code')
            ->setParameter('sa', $stopAreaId);

        if ($getPhantoms === false) {
            $query->join('s.stopHistories', 'sh');
        }

        return $query->getQuery()->getResult();
    }
}
