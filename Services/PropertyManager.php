<?php

namespace Tisseo\EndivBundle\Services;

class PropertyManager extends AbstractManager
{
    public function findAllAsArray()
    {
        $properties = $this->getRepository()->findAll();
        $arrayProperties = array();

        foreach ($properties as $property) {
            $arrayProperties[$property->getId()] = $property;
        }

        return $arrayProperties;
    }
}
