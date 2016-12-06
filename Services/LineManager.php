<?php

namespace Tisseo\EndivBundle\Services;

use Doctrine\Common\Persistence\ObjectManager;
use Tisseo\EndivBundle\Entity\Line;

class LineManager extends SortManager
{
    private $om = null;
    private $repository = null;


    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
        $this->repository = $om->getRepository('TisseoEndivBundle:Line');
    }


    public function findAll()
    {
        return ($this->repository->findAll());
    }

    public function find($lineId)
    {
        return empty($lineId) ? null : $this->repository->find($lineId);
    }

    public function findByDataSource($dataSourceId)
    {
        $query = $this->repository->createQueryBuilder('l')
            ->innerJoin('l.lineDatasources', 'lds')
            ->innerJoin('lds.datasource', 'ds')
            ->where('ds.id = :datasourceId')
        ->setParameter('datasourceId', $dataSourceId);

        return $this->sortLinesByNumber($query->getQuery()->getResult());
    }

    public function findByDataSourceSortByStatus($dataSourceId)
    {
        $query = $this->repository->createQueryBuilder('l')
            ->innerJoin('l.lineDatasources', 'lds')
            ->innerJoin('lds.datasource', 'ds')
            ->where('ds.id = :datasourceId')
        ->setParameter('datasourceId', $dataSourceId);

        return $this->sortLinesByStatus($query->getQuery()->getResult());
    }

    public function findExistingNumber($number, $id)
    {
        $query = $this->repository->createQueryBuilder('l')
            ->where('l.number = :number AND l.id != :id')
            ->setParameter('number', $number)
            ->setParameter('id', $id)
            ->getQuery();

        return $query->getResult();
    }

    public function findAllLinesByPriority()
    {
        $query = $this->repository->createQueryBuilder('l')
            ->addOrderBy('l.priority', 'ASC')
            ->getQuery();

        return $this->sortLinesByNumber($query->getResult());
    }

    public function findAllWithSchematics() {
        $query = $this->repository->createQueryBuilder('l')
            ->select('l, sc, lgc, lgg, p, lv, bgc, fgc')
            ->join('l.lineVersions', 'lv', 'with', 'lv.endDate is null or (lv.endDate + 1) > current_date()')
            ->join('l.physicalMode', 'p')
            ->leftJoin('l.schematics', 'sc')
            ->leftJoin('lv.fgColor', 'fgc')
            ->leftJoin('lv.bgColor', 'bgc')
            ->leftJoin('l.lineGroupGisContents', 'lgc')
            ->leftJoin('lgc.lineGroupGis', 'lgg')
            ->getQuery();
        $result = $this->sortLinesByNumber($query->getResult());

        $query = $this->repository->createQueryBuilder('pm')
            ->select('p.name')
            ->from('Tisseo\EndivBundle\Entity\PhysicalMode', 'p')
            ->getQuery();
        $result = $this->splitByPhysicalMode($result, $query->getResult());

        return $result;
    }

    public function save(Line $line)
    {
        $this->om->persist($line);
        $this->om->flush();
    }
}
