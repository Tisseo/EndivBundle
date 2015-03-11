<?php

namespace Tisseo\EndivBundle\Services;

use Doctrine\Common\Persistence\ObjectManager;
use Tisseo\EndivBundle\Entity\ExceptionType;

class ExceptionTypeManager
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

    public function find($exceptionTypeId)
    {   
        return empty($exceptionTypeId) ? null : $this->repository->find($exceptionTypeId);
    }

    public function save(ExceptionType $exceptionType)
    {
        $this->om->persist($exceptionType);
        $this->om->flush();
    }
}
