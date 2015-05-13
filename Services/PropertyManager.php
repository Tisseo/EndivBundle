<?php

namespace Tisseo\EndivBundle\Services;

use Doctrine\Common\Persistence\ObjectManager;
use Tisseo\EndivBundle\Entity\Property;

class PropertyManager
{
    private $om = null;
    private $repository = null;

    public function __construct(ObjectManager $om)
    {   
        $this->om = $om;
        $this->repository = $om->getRepository('TisseoEndivBundle:Property');
    }   

    public function findAll()
    {   
        return ($this->repository->findAll());
    }

    public function findAllAsArray()
    {
        $properties = $this->repository->findAll();
        $arrayProperties = array();

        foreach($properties as $property)
        {
            $arrayProperties[$property->getId()] = $property;
        }

        return $arrayProperties;
    }
}
