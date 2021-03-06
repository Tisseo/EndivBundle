<?php

namespace Tisseo\EndivBundle\Services;

use Doctrine\Common\Persistence\ObjectManager;
use Tisseo\EndivBundle\Entity\Printing;

class PrintingManager
{
    private $om = null;
    private $repository = null;

    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
        $this->repository = $om->getRepository('TisseoEndivBundle:Printing');
    }

    public function findAll()
    {
        return $this->repository->findAll();
    }

    public function find($printingId)
    {
        return empty($printingId) ? null : $this->repository->find($printingId);
    }

    public function save(Printing $printing)
    {
        $this->om->persist($printing);
        $this->om->flush();
    }

    public function getCsvExport()
    {
        $query = $this->om->createQuery("
            SELECT
                l.number AS line_number,
                lv.version AS line_version_version,
                p.quantity AS printing_quantity,
                p.date AS printing_date,
                CASE WHEN p.format = 1 THEN 'ligne' ELSE 'arrêt' END AS format,
                p.comment AS printing_comment,
                pt.label as printing_type_label
            FROM Tisseo\EndivBundle\Entity\LineVersion lv
            JOIN lv.printings p
            JOIN lv.line l
            LEFT OUTER JOIN p.printingType pt
            ORDER BY l.number, lv.version
        ");

        $content = $query->getArrayResult();

        $date = new \Datetime();
        $filename = 'printings_'.$date->format('Y-m-d');

        return array($content, $filename);
    }
}
