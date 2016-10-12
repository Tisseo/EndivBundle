<?php

namespace Tisseo\EndivBundle\Services;

use Tisseo\EndivBundle\Entity\Calendar;
use Tisseo\EndivBundle\Entity\CalendarElement;

class CalendarElementManager extends AbstractManager
{
    /**
     * Remove a calendar element
     *
     * @param integer $calendarElementId
     */
    protected function remove($calendarElementId)
    {
        $connection = $this->getObjectManager()->getConnection()->getWrappedConnection();
        $stmt = $connection->prepare("select public.deletecalendarelement(:calendarElementId::int)");
        $stmt->bindValue(':calendarElementId', $calendarElementId, \PDO::PARAM_INT);
        $stmt->execute();
    }

    /**
     * Save a CalendarElement
     *
     * @param integer         $calendarId
     * @param CalendarElement $calendarElement
     */
    protected function save($calendarId, CalendarElement $calendarElement)
    {
        $connection = $this->getObjectManager()->getConnection()->getWrappedConnection();
        $stmt = $connection->prepare("select public.insertcalendarelement(:calendarId::int, :startDate::date, :endDate::date, :interval::int, :operator, :includedCalendarId::int)");

        $stmt
            ->bindValue(':calendarId', $calendarId, \PDO::PARAM_INT)
            ->bindValue(':startDate', ($calendarElement->getStartDate() ? date_format($calendarElement->getStartDate(), 'Y-m-d') : null))
            ->bindValue(':endDate', ($calendarElement->getEndDate() ? date_format($calendarElement->getEndDate(), 'Y-m-d') : null))
            ->bindValue(':interval', ($calendarElement->getInterval() === null ? 1 : $calendarElement->getInterval()), \PDO::PARAM_INT)
            ->bindValue(':operator', $calendarElement->getOperator())
            ->bindValue(':includedCalendarId', ($calendarElement->getIncludedCalendar() ? $calendarElement->getIncludedCalendar()->getId() : null), \PDO::PARAM_INT);

        $stmt->execute();
    }

    public function updateCalendarElements($calendarElements, Calendar $calendar)
    {
        foreach ($calendar->getCalendarElements('DESC') as $calendarElement) {
            $existing = array_filter(
                $calendarElements,
                function ($object) use ($calendarElement) {
                    return ($object['id'] == $calendarElement->getId());
                }
            );

            if (empty($existing)) {
                $this->remove($calendarElement->getId());
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

            if ($calendarElement->getIncludedCalendar() !== null) {
                $includedCalendar = $this->getObjectManager()->createQuery(
                    "
                    SELECT c FROM Tisseo\EndivBundle\Entity\Calendar c
                    WHERE c.id = :calendar
                "
                )
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
