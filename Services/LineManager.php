<?php

namespace Tisseo\EndivBundle\Services;

use Tisseo\EndivBundle\Utils\Sorting;

class LineManager extends AbstractManager
{
    /**
     * Find lines and their LineVersions using Datasources as filter
     * Only lines having current/future LineVersions will be fetch
     *
     * @param array $datasources
     * @param integer $sort
     */
    public function findImportable(array $datasources, $sort = null)
    {
        // ensure lower comparison
        $datasources = array_map(
            function ($datasource) {
                return strtolower($datasource);
            },
            $datasources
        );

        $query = $this->getRepository()->createQueryBuilder('l')
            ->orderBy('l.priority', 'ASC')
            ->join('l.lineDatasources', 'lds')
            ->join('lds.datasource', 'da')
            ->join('l.lineVersions', 'lv')
            ->join('lv.depot', 'd')
            ->join('lv.fgColor', 'fg')
            ->join('lv.bgColor', 'bg')
            ->leftJoin('l.lineStatuses', 'ls')
            ->addSelect('lv, fg, bg, ls, lds, d')
            ->where('lower(da.name) IN (:datasources)')
            ->andWhere('lv.endDate IS NULL OR lv.endDate > CURRENT_DATE()')
            ->setParameter('datasources', $datasources)
            ->getQuery();

        $result = $query->getResult();

        if ($sort === null) {
            return $result;
        }

        return Sorting::sortByMode($result, $sort);
    }

    /**
     * Find a line
     *
     * @param  integer $identifier
     * @return Tisseo\EndivBundle\Entity\Line
     */
    public function find($identifier)
    {
        $query = $this->getRepository()->createQueryBuilder('l')
            ->join('l.physicalMode', 'p')
            ->join('l.lineDatasources', 'ld')
            ->addSelect('p, ld')
            ->where('l.id = :identifier')
            ->setParameter('identifier', $identifier)
            ->getQuery();

        return $query->getOneOrNullResult();
    }

    /**
     * Find all lines sorted by number/priority
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
            ->addSelect('lv, fg, bg, p')
            ->getQuery();

        return Sorting::sortLinesByNumber($query->getResult());
    }

    /**
     * Find all lines sorted by number/priority
     * Join past LineVersions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function findAllWithPastVersions()
    {
        $query = $this->getRepository()->createQueryBuilder('l')
            ->join('l.physicalMode', 'p')
            ->join('l.lineVersions', 'lv')
            ->join('lv.fgColor', 'fg')
            ->join('lv.bgColor', 'bg')
            ->groupBy('l.id, p.id, lv.id, fg.id, bg.id')
            ->having(
                '
                (lv.startDate <= CURRENT_DATE() AND ((lv.endDate is null AND lv.plannedEndDate > CURRENT_DATE()) OR (lv.endDate) > CURRENT_DATE())) OR
                (lv.plannedEndDate < CURRENT_DATE()) OR
                lv is NULL'
            )
            ->orderBy('l.priority', 'ASC')
            ->addSelect('lv, fg, bg, p')
            ->addOrderBy('lv.version', 'DESC')
            ->getQuery();

        return Sorting::sortLinesByNumber($query->getResult());
    }

    /**
     * Find all lines with their schematics
     *
     * @param  boolean $mode
     * @return \Doctrine\Common\Collections\Collection
     */
    public function findAllWithSchematic($mode = false)
    {
        $query = $this->getRepository()->createQueryBuilder('l')
            ->join('l.lineVersions', 'lv')
            ->join('lv.fgColor', 'fg')
            ->join('lv.bgColor', 'bg')
            ->join('l.physicalMode', 'p')
            ->leftJoin('l.schematics', 'sc')
            ->leftJoin('l.lineGroupGisContents', 'lgc')
            ->leftJoin('lgc.lineGroupGis', 'lgg')
            ->orderBy('l.physicalMode')
            ->addSelect('lv, p, fg, bg, sc, lgc, lgg')
            ->getQuery();

        $result = Sorting::sortLinesByNumber($query->getResult());

        if ($mode === true) {
            $modes = $this->getRepository('TisseoEndivBundle:PhysicalMode')->createQueryBuilder('p')
                ->select('p.name')->getQuery()->getScalarResult();
            $modes = array_map('current', $modes);
            $modeNames = array();
            foreach ($modes as $mode) {
                $modeNames[$mode] = array();
            }

            $result = Sorting::splitByPhysicalMode($result, Sorting::SPLIT_LINE, $modeNames);
        }

        return $result;
    }
}
