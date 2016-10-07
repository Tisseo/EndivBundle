<?php

namespace Tisseo\EndivBundle\Services;

use Tisseo\EndivBundle\Entity\LineVersion;

class LineGroupManager extends AbstractManager
{
    /*
     * Get the child LineVersion of a LineVersion
     *
     * @param  LineVersion $lineVersion
     * @return LineVersion | null
     */
    public function getChildLine(LineVersion $lineVersion)
    {
        $query = $this->getObjectManager()->createQuery(
            "
            SELECT IDENTITY(lgc.lineVersion)
            FROM Tisseo\EndivBundle\Entity\LineGroupContent lgc
            JOIN lgc.lineGroup lg
            JOIN lg.lineGroupContent lgc2
            WHERE lgc2.lineVersion = :lv
            AND lgc2.isParent"
        )
            ->setParameter('lv', $lineVersion);

        return $query->getOneOrNullResult();
    }
}
