<?php

namespace Tisseo\EndivBundle\Services;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Tisseo\EndivBundle\Entity\Calendar;

class CalendarManager
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
        return ($this->repository->findAll());
    }

    public function findBy(array $array)
    {
        return ($this->repository->findBy($array));
    }

    public function advancedFindBy(array $array, $orderParams=null, $limit=null, $offset=null)
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
                        $q->andWhere("UPPER(UNACCENT(".$alias.".".$key.")) LIKE UPPER(UNACCENT('%".$value."%'))");
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

    /**
     * @param string $term
     * @param mixed $calendarType
     * @param int $limit
     * @param int $lineVersionId
     *
     * Find calendars by name with optionnaly passed type and lineVersionId
     */
    public function findCalendarsLike($term, $calendarType = array(), $limit = 0, $lineVersionId = null)
    {
        $query = $this->repository->createQueryBuilder('c')
                ->select('c.name, c.id')
                ->where('UPPER(unaccent(c.name)) LIKE UPPER(unaccent(:term))')
                ->setParameter('term', '%'.$term.'%');

        if (count($calendarType) > 0) {
            $query->andWhere('c.calendarType IN (:type)');
            $query->setParameter('type', $calendarType);
        }

        if ($lineVersionId !== null) {
            $query->andWhere('c.lineVersion = :lvid')->setParameter('lvid', $lineVersionId);
        }

        if ($limit > 0) {
            $query->setMaxResults($limit);
        }

        return $query->getQuery()->getScalarResult();
    }

    public function getCalendarBitmask($calendarId, \Datetime $startDate, \Datetime $endDate)
    {
        $connection = $this->em->getConnection()->getWrappedConnection();
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
        $connection = $this->em->getConnection()->getWrappedConnection();
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
