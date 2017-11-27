<?php

namespace Tisseo\EndivBundle\Services;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\ResultSetMapping;
use JMS\Serializer\Serializer;
use Tisseo\EndivBundle\Entity\Calendar;
use Tisseo\EndivBundle\Entity\CalendarElement;

class CalendarElementManager extends SortManager
{
    private $em = null;
    private $repository = null;
    private $serializer = null;

    public function __construct(EntityManager $em, Serializer $serializer)
    {
        $this->em = $em;
        $this->repository = $em->getRepository('TisseoEndivBundle:CalendarElement');
        $this->serializer = $serializer;
    }

    public function findAll()
    {
        return $this->repository->findAll();
    }

    public function findbyCalendar($calendar)
    {
        if ($calendar == null) {
            return null;
        }

        $rsm = new ResultSetMapping();
        $rsm->addEntityResult('TisseoEndivBundle:CalendarElement', 'ce');
        $rsm->addFieldResult('ce', 'id', 'id');
        $rsm->addFieldResult('ce', 'start_date', 'startDate');
        $rsm->addFieldResult('ce', 'end_date', 'endDate');
        $rsm->addFieldResult('ce', 'rank', 'rank');
        $rsm->addFieldResult('ce', 'operator', 'operator');
        $rsm->addFieldResult('ce', 'interval', 'interval');
        $rsm->addMetaResult('ce', 'included_calendar_id', 'included_calendar_id');

        $sql = 'SELECT ce.id, ce.start_date, ce.end_date, ce.rank, ce.operator, ce.interval, ce.included_calendar_id
        FROM calendar_element ce
        JOIN calendar c on c.id=ce.calendar_id
        where ce.calendar_id=:calendarId order by rank asc';

        $query = $this->em->createNativeQuery($sql, $rsm);
        $query->setParameter('calendarId', $calendar);

        return $query->getResult();
    }

    public function find($calendarElementId)
    {
        return empty($calendarElementId) ? null : $this->repository->find($calendarElementId);
    }

    public function saveFromCalendar(Calendar $calendar)
    {
        foreach ($calendar->getCalendarElements() as $calendarElement) {
            $this->save($calendarElement);
        }
    }

    public function delete($calendarElementId)
    {
        $connection = $this->em->getConnection()->getWrappedConnection();
        $stmt = $connection->prepare('select public.deletecalendarelement(:calendarElementId::int)');
        $stmt->bindValue(':calendarElementId', $calendarElementId, \PDO::PARAM_INT);
        $stmt->execute();
    }

    public function save($calendarId, CalendarElement $calendarElement)
    {
        $connection = $this->em->getConnection()->getWrappedConnection();

        $stmt = $connection->prepare('select public.insertcalendarelement(:calendarId::int, :startDate::date, :endDate::date, :interval::int, :operator, :includedCalendarId::int)');

        $stmt->bindValue(':calendarId', $calendarId, \PDO::PARAM_INT);
        $stmt->bindValue(':startDate', (!$calendarElement->getStartDate() ? null : date_format($calendarElement->getStartDate(), 'Y-m-d')));
        $stmt->bindValue(':endDate', (!$calendarElement->getEndDate() ? null : date_format($calendarElement->getEndDate(), 'Y-m-d')));
        $stmt->bindValue(':interval', ($calendarElement->getInterval() === null ? 1 : $calendarElement->getInterval()), \PDO::PARAM_INT);
        $stmt->bindValue(':operator', $calendarElement->getOperator());
        $stmt->bindValue(':includedCalendarId', (!$calendarElement->getIncludedCalendar() ? null : $calendarElement->getIncludedCalendar()->getId()), \PDO::PARAM_INT);

        $stmt->execute();
    }

    public function updateCalendarElements($calendarElements, Calendar $calendar)
    {
        foreach ($calendar->getCalendarElements('DESC') as $calendarElement) {
            $existing = array_filter(
                $calendarElements,
                function ($object) use ($calendarElement) {
                    return $object['id'] == $calendarElement->getId();
                }
            );

            if (empty($existing)) {
                $this->delete($calendarElement->getId());
            }
        }

        $newCalendarElements = array_filter(
            $calendarElements,
            function ($object) {
                return empty($object['id']);
            }
        );

        foreach ($newCalendarElements as $calendarElement) {
            $calendarElement = $this->serializer->deserialize(json_encode($calendarElement), 'Tisseo\EndivBundle\Entity\CalendarElement', 'json');
            $calendarElement->setCalendar($calendar);

            if ($calendarElement->getIncludedCalendar() != null) {
                $includedCalendar = $this->em->createQuery("
                    SELECT c FROM Tisseo\EndivBundle\Entity\Calendar c
                    WHERE c.id = :calendar
                ")
                ->setParameter('calendar', $calendarElement->getIncludedCalendar()->getId())
                ->getOneOrNullResult();

                if ($includedCalendar === null) {
                    throw new \Exception("Can't create a new CalendarElement because provided includedCalendar with id: ".$calendarElement->getIncludedCalendar()->getId()." can't be found.");
                }
                $calendarElement->setIncludedCalendar($includedCalendar);
            }

            $this->save($calendar->getId(), $calendarElement);
        }
    }
}
