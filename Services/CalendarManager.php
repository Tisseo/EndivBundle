<?php

namespace Tisseo\EndivBundle\Services;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Tisseo\EndivBundle\Entity\Calendar;

class CalendarManager extends SortManager
{
    private $em = null;
    private $repository = null;

    public function __construct(EntityManager $em, CalendarElementManager $calendarElementManager)
    {
        $this->em = $em;
        $this->calendarElementManager = $calendarElementManager;
        $this->repository = $em->getRepository('TisseoEndivBundle:Calendar');
    }

    public function findAll()
    {
        return $this->repository->findAll();
    }

    public function findBy(array $array)
    {
        return $this->repository->findBy($array);
    }

    public function advancedFindBy(array $array, $orderParams = null, $limit = null, $offset = null)
    {
        $q = $this->repository->createQueryBuilder('q');
        $this->buildCriteria($array, $q);

        if (!is_null($orderParams)) {
            foreach ($orderParams as $key => $order) {
                $q->addOrderBy('q.'.$order['columnName'], $order['orderDir']);
            }
        }
        if (false === is_null($offset)) {
            $q->setFirstResult($offset);
        }
        if (false === is_null($limit)) {
            $q->setMaxResults($limit);
        }

        return $q->getQuery()->getResult();
    }

    public function findByCountResult(array $array)
    {
        $q = $this->repository->createQueryBuilder('q')->select('COUNT(q)');
        $this->buildCriteria($array, $q);

        return $q->getQuery()->getSingleScalarResult();
    }

    private function buildCriteria(array $params, QueryBuilder &$q)
    {
        $alias = $q->getRootAliases()[0];

        if (count($params) > 0) {
            foreach ($params as $key => $value) {
                if (!empty($value)) {
                    if ($key === 'name') {
                        $q->andWhere('UPPER('.$alias.'.'.$key.") LIKE UPPER('%".$value."%')");
                    } else {
                        $q->andWhere(($alias.'.'.$key.' = :val_'.$key));
                        $q->setParameter('val_'.$key, $value);
                    }
                }
            }
        }
    }

    public function find($calendarId)
    {
        return empty($calendarId) ? null : $this->repository->find($calendarId);
    }

    public function save(Calendar $calendar)
    {
        $this->em->persist($calendar);
        $this->em->flush();
    }

    public function remove($calendarId)
    {
        $calendar = $this->find($calendarId);

        $this->em->remove($calendar);
        $this->em->flush();
    }

    //If $lineVersionId argument is given, then only calendars with the same lineVersionId (or null) will be returned
    public function findCalendarsLike($term, $calendarType = null, $limit = 0, $lineVersionId = null)
    {
        $connection = $this->em->getConnection()->getWrappedConnection();
        $sql = "
            SELECT name, id
            FROM calendar
            WHERE UPPER(unaccent(name)) LIKE UPPER(unaccent('%".$term."%'))";

        if ($calendarType) {
            if (is_array($calendarType)) {
                $sql .= " and calendar_type in ('".implode("','", $calendarType)."')";
            } else {
                $sql .= " and calendar_type in ('".$calendarType."')";
            }
        }

        if (!empty($lineVersionId)) {
            $sql .= ' and (line_version_id IS NULL OR line_version_id = :lv_id)';
        }

        if ($limit > 0) {
            $sql .= ' LIMIT '.number_format($limit);
        }

        $stmt = $connection->prepare($sql);

        if (!empty($lineVersionId)) {
            $stmt->bindValue(':lv_id', $lineVersionId);
        }

        $stmt->execute();
        $calendars = $stmt->fetchAll();

        $result = array();

        foreach ($calendars as $calendar) {
            $result[] = array(
                'name' => $calendar['name'],
                'id' => $calendar['id']
            );
        }

        return $result;
    }

    public function getCalendarBitmask($calendarId, \DatetimeInterface $startDate, \DatetimeInterface $endDate)
    {
        $connection = $this->em->getConnection()->getWrappedConnection();
        $stmt = $connection->prepare('select public.getcalendarbitmask(:calendarId::int, :startDate::date, :endDate::date)');
        $stmt->bindValue(':calendarId', $calendarId, \PDO::PARAM_INT);
        $stmt->bindValue(':startDate', $startDate->format('Y-m-d'), \PDO::PARAM_STR);
        $stmt->bindValue(':endDate', $endDate->format('Y-m-d'), \PDO::PARAM_STR);

        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $result['getcalendarbitmask'];
    }

    public function getCalendarsIntersectionBitmask($calendar1Id, $calendar2Id, \DatetimeInterface $startDate, \DatetimeInterface $endDate)
    {
        $connection = $this->em->getConnection()->getWrappedConnection();
        $stmt = $connection->prepare('select public.getbitmaskbeetweencalendars(:calendar1Id::int, :calendar2Id::int, :startDate::date, :endDate::date)');
        $stmt->bindValue(':calendar1Id', $calendar1Id, \PDO::PARAM_INT);
        $stmt->bindValue(':calendar2Id', $calendar2Id, \PDO::PARAM_INT);
        $stmt->bindValue(':startDate', $startDate->format('Y-m-d'), \PDO::PARAM_STR);
        $stmt->bindValue(':endDate', $endDate->format('Y-m-d'), \PDO::PARAM_STR);

        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $result['getbitmaskbeetweencalendars'];
    }
}
