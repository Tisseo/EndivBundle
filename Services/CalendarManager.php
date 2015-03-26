<?php

namespace Tisseo\EndivBundle\Services;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Tisseo\EndivBundle\Entity\Calendar;


class CalendarManager extends SortManager
{
    private $om = null;
    private $repository = null;

    public function __construct(ObjectManager $om, CalendarElementManager $calendarElementManager)
    {   
        $this->om = $om;
        $this->calendarElementManager = $calendarElementManager;
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
			->andWhere('c.id not in (select distinct IDENTITY(t1.dayCalendar) from Tisseo\EndivBundle\Entity\Trip t1)')
			->andWhere('c.id not in (select distinct IDENTITY(t2.periodCalendar) from Tisseo\EndivBundle\Entity\Trip t2)')
            ->setParameter('type', $type)
            ->getQuery();
/*		
        $query = $this->repository->createQueryBuilder('c')
            ->where('c.calendarType = :type')
            ->setParameter('type', $type)
            ->getQuery();
*/
        return $query->getResult();
    }
	
    public function find($CalendarId)
    {   
        return empty($CalendarId) ? null : $this->repository->find($CalendarId);
    }

    public function save(Calendar $Calendar)
    {
/*	
		foreach ($Calendar->getCalendarElements() as $CalendarElement) {
			$this->calendarElementManager->save($CalendarElement);
		}
*/		
		$this->om->persist($Calendar);
        $this->om->flush();
    }
}
