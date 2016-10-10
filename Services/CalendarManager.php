<?php

namespace Tisseo\EndivBundle\Services;

use Doctrine\ORM\QueryBuilder;

class CalendarManager extends AbstractManager
{
    public function advancedFindBy(array $array, $orderParams=null, $limit=null, $offset=null)
    {
        $query = $this->getRepository()->createQueryBuilder('q');
        $this->buildCriteria($array, $query);

        if (!is_null($orderParams)) {
            foreach ($orderParams as $order) {
                $query->addOrderBy('q.'.$order['columnName'], $order['orderDir']);
            }
        }
        if (false === is_null($offset)) {
            $query->setFirstResult($offset);
        }
        if (false === is_null($limit)) {
            $query->setMaxResults($limit);
        }

        return $query->getQuery()->getResult();
    }

    public function findByCountResult(array $array)
    {
        $query = $this->getRepository()->createQueryBuilder('q')->select('COUNT(q)');
        $this->buildCriteria($array, $query);

        return $query->getQuery()->getSingleScalarResult();
    }

    private function buildCriteria(array $params, QueryBuilder &$query)
    {
        $alias = $query->getRootAliases()[0];

        if (count($params) > 0) {
            foreach ($params as $key => $value) {
                if (!empty($value)) {
                    if ($key === 'name') {
                        $query->andWhere("UPPER(UNACCENT(".$alias.".".$key.")) LIKE UPPER(UNACCENT('%".$value."%'))");
                    } else {
                        $query->andWhere(($alias.'.'.$key.' = :val_'.$key));
                        $query->setParameter('val_'.$key, $value);
                    }
                }
            }
        }
    }

    /**
     * Find calendars by name with optionnaly passed type and lineVersionId
     *
     * @param  string $term
     * @param  array  $calendarTypes
     * @param  int    $lineVersionId
     * @param  int    $limit
     * @param  int    $offset
     * @return array
     */
    public function findCalendarsLike($term, $calendarTypes = array(), $lineVersionId = null, $offset = 0, $limit = 0)
    {
        $query = $this->createLikeQueryBuilder(array('name'), $term, $offset, $limit);

        $query->select('o.name, o.id');

        if (count($calendarTypes) > 0) {
            $query->andWhere('o.calendarType IN (:type)');
            $query->setParameter('type', $calendarTypes);
        }

        if ($lineVersionId !== null) {
            $query->andWhere('o.lineVersion = :lvid')->setParameter('lvid', $lineVersionId);
        }

        return $query->getQuery()->getScalarResult();
    }

    public function getCalendarBitmask($calendarId, \Datetime $startDate, \Datetime $endDate)
    {
        $connection = $this->getObjectManager()->getConnection()->getWrappedConnection();
        $stmt = $connection->prepare("select public.getcalendarbitmask(:calendarId::int, :startDate::date, :endDate::date)");
        $stmt->bindValue(':calendarId', $calendarId, \PDO::PARAM_INT);
        $stmt->bindValue(':startDate', $startDate->format('Y-m-d'), \PDO::PARAM_STR);
        $stmt->bindValue(':endDate', $endDate->format('Y-m-d'), \PDO::PARAM_STR);

        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $result["getcalendarbitmask"];
    }

    public function getCalendarsIntersectionBitmask($calendar1Id, $calendar2Id, \Datetime $startDate, \Datetime $endDate)
    {
        $connection = $this->getObjectManager()->getConnection()->getWrappedConnection();
        $stmt = $connection->prepare("select public.getbitmaskbeetweencalendars(:calendar1Id::int, :calendar2Id::int, :startDate::date, :endDate::date)");
        $stmt->bindValue(':calendar1Id', $calendar1Id, \PDO::PARAM_INT);
        $stmt->bindValue(':calendar2Id', $calendar2Id, \PDO::PARAM_INT);
        $stmt->bindValue(':startDate', $startDate->format('Y-m-d'), \PDO::PARAM_STR);
        $stmt->bindValue(':endDate', $endDate->format('Y-m-d'), \PDO::PARAM_STR);

        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $result["getbitmaskbeetweencalendars"];
    }
}
