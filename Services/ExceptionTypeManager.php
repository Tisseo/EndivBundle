<?php

namespace Tisseo\EndivBundle\Services;

use Doctrine\Common\Persistence\ObjectManager;
use Tisseo\EndivBundle\Entity\ExceptionType;

class ExceptionTypeManager extends SortManager
{
    private $om = null;
    private $repository = null;

    public function __construct(ObjectManager $om)
    {   
        $this->om = $om;
        $this->repository = $om->getRepository('TisseoEndivBundle:ExceptionType');
    }   

    public function findAll()
    {   
        return ($this->repository->findAll());
    }   

    public function find($ExceptionTypeId)
    {   
        return empty($ExceptionTypeId) ? null : $this->repository->find($ExceptionTypeId);
    }

    public function save(ExceptionType $ExceptionType)
    {
        $this->om->persist($ExceptionType);
        $this->om->flush();
    }
}
