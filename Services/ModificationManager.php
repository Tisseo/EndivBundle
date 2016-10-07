<?php

namespace Tisseo\EndivBundle\Services;

class ModificationManager extends AbstractManager
{
    public function findAllNotResolvedByLine($lineId)
    {
        return $this->getRepository()->createQueryBuilder('m')
            ->innerJoin('m.lineVersion', 'lv')
            ->innerJoin('lv.line', 'l')
            ->where('l.id = :id')
            ->andWhere('m.resolvedIn IS NULL')
            ->setParameter('id', $lineId);
    }
}
