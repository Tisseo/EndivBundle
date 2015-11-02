<?php

namespace Tisseo\EndivBundle\Services;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Collections\ArrayCollection;
use Tisseo\EndivBundle\Entity\LineGroupGis;

class LineGroupGisManager extends SortManager
{
    /**
     * @var ObjectManager $om
     */
    private $om = null;

    /** @var \Doctrine\ORM\EntityRepository $repository */
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
     * @param LineGroupGis $lineGroupGis
     * @return array(boolean, string message, LineGroupGis)
     *
     * Persist and save a LineGroupGis into database.
     */
    public function save(LineGroupGis $lineGroupGis)
    {
        $lineGroupGisContents = clone $lineGroupGis->getLineGroupGisContents();

        if ($lineGroupGis->getId() === null)
        {
            $lineGroupGis->clearLineGroupGisContents();
            $this->om->persist($lineGroupGis);
        }

        foreach($lineGroupGisContents as $lineGroupGisContent)
        {
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
        $query = $this->om->createQuery("
            SELECT
                lgg.name as line_group_gis_name,
                l.number as line_number,
                max(s.date) as first_schematic,
                min(s.date) as last_schematic,
                lv.depot as line_version_depot
            FROM Tisseo\EndivBundle\Entity\Schematic s
            JOIN s.line l
            JOIN l.lineVersions lv
            JOIN l.lineGroupGisContents lggc
            JOIN lggc.lineGroupGis lgg
            WHERE s.groupGis = true
            AND s.deprecated = false
            GROUP BY lgg.name, l.number, lv.depot
        ");

        $content = $query->getArrayResult();
        $date = new \Datetime();
        $filename = 'line_group_gis_'.$date->format('Y-m-d');

        return array($content, $filename);
    }
}
