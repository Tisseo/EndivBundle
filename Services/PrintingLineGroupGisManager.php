<?php

namespace Tisseo\EndivBundle\Services;

class PrintingLineGroupGisManager extends AbstractManager implements CsvExportInterface
{
    public function getCsvExport()
    {
        $query = $this->getObjectManager()->createQuery(
            "
            SELECT
                DISTINCT lgg.name AS line_group_gis_name,
                p.quantity AS printing_quantity,
                p.date AS printing_date,
                p.comment AS printing_comment
            FROM Tisseo\EndivBundle\Entity\LineGroupGis lgg
            JOIN lgg.printings p
            ORDER BY lgg.name
        "
        );

        $content = $query->getArrayResult();

        $date = new \Datetime();
        $filename = 'printings_line_group_gis_'.$date->format('Y-m-d');

        return array($content, $filename);
    }
}
