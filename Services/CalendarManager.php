<?php

namespace Tisseo\EndivBundle\Services;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\EntityManager;

use Tisseo\EndivBundle\Entity\Calendar;


class CalendarManager extends SortManager
{
    private $om = null;
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

    public function findbyType($type)
    {
		$query = $this->em->createQuery("
			SELECT c FROM Tisseo\EndivBundle\Entity\Calendar c
			WHERE c.calendarType=:type
			AND c.id NOT IN (
				SELECT DISTINCT IDENTITY(t.periodCalendar)
				FROM Tisseo\EndivBundle\Entity\Trip t
				WHERE  t.periodCalendar IS NOT NULL
			)
			ORDER BY c.name
		")
			->setParameter('type', $type);;
		$calendars = $query->getResult();
		
		$result = array();
		$connection = $this->em->getConnection()->getWrappedConnection();
		foreach($calendars as $calendar) {
			$stmt = $connection->prepare("select count(*) from calendar_element where calendar_id = :calendarId::int");
			$stmt->bindValue(':calendarId', $calendar->getId(), \PDO::PARAM_INT);
			$stmt->execute();
			$nb = $stmt->fetchColumn();
			
			 $result[] = array("calendar" =>  $calendar, "nb_elements" => $nb);
		}
		
		return $result;		
    }
	
    public function find($CalendarId)
    {   
        return empty($CalendarId) ? null : $this->repository->find($CalendarId);
    }

    public function save(Calendar $Calendar)
    {
		$this->em->persist($Calendar);
        $this->em->flush();
    }
	
	public function findCalendarsLike( $term, $limit = 10 )
	{
	 
		$qb = $this->repository->createQueryBuilder('c');
		$qb ->select('c.name, c.id')
		->where('UPPER(c.name) LIKE UPPER(:term)')
		->setParameter('term', '%'.$term.'%')
		->setMaxResults($limit);
	 
		$arrayAss= $qb->getQuery()
		->getArrayResult();
	 
		// Transformer le tableau associatif en un tableau standard
		$array = array();
		foreach($arrayAss as $data)
		{
			$array[] = array("name"=>$data['name'], "id"=>$data['id']);
		}

		return $array;
	}	
	
	
	public function getCalendarsBitmask( $CalendarId, $startDate, $endDate)
	{
		$connection = $this->em->getConnection()->getWrappedConnection();
		$stmt = $connection->prepare("select public.getcalendarbitmask(:calendarId::int, :startDate::date, :endDate::date)");
		$stmt->bindValue(':calendarId', $CalendarId, \PDO::PARAM_INT);
		$stmt->bindValue(':startDate', $startDate);
		$stmt->bindValue(':endDate', $endDate);
		
		$stmt->execute();
		$result = $stmt->fetch(\PDO::FETCH_ASSOC);
		
		return $result["getcalendarbitmask"];
	}	
	
	
	public function delete($CalendarId)
	{
		$calendar = $this->find($CalendarId);
		if($calendar == null) return false;
		
		$this->em->remove($calendar);
        $this->em->flush();
		
		return true;
	}
}
