<?php

namespace Tisseo\EndivBundle\Services;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\EntityManager;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
use Tisseo\EndivBundle\Entity\OdtArea;
use Tisseo\EndivBundle\Entity\OdtStop;
use Tisseo\EndivBundle\Entity\Waypoint;

class OdtAreaManager extends SortManager
{
    private $em = null;
    private $repository = null;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->repository = $em->getRepository('TisseoEndivBundle:OdtArea');
    }

    public function findAll()
    {
        return ($this->repository->findAll());
    }

    public function find($odtAreaId)
    {
        return empty($odtAreaId) ? null : $this->repository->find($odtAreaId);
    }

    public function create(OdtArea $odtArea)
    {
        $connection = $this->em->getConnection()->getWrappedConnection();
        $stmt = $connection->prepare("INSERT INTO waypoint(id) VALUES (nextval('waypoint_id_seq')) RETURNING waypoint.id");
        $stmt->execute();
        $odtArea->setId($stmt->fetch(\PDO::FETCH_ASSOC)["id"]);

        $this->em->persist($odtArea);
        $this->em->flush();

        return $odtArea->getId();
    }

    public function save(OdtArea $odtArea)
    {
        $this->em->persist($odtArea);
        $this->em->flush();
    }

    /**
       * delete
       * @param OdtArea $odtArea
       *
       * Delete a OdtArea from the database.
       */
    public function delete(OdtArea $odtArea)
    {
        $waypoint = $odtArea->getWaypoint();
        $query = $this->em->createQuery("
            SELECT count(r)
            FROM Tisseo\EndivBundle\Entity\RouteStop r
            WHERE r.waypoint = :wp
        ")
        ->setParameter('wp', $waypoint);
        $count = $query->getSingleScalarResult();
        if ($count > 0)
            throw new \Exception('Suppression impossible au motif que la zone "'.$odtArea->getName().'" est encore utilisée dans un ou plusieurs itinéraires');
        $odtArea->getOdtStops()->clear();
        $this->em->flush();
        $this->em->remove($odtArea);
        $this->em->refresh($waypoint);
        $this->em->flush();
        $this->em->remove($waypoint);
        $this->em->flush();
    }

    public function getLines(OdtArea $odtArea)
    {
        $query = $this->em->createQuery("
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
        ")
        ->setParameter('oar', $odtArea);
        $result = $query->getResult();

        return $result;
    }

    public function getLinesByOdtArea()
    {
        $query = $this->em->createQuery("
            SELECT DISTINCT oar.id as odtArea, l.id as line, l.priority as priority, l.number as number
            FROM Tisseo\EndivBundle\Entity\Line l
            JOIN l.lineVersions lv
            JOIN lv.routes r
            JOIN r.routeStops rs
            JOIN rs.waypoint w
            JOIN w.stop s
            JOIN s.stopDatasources sd
            JOIN s.odtStops os
            JOIN os.odtArea oar
            WHERE (os.endDate IS NULL OR os.endDate >= CURRENT_DATE())
            AND (lv.endDate IS NULL OR lv.endDate >= CURRENT_DATE())
            ORDER BY l.priority
        ");
        // There is a bug with getResult() and custom request SELECT s.id, l
        // leading to missing rows in the result array.
        $array = $query->getResult();
        $lines = array();
        foreach ($array as $item)
            $lines[] = $item['line'];

        $query = $this->em->createQuery("
            SELECT DISTINCT l
            FROM Tisseo\EndivBundle\Entity\Line l
            WHERE l.id IN (:lines)
        ")
        ->setParameter('lines', $lines);
        $linesResult = $query->getResult();

        $lines = array();
        foreach($linesResult as $line)
            $lines[$line->getId()] = $line;

        $result = array();
        foreach ($array as $item) {
            $result[$item['odtArea']][] = $lines[$item['line']];
        }

        foreach ($result as $key => $item) {
            $result[$key] = $this->sortLinesByNumber($item);
        }

        return $result;
    }

}
