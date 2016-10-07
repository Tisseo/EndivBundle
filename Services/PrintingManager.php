<?php

namespace Tisseo\EndivBundle\Services;

class PrintingManager extends AbstractManager
{
    public function getCsvExport()
    {
        $query = $this->getObjectManager()->createQuery(
            "
            SELECT
                l.number AS line_number,
                lv.version AS line_version_version,
                p.quantity AS printing_quantity,
                p.date AS printing_date,
                p.comment AS printing_comment
            FROM Tisseo\EndivBundle\Entity\LineVersion lv
            JOIN lv.printings p
            JOIN lv.line l
            ORDER BY l.number, lv.version
        "
        );

        $content = $query->getArrayResult();

        $date = new \Datetime();
        $filename = 'pritings_'.$date->format('Y-m-d');

        return array($content, $filename);
    }
}
