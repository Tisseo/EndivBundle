<?php

namespace Tisseo\EndivBundle\Entity\Ogive;

class OgiveEntity
{
    /**
     * Merge
     *
     * Merge two entities of the same class using a mapper or all the properties
     * of the merged object.
     *
     * @param OgiveEntity $entity
     * @param array $mapper
     * @param boolean $withId
     */
    public function merge(OgiveEntity $entity, array $mapper = array(), $withId = true)
    {
        if (!is_a($this, get_class($entity))) {
            throw new \Exception('Cannot merge different entities.');
        }

        if (!empty($mapper)) {
            foreach ($mapper as $property) {
                if (property_exists($this, $property)) {
                    $setter = 'set' . ucfirst($property);
                    $getter = 'get' . ucfirst($property);

                    $this->$setter($entity->$getter());
                }
            }
        } else {
            $reflect = new \ReflectionClass($entity);
            foreach ($reflect->getProperties() as $property) {
                if (!$withId && $property->getName() === 'id') {
                    continue;
                }
                $setter = 'set' . ucfirst($property->getName());
                $getter = 'get' . ucfirst($property->getName());
                $this->$setter($entity->$getter());
            }
        }
    }
}
