<?php

namespace Tisseo\EndivBundle\Entity\Ogive;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use ReflectionClass;

class OgiveEntity
{
    /**
     * Merge
     *
     * Merge two entities of the same class using a mapper or all the properties
     * of the merged object. Doesn't work with relations (have to be managed by em)
     *
     * @param OgiveEntity $entity
     * @param array $mapper
     * @param boolean $withId
     * @throws \Exception
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
            $reflect = new ReflectionClass($entity);
            foreach ($reflect->getProperties() as $property) {
                if (!$withId && $property->getName() === 'id') {
                    continue;
                }

                $setter = 'set' . ucfirst($property->getName());
                $getter = 'get' . ucfirst($property->getName());

                if ($entity->$getter() instanceof Collection) {
                    continue;
                }

                $this->$setter($entity->$getter());
            }
        }
    }

    public function cloneCollection(Collection $collection)
    {
        $newCollection = new ArrayCollection();

        foreach ($collection as $subEntity) {
            $newSubEntity = clone $subEntity;
            $reflect = new ReflectionClass($this);
            $setter = 'set' . ucfirst($reflect->getShortName());
            $newSubEntity->$setter($this);
            $newCollection->add($newSubEntity);
        }

        return $newCollection;
    }
}
