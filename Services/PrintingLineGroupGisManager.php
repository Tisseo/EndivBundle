<?php

namespace Tisseo\EndivBundle\Services;

use Doctrine\Common\Persistence\ObjectManager;
use Tisseo\EndivBundle\Entity\PrintingLineGroupGis;

class PrintingLineGroupGisManager
{
    private $om = null;
    private $repository = null;

    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
        $this->repository = $om->getRepository('TisseoEndivBundle:PrintingLineGroupGis');
    }

    public function findAll()
    {
        return $this->repository->findAll();
    }

    public function find($printingLineGroupGisId)
    {
        return empty($printingLineGroupGisId) ? null : $this->repository->find($printingLineGroupGisId);
    }

    public function save(PrintingLineGroupGis $printingLineGroupGis)
    {
        $this->om->persist($printingLineGroupGis);
        $this->om->flush();
    }

    public function getCsvExport()
    {
        $query = $this->om->createQuery("
            SELECT
                DISTINCT lgg.name AS line_group_gis_name,
                p.quantity AS printing_quantity,
                p.date AS printing_date,
                p.comment AS printing_comment
            FROM Tisseo\EndivBundle\Entity\LineGroupGis lgg
            JOIN lgg.printings p
            WHERE lgg.deprecated = FALSE
            ORDER BY lgg.name
        ");

        $content = $query->getArrayResult();

        $date = new \Datetime();
        $filename = 'printings_line_group_gis_'.$date->format('Y-m-d');

        return array($content, $filename);
    }
}
