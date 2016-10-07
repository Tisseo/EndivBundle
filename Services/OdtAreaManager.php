<?php

namespace Tisseo\EndivBundle\Services;

use Tisseo\EndivBundle\Entity\OdtArea;

class OdtAreaManager extends AbstractManager
{
    /**
     * {inheritdoc}
     */
    public function findAll()
    {
        return $this->getRepository()->findBy(array(), array('name' => 'ASC'));
    }

    public function create(OdtArea $odtArea)
    {
        $objectManager = $this->getObjectManger();
        $connection = $objectManager->getConnection()->getWrappedConnection();
        $stmt = $connection->prepare("INSERT INTO waypoint(id) VALUES (nextval('waypoint_id_seq')) RETURNING waypoint.id");
        $stmt->execute();
        $odtArea->setId($stmt->fetch(\PDO::FETCH_ASSOC)["id"]);

        $objectManager->persist($odtArea);
        $objectManager->flush();

        return $odtArea->getId();
    }

    /**
     * Remove a OdtArea
     *
     * @param OdtArea $odtArea
     */
    public function remove(OdtArea $odtArea)
    {
        $objectManager = $this->getObjectManger();

        $waypoint = $odtArea->getWaypoint();
        $query = $objectManager->createQuery(
            "
            SELECT count(r)
            FROM Tisseo\EndivBundle\Entity\RouteStop r
            WHERE r.waypoint = :wp
        "
        )
            ->setParameter('wp', $waypoint);

        $count = $query->getSingleScalarResult();
        if ($count > 0) {
            throw new \Exception('Suppression impossible au motif que la zone "'.$odtArea->getName().'" est encore utilisée dans un ou plusieurs itinéraires');
        }

        $odtArea->getOdtStops()->clear();
        $objectManager->remove($odtArea);
        $objectManager->refresh($waypoint);
        $objectManager->flush();
        $query = $objectManager->createQuery(
            "
            DELETE
            FROM Tisseo\EndivBundle\Entity\Waypoint w
            WHERE w = :wp
        "
        )
            ->setParameter('wp', $waypoint);
        $query->execute();
    }

    public function getLines(OdtArea $odtArea)
    {
        $objectManager = $this->getObjectManger();

        $query = $objectManager->createQuery(
            "
           SELECT l
           FROM Tisseo\EndivBundle\Entity\Line l
           JOIN l.lineVersions lv
           JOIN lv.routes r
           JOIN r.routeStops rs
           JOIN rs.waypoint w
           JOIN w.stop s
           JOIN s.stopDatasources sd
           JOIN s.odtStops os
           WHERE (os.endDate IS NULL OR os.endDate >= CURRENT_DATE())
           AND os.odtArea = :oar
           ORDER BY l.priority
        "
        )
            ->setParameter('oar', $odtArea);
        $result = $query->getResult();

        return $result;
    }

    public function getLinesByOdtArea()
    {
        $objectManager = $this->getObjectManger();

        $query = $objectManager->createQuery(
            "
            SELECT DISTINCT oar.id as odtArea, oar2.id as odtArea2, l.id as line, l.priority as priority, l.number as number
            FROM Tisseo\EndivBundle\Entity\Line l
            JOIN l.lineVersions lv
            JOIN lv.routes r
            JOIN r.routeStops rs
            JOIN rs.waypoint w
            LEFT JOIN w.stop s
            LEFT JOIN s.odtStops os WITH (os.endDate IS NULL OR os.endDate >= CURRENT_DATE())
            LEFT JOIN os.odtArea oar
            LEFT JOIN w.odtArea oar2
            WHERE (lv.endDate IS NULL OR lv.endDate >= CURRENT_DATE())
            ORDER BY l.priority
        "
        );
        // There is a bug with getResult() and custom request SELECT s.id, l
        // leading to missing rows in the result array.
        $array = $query->getResult();
        $lines = array();
        foreach ($array as $item) {
            $lines[] = $item['line'];
        }

        $query = $objectManager->createQuery(
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

        $result = array();
        foreach ($array as $item) {
            if (!empty($item['odtArea'])) {
                $result[$item['odtArea']][] = $lines[$item['line']];
            } else {
                $result[$item['odtArea2']][] = $lines[$item['line']];
            }
        }

        foreach ($result as $key => $item) {
            $result[$key] = $this->sortLinesByNumber($item);
        }

        return $result;
    }

    public function findOdtAreasLike($term)
    {
        $objectManager = $this->getObjectManger();

        $specials = array("-", " ", "'");
        $cleanTerm = str_replace($specials, "_", $term);

        $connection = $objectManager->getConnection()->getWrappedConnection();
        $stmt = $connection->prepare(
            "
            SELECT oa.name as name, oa.id as id
            FROM odt_area oa
            WHERE (UPPER(unaccent(oa.name)) LIKE UPPER(unaccent(:term)))
            ORDER BY oa.name
        "
        );
        $stmt->bindValue(':term', '%'.$cleanTerm.'%');
        $stmt->execute();
        $odtAreas = $stmt->fetchAll();

        return $odtAreas;
    }

    public function getOdtStopsJson($odtArea)
    {
        $objectManager = $this->getObjectManger();

        $connection = $objectManager->getConnection()->getWrappedConnection();

        $query="SELECT DISTINCT s.id as id, s.master_stop_id as master_stop_id, sh.short_name as name, sd.code as code, ST_X(ST_Transform(sh.the_geom, 4326)) as x, ST_Y(ST_Transform(sh.the_geom, 4326)) as y
            FROM stop s
            JOIN odt_stop os on os.stop_id = s.id
            JOIN stop_datasource sd on sd.stop_id = s.id
            JOIN stop_history sh on (sh.stop_id = COALESCE(s.master_stop_id, s.id))
            WHERE os.odt_area_id = :oa_id
            AND (os.end_date IS NULL or os.end_date >= CURRENT_DATE)
            AND sh.start_date <= CURRENT_DATE
            AND (sh.end_date IS NULL OR sh.end_date > CURRENT_DATE)";
        $stmt = $connection->prepare($query);
        $stmt->bindValue(':oa_id', $odtArea->getId());
        $stmt->execute();
        $result = $stmt->fetchAll();

        return $result;
    }
}
