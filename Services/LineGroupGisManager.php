<?php

namespace Tisseo\EndivBundle\Services;

use Tisseo\EndivBundle\Entity\LineGroupGis;

class LineGroupGisManager extends AbstractManager
{
    /**
     * Find All with more data
     */
    public function findAll()
    {
        $query = $this->getRepository()->createQueryBuilder('lgg')
            ->leftJoin('lgg.printings', 'p')
            ->leftjoin('lgg.lineGroupGisContents', 'lggc')
            ->leftJoin('lggc.line', 'l')
            ->leftJoin('l.schematics', 's')
            ->leftJoin('l.lineVersions', 'lv')
            ->leftJoin('l.lineVersions', 'lv2')
            ->groupBy('lgg.id, lggc, p.id, l.id, s.id, lv.id')
            ->having('lv.version = max(lv2.version)')
            ->addSelect('p, l, lggc, s, lv')
            ->getQuery();

        return $query->getResult();
    }

    /**
     * Get specific data for an export
     *
     * @return array
     */
    public function getCsvExport()
    {
        $query = $this->getObjectManager()->createQuery(
            "
            SELECT
                DISTINCT lgg.name as line_group_gis_name,
                l.number as line_number,
                s1.date as first_schematic_date,
                s2.date as last_schematic_date,
                s1.comment as first_schematic_comment,
                d.longName as line_version_depot,
                s1.groupGis,
                s2.groupGis
            FROM Tisseo\EndivBundle\Entity\Line l
            JOIN l.lineVersions lv
            LEFT JOIN lv.depot d
            JOIN l.lineGroupGis lgg
            JOIN l.schematics s1 WITH s1.line = l AND s1.date = (
                SELECT max(subs1.date) FROM Tisseo\EndivBundle\Entity\Schematic subs1
                WHERE subs1.line = l AND subs1.deprecated != true AND subs1.groupGis = true
            )
            JOIN l.schematics s2 WITH s2.line = l AND s2.date = (
                SELECT min(subs2.date) FROM Tisseo\EndivBundle\Entity\Schematic subs2
                WHERE subs2.line = l AND subs2.deprecated != true AND subs2.groupGis = true
            )
        "
        );

        $content = $query->getArrayResult();
        $date = new \Datetime();
        $filename = 'line_group_gis_'.$date->format('Y-m-d');

        return array($content, $filename);
    }
}
