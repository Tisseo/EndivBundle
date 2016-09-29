<?php

namespace Tisseo\EndivBundle\Services;

use Doctrine\Common\Persistence\ObjectManager;
use Tisseo\EndivBundle\Entity\LineGroupGis;

class LineGroupGisManager extends SortManager
{
    /**
     * @var ObjectManager $om
     */
    private $om = null;

    /**
 * @var \Doctrine\ORM\EntityRepository $repository
*/
    private $repository = null;

    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
        $this->repository = $om->getRepository('TisseoEndivBundle:LineGroupGis');
    }

    public function findAll()
    {
        return ($this->repository->findAll());
    }

    public function find($lineGroupGisId)
    {
        return empty($lineGroupGisId) ? null : $this->repository->find($lineGroupGisId);
    }

    /**
     * save
     *
     * @param  LineGroupGis $lineGroupGis
     * @return array(boolean, string message, LineGroupGis)
     *
     * Persist and save a LineGroupGis into database.
     */
    public function save(LineGroupGis $lineGroupGis)
    {
        $lineGroupGisContents = clone $lineGroupGis->getLineGroupGisContents();

        if ($lineGroupGis->getId() === null) {
            $lineGroupGis->clearLineGroupGisContents();
            $this->om->persist($lineGroupGis);
        }

        foreach ($lineGroupGisContents as $lineGroupGisContent) {
            $lineGroupGisContent->setLineGroupGis($lineGroupGis);
            $this->om->persist($lineGroupGisContent);
        }

        $this->om->persist($lineGroupGis);
        $this->om->flush();
    }

    /**
     * @param LineGroupGis $lineGroupGis
     * @return array(boolean, string message)
     *
     * Remove LineGroupGis into database
     */
    public function remove(LineGroupGis $lineGroupGis)
    {
        $this->om->remove($lineGroupGis);
        $this->om->flush();
    }

    public function getCsvExport()
    {
        $query = $this->om->createQuery(
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
            JOIN l.lineGroupGisContents lggc
            JOIN lggc.lineGroupGis lgg
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
