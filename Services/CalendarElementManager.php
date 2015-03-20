<?php

namespace Tisseo\EndivBundle\Services;

use Doctrine\Common\Persistence\ObjectManager;
use Tisseo\EndivBundle\Entity\CalendarElement;

class CalendarElementManager extends SortManager
{
    private $om = null;
    private $repository = null;

    public function __construct(ObjectManager $om)
    {   
        $this->om = $om;
        $this->repository = $om->getRepository('TisseoEndivBundle:CalendarElement');
    }   

    public function findAll()
    {   
        return ($this->repository->findAll());
    }   

    public function findbyCalendar($Calendar)
    {
        if ($Calendar == null) return $finalResult;
	
        $query = $this->repository->createQueryBuilder('c')
            ->where('c.calendar = :calendar')
            ->setParameter('calendar', $Calendar)
            ->getQuery();

        return $query->getResult();
    }	
	
    public function find($CalendarElementId)
    {   
        return empty($CalendarElementId) ? null : $this->repository->find($CalendarElementId);
    }

    public function save(CalendarElement $CalendarElement)
    {
        $this->om->persist($CalendarElement);
        $this->om->flush();
    }
}
