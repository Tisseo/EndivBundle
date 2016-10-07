<?php

namespace Tisseo\EndivBundle\Services;

use Tisseo\EndivBundle\Utils\Sorting;

class LineManager extends AbstractManager
{
    /**
     * Find lines by datasource and sort them
     *
     * @param  integer $datasourceId
     * @param  integer $sort
     * @return Doctrine\Common\Collections\Collection;
     */
    public function findByDataSource($dataSourceId, $sort = null)
    {
        $query = $this->getRepository()->createQueryBuilder('l')
            ->innerJoin('l.lineDatasources', 'lds')
            ->innerJoin('lds.datasource', 'ds')
            ->where('ds.id = :datasourceId')
            ->setParameter('datasourceId', $dataSourceId);

        $result = $query->getQuery()->getResult();

        if ($sort === null) {
            return $result;
        }

        return Sorting::sortByMode($result, $sort);
    }

    public function findExistingNumber($number, $identifier)
    {
        $query = $this->getRepository()->createQueryBuilder('l')
            ->where('l.number = :number AND l.id != :id')
            ->setParameter('number', $number)
            ->setParameter('id', $identifier)
            ->getQuery();

        return $query->getResult();
    }

    /**
     * Find all lines sorted by priority
     * Add current or last LineVersion if it exists in order to get the colors and more
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function findAll()
    {
        $query = $this->getRepository()->createQueryBuilder('l')
            ->join('l.physicalMode', 'p')
            ->orderBy('l.priority', 'ASC')
            ->leftJoin('l.lineVersions', 'lv')
            ->leftJoin('l.lineVersions', 'lv2')
            ->leftJoin('lv.fgColor', 'fg')
            ->leftJoin('lv.bgColor', 'bg')
            ->groupBy('l.id, p.id, lv.id, fg.id, bg.id')
            ->having(
                '
                (lv.startDate <= CURRENT_DATE() AND ((lv.endDate is null AND lv.plannedEndDate > CURRENT_DATE()) OR (lv.endDate) > CURRENT_DATE())) OR
                (lv.plannedEndDate < CURRENT_DATE() AND lv.version = max(lv2.version)) OR
                lv is NULL'
            )
            ->addSelect('lv, fg, bg, p');

        return Sorting::sortLinesByNumber($query->getQuery()->getResult());
    }

    /**
     * Find all lines sorted by priority
     * Join past LineVersions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function findAllWithPastVersions()
    {
        $query = $this->getRepository()->createQueryBuilder('l')
            ->join('l.physicalMode', 'p')
            ->orderBy('l.priority', 'ASC')
            ->leftJoin('l.lineVersions', 'lv')
            ->leftJoin('lv.fgColor', 'fg')
            ->leftJoin('lv.bgColor', 'bg')
            ->groupBy('l.id, p.id, lv.id, fg.id, bg.id')
            ->having(
                '
                (lv.startDate <= CURRENT_DATE() AND ((lv.endDate is null AND lv.plannedEndDate > CURRENT_DATE()) OR (lv.endDate) > CURRENT_DATE())) OR
                (lv.plannedEndDate < CURRENT_DATE()) OR
                lv is NULL'
            )
            ->addSelect('lv, fg, bg, p')
            ->addOrderBy('lv.version', 'DESC');

        return Sorting::sortLinesByNumber($query->getQuery()->getResult());
    }

    public function findAllLinesWithSchematic($splitByPhysicalMode = false)
    {
        $query = $this->getRepository()->createQueryBuilder('l')
            ->leftJoin('l.schematics', 'sc')
            ->leftJoin('l.lineGroupGisContents', 'lgc')
            ->orderBy('l.physicalMode')
            ->getQuery();

        $result = Sorting::sortLinesByNumber($query->getResult());

        if ($splitByPhysicalMode) {
            $query = $this->getObjectManager()
                ->getRepository('Tisseo\EndivBundle\Entity\PhysicalMode')
                ->createQueryBuilder('p')
                ->select('p.name')
                ->getQuery();

            $physicalModes = $query->getResult();

            $result = Sorting::splitByPhysicalMode($result, $physicalModes);
        }

        return $result;
    }
}
