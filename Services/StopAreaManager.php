<?php

namespace Tisseo\EndivBundle\Services;

use Doctrine\ORM\EntityManager;
use Tisseo\EndivBundle\Entity\StopArea;
use Tisseo\EndivBundle\Entity\Transfer;

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

    public function findStopAreasLike($term)
    {
        $specials = array("-", " ", "'");
        $cleanTerm = str_replace($specials, "_", $term);

        $connection = $this->em->getConnection()->getWrappedConnection();
        $stmt = $connection->prepare("
            SELECT DISTINCT sa.short_name as name, c.name as city, sa.id as id
            FROM stop_area sa
            JOIN city c on c.id = sa.city_id
            LEFT JOIN alias a on a.stop_area_id = sa.id
            WHERE UPPER(unaccent(sa.short_name)) LIKE UPPER(unaccent(:term))
            OR UPPER(unaccent(sa.long_name)) LIKE UPPER(unaccent(:term))
            OR UPPER(unaccent(a.name)) LIKE UPPER(unaccent(:term))
            ORDER BY sa.short_name, c.name
        ");
        $stmt->bindValue(':term', '%'.$cleanTerm.'%');
        $stmt->execute();
        $shs = $stmt->fetchAll();

        $array = array();
        foreach($shs as $sh) {
            $label = $sh["name"]." ".$sh["city"];
            $array[] = array("name"=>$label, "id"=>$sh["id"]);
        }

        return $array;
    }

    public function getStopsOrderedByCode($stopArea) {
        $query = $this->em->createQuery("
               SELECT s
               FROM Tisseo\EndivBundle\Entity\Stop s
               JOIN s.stopDatasources sd
               WHERE s.stopArea = :sa
               ORDER BY sd.code
        ")
        ->setParameter('sa', $stopArea);

        return $query->getResult();
    }

    //can get closed stops
    public function getCurrentStops($stopArea) {
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

    /**
     * getMainStopCityName
     * @param entity $stopArea => stop_area to check
     * @return the name of the (first) city found, an empty string if none
     */
    public function getMainStopCityName($stopArea) {
        if( !$stopArea->getId() ) return "";

        $query = $this->em->createQuery("
               SELECT c.name
               FROM Tisseo\EndivBundle\Entity\City c
               WHERE c.mainStopArea = :sa
        ")
        ->setParameter('sa', $stopArea)
        ->setMaxResults(1);

        $result = $query->getResult();
        if( $result ) return $result[0]["name"];
        return "";
    }


    public function getStopArea($id) {
        $query = $this->em->createQuery("
               SELECT ar.id, ar.rank, wp.id as waypoint, c.id as city
               FROM Tisseo\EndivBundle\Entity\StopArea ar
               JOIN ar.waypoint wp
               JOIN ar.city c
               WHERE ar.route = :id
        ")->setParameter('id', $id);

        return $query->getResult();
    }

    //
    // VERIFIED FUNCTION
    //

    public function getLineVersions($stopAreaId)
    {
        $query = $this->em->createQuery("
           SELECT s.id, lv
           FROM Tisseo\EndivBundle\Entity\LineVersion lv
           JOIN lv.line l
           JOIN lv.routes r
           JOIN r.routeStops rs
           JOIN rs.waypoint w
           JOIN w.stop s
           WHERE lv.startDate <= CURRENT_DATE() AND (lv.endDate IS NULL OR lv.endDate >= CURRENT_DATE())
           AND s.stopArea = :sa
        ")
        ->setParameter('sa', $stopAreaId);

        $array = $query->getResult();
        $result = array();

        foreach ($array as $item)
            $result[$item['id']][] = $item[0];

        return $result;
    }
}
