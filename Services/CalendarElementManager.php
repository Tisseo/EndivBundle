<?php

namespace Tisseo\EndivBundle\Services;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\ResultSetMapping;
use Tisseo\EndivBundle\Entity\CalendarElement;
use Tisseo\EndivBundle\Entity\Calendar;

class CalendarElementManager extends SortManager
{
    private $om = null;
    private $repository = null;

    public function __construct(EntityManager $em)
    {   
        $this->em = $em;
        $this->repository = $em->getRepository('TisseoEndivBundle:CalendarElement');
    }   

    public function findAll()
    {   
        return ($this->repository->findAll());
    }   

    public function findbyCalendar($Calendar)
    {
		if ($Calendar == null) return null;
	
		$rsm = new ResultSetMapping();
		$rsm->addEntityResult('TisseoEndivBundle:CalendarElement', 'ce');
		$rsm->addFieldResult('ce','id','id');
		$rsm->addFieldResult('ce','start_date','startDate');
		$rsm->addFieldResult('ce','end_date','endDate');
		$rsm->addFieldResult('ce','rank','rank');
		$rsm->addFieldResult('ce','operator','operator');
		$rsm->addFieldResult('ce','interval','interval');
		//$rsm->addFieldResult('ce','included_calendar_id','includedCalendar');
		//$rsm->addJoinedEntityResult('TisseoEndivBundle:Calendar', 'c', 'ce', 'calendar');
		$rsm->addMetaResult('ce', 'included_calendar_id', 'included_calendar_id');
		
		$sql = "SELECT ce.id, ce.start_date, ce.end_date, ce.rank, ce.operator, ce.interval, ce.included_calendar_id
		FROM calendar_element ce
		JOIN calendar c on c.id=ce.calendar_id
		where ce.calendar_id=:calendarId order by rank asc";
		
		$query = $this->em->createNativeQuery($sql,$rsm);
		$query->setParameter('calendarId', $Calendar);

		return $query->getResult();
/*	
        $query = $this->repository->createQueryBuilder('c')
            ->where('c.calendar = :calendar')
            ->setParameter('calendar', $Calendar)
			->orderBy('c.rank', 'ASC')
            ->getQuery();

        return $query->getResult();
*/			
    }	
	
    public function find($CalendarElementId)
    {   
        return empty($CalendarElementId) ? null : $this->repository->find($CalendarElementId);
    }

    public function saveFromCalendar(Calendar $Calendar)
    {

	foreach ($Calendar->getCalendarElements() as $CalendarElement) {
			$this->save($CalendarElement);
		}
		
    }
	
    public function delete($CalendarElementId)
    {
		$connection = $this->em->getConnection()->getWrappedConnection();
		$stmt = $connection->prepare("select public.deletecalendarelement(:calendarElementId::int)");
		$stmt->bindValue(':calendarElementId', $CalendarElementId, \PDO::PARAM_INT);
		$stmt->execute();
	}

    public function save($CalendarId, CalendarElement $CalendarElement)
    {
		$connection = $this->em->getConnection()->getWrappedConnection();
		$stmt = $connection->prepare("select public.insertcalendarelement(:calendarId::int, :startDate::date, :endDate::date, :interval::int, :operator, :includedCalendarId::int)");
		$stmt->bindValue(':calendarId', $CalendarId, \PDO::PARAM_INT);
		$startDate = (!$CalendarElement->getStartDate() ? null : date_format($CalendarElement->getStartDate(), 'Y-m-d') );
		$stmt->bindValue(':startDate', $startDate);
		$endDate = (!$CalendarElement->getEndDate() ? null : date_format($CalendarElement->getEndDate(), 'Y-m-d') );
		$stmt->bindValue(':endDate', $endDate);
		$interval = ($CalendarElement->getInterval() ? null : $CalendarElement->getInterval());
		$stmt->bindValue(':interval', $interval, \PDO::PARAM_INT);
		$stmt->bindValue(':operator', $CalendarElement->getOperator());
		$includedCalendarId = (!$CalendarElement->getIncludedCalendar() ? null: $CalendarElement->getIncludedCalendar()->getId());
		$stmt->bindValue(':includedCalendarId', $includedCalendarId, \PDO::PARAM_INT);
		
		$stmt->execute();
    }
}
