<?php

namespace Tisseo\EndivBundle\Services;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityManager;
use Tisseo\EndivBundle\Entity\StopArea;
use Tisseo\EndivBundle\Entity\Transfer;
use Tisseo\EndivBundle\Entity\Alias;

class StopAreaManager extends SortManager
{
    private $em = null;
    private $repository = null;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->repository = $em->getRepository('TisseoEndivBundle:StopArea');
    }

    public function findAll()
    {
        return ($this->repository->findAll());
    }

    public function find($stopAreaId)
    {
        return empty($stopAreaId) ? null : $this->repository->find($stopAreaId);
    }

    public function findByCityId($cityId, $search = array(), $orderParams = null, $limit = null, $offset = null)
    {
        $q = $this->repository->createQueryBuilder('q');
        $q->where($q->expr()->eq('q.city', ':cityId'))
            ->setParameter('cityId', $cityId);

        $this->buildSearchQuery($search, $q, 'q');

        if (!is_null($orderParams)) {
            foreach ($orderParams as $key => $order) {
                $q->addOrderBy('q.'.$order['columnName'], $order['orderDir']);
            }
        }
        if (!is_null($offset) && !is_null($limit)) {
            $q->setFirstResult($offset * $limit);
        }
        if (!is_null($limit)) {
            $q->setMaxResults($limit);
        }

        return $q->getQuery()->getResult();
    }

    public function findByCountResult($cityId, $search = array())
    {
        $q = $this->repository->createQueryBuilder('q');
        $q->select('COUNT(q)')
            ->where($q->expr()->eq('q.city', ':cityId'))
            ->setParameter('cityId', $cityId);

        $this->buildSearchQuery($search, $q, 'q');

        return $q->getQuery()->getSingleScalarResult();
    }

    private function buildSearchQuery(array $search, QueryBuilder &$q, $alias)
    {
        if (count($search) > 0) {
            foreach ($search as $key => $value) {
                if (!empty($value)) {
                    $q->andWhere('LOWER('.$alias.'.'.$key.') LIKE LOWER(:val_' . $key . ')');
                    $q->setParameter('val_' . $key, '%' . $value . '%');
                }
            }
        }
    }

    public function delete(StopArea $stopArea)
    {
        $this->em->remove($stopArea);
        $this->em->flush();
    }

    public function save(StopArea $stopArea)
    {
        $this->em->persist($stopArea);
        $this->em->flush();

        return $stopArea->getId();
    }

    public function saveAliases(StopArea $stopArea, $originalAliases)
    {
        foreach ($originalAliases as $alias) {
            if (false === $stopArea->getAlias()->contains($alias)) {
                $stopArea->removeAlias($alias);
                $this->em->remove($alias);
            }
        }

        $this->em->persist($stopArea);
        $this->em->flush();
    }

    /*
     * if $stopAreaId argument is given, then no stopArea with given id will be returned
     */
    public function findStopAreasLike($term, $stopAreaId = null)
    {
        $specials = array("-", " ", "'");
        $cleanTerm = str_replace($specials, "_", $term);

        $connection = $this->em->getConnection()->getWrappedConnection();

        $query="SELECT DISTINCT sa.short_name as name, c.name as city, sa.id as id
            FROM stop_area sa
            JOIN city c on c.id = sa.city_id
            LEFT JOIN alias a on a.stop_area_id = sa.id
            WHERE (UPPER(unaccent(sa.short_name)) LIKE UPPER(unaccent(:term))
            OR UPPER(unaccent(sa.long_name)) LIKE UPPER(unaccent(:term))
            OR UPPER(unaccent(a.name)) LIKE UPPER(unaccent(:term)))";
        if (!is_null($stopAreaId)) {
            $query .= " AND (sa.id != :stop_area_id)";
        }
        $query .= " ORDER BY sa.short_name, c.name";

        $stmt = $connection->prepare($query);
        $stmt->bindValue(':term', '%'.$cleanTerm.'%');
        if (!is_null($stopAreaId)) {
            $stmt->bindValue(':stop_area_id', $stopAreaId);
        }
        $stmt->execute();
        $shs = $stmt->fetchAll();

        $array = array();
        foreach ($shs as $sh) {
            $label = $sh["name"]." ".$sh["city"];
            $array[] = array("name"=>$label, "id"=>$sh["id"]);
        }

        return $array;
    }

    public function getStopsOrderedByCode($stopArea, $getPhantoms = false)
    {
        $queryString = "SELECT s
            FROM Tisseo\EndivBundle\Entity\Stop s
            JOIN s.stopDatasources sd ";

        if (! $getPhantoms) {
            $queryString .= "JOIN s.stopHistories sh ";
        }
        $queryString .= "WHERE s.stopArea = :sa
               ORDER BY sd.code";
        $query = $this->em->createQuery($queryString)->setParameter('sa', $stopArea);

        return $query->getResult();
    }

    public function getStopsOrderedByShortName($stopArea)
    {
        $query = $this->em->createQuery(
            "
               SELECT s
               FROM Tisseo\EndivBundle\Entity\Stop s
               JOIN s.stopDatasources sd
               JOIN s.stopHistories sh
               WHERE s.stopArea = :sa
               AND sh.startDate <= CURRENT_DATE()
               ORDER BY sh.shortName
        "
        )
            ->setParameter('sa', $stopArea);

        return $query->getResult();
    }

    //can get closed stops
    public function getCurrentStops($stopArea)
    {
        $sql = "
            SELECT s
            FROM Tisseo\EndivBundle\Entity\Stop s
            JOIN s.stopDatasources sd
            JOIN s.stopHistories sh
            WHERE s.stopArea = :sa
            AND sh.startDate <= CURRENT_DATE()
            ORDER BY sd.code";


        $query = $this->em->createQuery($sql)
            ->setParameter('sa', $stopArea);

        return $query->getResult();
    }

    public function getOpenedStops($stopArea)
    {
        $sql = "
            SELECT s
            FROM Tisseo\EndivBundle\Entity\Stop s
            JOIN s.stopDatasources sd
            JOIN s.stopHistories sh
            WHERE s.stopArea = :sa
            AND sh.startDate <= CURRENT_DATE()
            AND (sh.endDate IS NULL OR sh.endDate > CURRENT_DATE())
            ORDER BY sd.code";

        $query = $this->em->createQuery($sql)
            ->setParameter('sa', $stopArea);

        return $query->getResult();
    }

    /**
     * getMainStopCityName
     *
     * @param  entity $stopArea => stop_area to check
     * @return the name of the (first) city found, an empty string if none
     */
    public function getMainStopCityName($stopArea)
    {
        if (!$stopArea->getId()) {
            return "";
        }

        $query = $this->em->createQuery(
            "
               SELECT c.name
               FROM Tisseo\EndivBundle\Entity\City c
               WHERE c.mainStopArea = :sa
        "
        )
            ->setParameter('sa', $stopArea)
            ->setMaxResults(1);

        $result = $query->getResult();
        if ($result) {
            return $result[0]["name"];
        }
        return "";
    }


    public function getStopArea($id)
    {
        $query = $this->em->createQuery(
            "
               SELECT ar.id, ar.rank, wp.id as waypoint, c.id as city
               FROM Tisseo\EndivBundle\Entity\StopArea ar
               JOIN ar.waypoint wp
               JOIN ar.city c
               WHERE ar.route = :id
        "
        )->setParameter('id', $id);

        return $query->getResult();
    }

    //
    // VERIFIED FUNCTION
    //
    public function getUsedStops($stopAreaId)
    {
        $query = $this->em->createQuery(
            "
            SELECT DISTINCT s.id as stop1, s2.id as stop2
            FROM Tisseo\EndivBundle\Entity\Line l
            JOIN l.lineVersions lv
            JOIN lv.routes r
            JOIN r.routeStops rs
            JOIN rs.waypoint w
            LEFT JOIN w.stop s
            LEFT JOIN w.odtArea oa
            LEFT JOIN oa.odtStops os WITH (os.endDate IS NULL OR os.endDate >= CURRENT_DATE())
            LEFT JOIN os.stop s2
            WHERE (lv.endDate IS NULL OR lv.endDate >= CURRENT_DATE())
            AND s.stopArea = :sa OR s2.stopArea = :sa
            ORDER BY s.id
       "
        )->setParameter('sa', $stopAreaId);

        $stops = $query->getArrayResult();

        $result = array();
        foreach ($stops as $stop) {
            $result[] = ($stop['stop1'] ? $stop['stop1'] : $stop['stop2']);
        }

        return $result;
    }

    public function getLinesByStop($stopAreaId, $groupResultByStop = true)
    {
        $query = $this->em->createQuery(
            "
            SELECT DISTINCT s.id as stop, s2.id as stop2, l.id as line
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
            AND s.stopArea = :sa OR s2.stopArea = :sa
            ORDER BY s.id
        "
        )
            ->setParameter('sa', $stopAreaId);
        // There is a bug with getResult() and custom request SELECT s.id, l
        // leading to missing rows in the result array.
        $array = $query->getResult();

        $lines = array();
        foreach ($array as $item) {
            $lines[] = $item['line'];
        }

        $query = $this->em->createQuery(
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

        if ($groupResultByStop) {
            foreach ($array as $item) {
                if (!empty($item['stop'])) {
                    $result[$item['stop']][] = $lines[$item['line']];
                } else {
                    $result[$item['stop2']][] = $lines[$item['line']];
                }
            }
        } else {
            $result = $lines;
        }

        return $result;
    }

    public function getLinesByStopAreas($stopAreas)
    {
        $query = $this->em->createQuery(
            "
            SELECT DISTINCT sa.id as stop_area, sa2.id as stop_area_2, l.id as line
            FROM Tisseo\EndivBundle\Entity\Line l
            JOIN l.lineVersions lv
            JOIN lv.routes r
            JOIN r.routeStops rs
            JOIN rs.waypoint w
            LEFT JOIN w.stop s
            LEFT JOIN s.stopArea sa
            LEFT JOIN w.odtArea oa
            LEFT JOIN oa.odtStops os WITH (os.endDate IS NULL OR os.endDate >= CURRENT_DATE())
            LEFT JOIN os.stop s2
            LEFT JOIN s2.stopArea sa2
            WHERE lv.startDate <= CURRENT_DATE() AND (lv.endDate IS NULL OR lv.endDate >= CURRENT_DATE())
            AND (sa IN (:stop_areas) OR sa2 IN (:stop_areas))
        "
        )
            ->setParameter('stop_areas', $stopAreas);
        // There is a bug with getResult() and custom request SELECT s.id, l
        // leading to missing rows in the result array.
        $array = $query->getResult();

        $lines = array();
        foreach ($array as $item) {
            $lines[] = $item['line'];
        }

        $query = $this->em->createQuery(
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
            if (!empty($item['stop_area'])) {
                $result[$item['stop_area']][] = $lines[$item['line']];
            } else {
                $result[$item['stop_area_2']][] = $lines[$item['line']];
            }
        }

        return $result;
    }

    public function updateAliases($aliases, $stopArea)
    {
        $sync = false;
        foreach ($stopArea->getAliases() as $alias) {
            $existing = array_filter(
                $aliases,
                function ($object) use ($alias) {
                    return ($object['id'] == $alias->getId());
                }
            );

            if (empty($existing)) {
                $sync = true;
                $stopArea->removeAlias($alias);
            }
        }

        foreach ($aliases as $alias) {
            if (empty($alias['id']) && !empty($alias['name'])) {
                $sync = true;
                $newAlias = new Alias();
                $newAlias->setName($alias['name']);
                $newAlias->setStopArea($stopArea);
                $this->em->persist($newAlias);
            }
        }

        if ($sync) {
            $this->em->flush();
        }
    }

    public function getStopsJson($stopArea)
    {
        $connection = $this->em->getConnection()->getWrappedConnection();

        $query="SELECT DISTINCT s.id as id, sh.short_name as name, sd.code as code, ST_X(ST_Transform(sh.the_geom, 4326)) as x, ST_Y(ST_Transform(sh.the_geom, 4326)) as y
            FROM stop s
            JOIN stop_datasource sd on sd.stop_id = s.id
            JOIN stop_history sh on sh.stop_id = s.id
            WHERE s.stop_area_id = :sa_id
            AND sh.start_date <= CURRENT_DATE
            AND (sh.end_date IS NULL OR sh.end_date > CURRENT_DATE)";
        $stmt = $connection->prepare($query);
        $stmt->bindValue(':sa_id', $stopArea->getId());
        $stmt->execute();
        $result = $stmt->fetchAll();

        return $result;
    }
}
