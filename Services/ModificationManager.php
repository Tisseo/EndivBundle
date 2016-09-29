<?php

namespace Tisseo\EndivBundle\Services;

use Doctrine\Common\Persistence\ObjectManager;

class ModificationManager
{
    private $om = null;
    private $repository = null;

    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
        $this->repository = $om->getRepository('TisseoEndivBundle:Modification');
    }

    public function findAll()
    {
        return ($this->repository->findAll());
    }

    public function find($lineId)
    {
        return empty($lineId) ? null : $this->repository->find($lineId);
    }

    public function findAllNotResolvedByLine($lineId)
    {
        $query = $this->repository->createQueryBuilder('m')
            ->innerJoin('m.lineVersion', 'lv')
            ->innerJoin('lv.line', 'l')
            ->where('l.id = :id')
            ->andWhere('m.resolvedIn IS NULL')
            ->setParameter('id', $lineId);

        return $query;
    }
}
