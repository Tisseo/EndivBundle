<?php

namespace Tisseo\EndivBundle\Services;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Tisseo\EndivBundle\Entity\Calendar;


class CalendarManager extends SortManager
{
    private $om = null;
    private $em = null;
    private $repository = null;

    public function __construct(ObjectManager $om, EntityManager $em)
    {   
        $this->om = $om;
		$this->em = $em;
        $this->repository = $om->getRepository('TisseoEndivBundle:Calendar');
    }   

    public function findAll()
    {   
        return ($this->repository->findAll());
    }   

    public function findbyType($type)
    {
        $query = $this->repository->createQueryBuilder('c')
            ->where('c.calendarType = :type')
            ->setParameter('type', $type)
            ->getQuery();

        return $query->getResult();
    }
	
    public function find($CalendarId)
    {   
        return empty($CalendarId) ? null : $this->repository->find($CalendarId);
    }

    public function save(Calendar $Calendar)
    {
		foreach ($Calendar->getCalendarElements() as $CalendarElement) {
			$connection = $this->em->getConnection()->getWrappedConnection();
			$stmt = $connection->prepare('SELECT insertcalendarelement(:calendarId, :startDate, :endDate, :interval, :operator, :includedCalendarId);');
			$stmt->bindParam(':calendarId', $Calendar->getId(), \PDO::PARAM_INT);
			//$stmt->bindParam(':startDate', $CalendarElement->getStartDate());
			$stmt->bindValue(':startDate', date_format($CalendarElement->getStartDate(), 'Y-m-d'), \PDO::PARAM_STR);
			$stmt->bindValue(':endDate', date_format($CalendarElement->getEndDate(), 'Y-m-d'), \PDO::PARAM_STR);
			//$stmt->bindParam(':endDate', $CalendarElement->getEndDate());
			$value = ($CalendarElement->getInterval() ? null : $CalendarElement->getInterval());
			$stmt->bindParam(':interval', $value, \PDO::PARAM_INT);
			$stmt->bindParam(':operator', $CalendarElement->getOperator());			
			$value = (!$CalendarElement->getIncludedCalendar() ? null: $CalendarElement->getIncludedCalendar()->getId());
			
			$stmt->bindParam(':includedCalendarId', $value, \PDO::PARAM_INT);
			$stmt->execute();
		}
		
		$this->om->persist($Calendar);
        $this->om->flush();
    }
}
