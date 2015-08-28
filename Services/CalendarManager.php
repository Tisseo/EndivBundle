<?php

namespace Tisseo\EndivBundle\Services;

use Doctrine\ORM\EntityManager;
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
        return ($this->repository->findAll());
    }

    public function findBy(array $array)
    {
        return ($this->repository->findBy($array));
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

    public function findCalendarsLike($term, $calendarType = null, $limit = 0)
    {
        $connection = $this->em->getConnection()->getWrappedConnection();
        $sql = "
            SELECT name, id
            FROM calendar
            WHERE UPPER(unaccent(name)) LIKE UPPER(unaccent('%".$term."%'))";
        if ($calendarType)
        {
            if (is_array($calendarType))
                $sql .= "and calendar_type in ('".implode("','",$calendarType)."')";
            else
                $sql .= "and calendar_type in ('".$calendarType."')";
        }
        if ($limit > 0)
            $sql .= " LIMIT ".number_format($limit);

        $stmt = $connection->prepare($sql);
        $stmt->execute();
        $calendars = $stmt->fetchAll();

        $result = array();
        foreach ($calendars as $calendar)
        {
            $result[] = array(
                "name" => $calendar["name"],
                "id" => $calendar["id"]
            );
        }

        return $result;
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
