<?php

namespace Tisseo\EndivBundle\Services\Ogive;

use Doctrine\Common\Persistence\ObjectManager;
use Tisseo\EndivBundle\Entity\Ogive\OgiveEntity;
use JMS\Serializer\Serializer;
use Traversable;
use Exception;

abstract class OgiveManager
{
    /**
     * @var ObjectManager
     */
    protected $objectManager = null;

    /**
     * @var entityClass
     */
    protected $entityClass = null;

    /**
     * @var Serializer
     */
    private $serializer = null;

    /**
     * Constructor
     *
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
        $this->entityClass = sprintf('TisseoEndivBundle:Ogive\%s', $this->resolveEntityClassName());
    }

    /**
     * Get the repository
     *
     * @return Repository
     */
    public function getRepository()
    {
        return $this->objectManager->getRepository($this->entityClass);
    }

    /**
     * Set the serializer
     *
     * @param Serializer $serializer
     */
    public function setSerializer(Serializer $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * Get serializer
     *
     * @return Serializer
     * @throws Exception if serializer not instanciated
     */
    public function getSerializer()
    {
        if ($this->serializer instanceof Serializer) {
            return $this->serializer;
        }

        throw new Exception("Serializer not instanciated in this manager");
    }

    /**
     * FindByLike
     *
     * @param string $property
     * @param string $term
     * @return mixed
     */
    public function findByLike($property, $term)
    {
        if (!$this->objectManager->getClassMetadata($this->entityClass)->hasField($property)) {
            throw new Exception("This property isn't mapped for this entity");
        }

        $data = $this->getRepository()->createQueryBuilder('o')
            ->where(sprintf('lower(o.%s) like :term', $property))
            ->setParameter('term', '%'.strtolower($term).'%')
            ->getQuery()
            ->getResult()
        ;

        return $this->normalize($data);
    }

    /**
     * Save the entity in database
     * 
     * @param OgiveEntity $entity
     * @return OgiveEntity
     */
    public function save(OgiveEntity $entity)
    {
        $this->objectManager->persist($entity);
        $this->objectManager->flush();
        $this->objectManager->refresh($entity);

        return $entity;
    }

    /**
     * Remove the entity with specific identifier
     *
     * @param integer $identifier
     * @throws Exception if entity not found
     */
    public function remove($identifier)
    {
        $entity = $this->getRepository()->find($identifier);

        if (empty($entity)) {
            throw new Exception("The entity {$identifier} was not found");
        }

        $this->objectManager->remove($entity);
        $this->objectManager->flush();
    }

    /**
     * Alias for remove function
     */
    public function delete($identifier)
    {
        $this->remove($identifier);
    }

    /**
     * Update collection
     * Manage the deleted entities from a collection
     *
     * @param OgiveEntity $entity
     * @param string $accessor
     * @param array $collection
     */
    public function updateCollection(OgiveEntity $entity, $accessor, array $collection)
    {
        foreach($entity->$accessor() as $childEntity) {
            foreach($collection as $key => $toDelete) {
                if ($toDelete->getId() === $childEntity->getId()) {
                    unset($collection[$key]);
                }
            }
        }

        foreach($collection as $childEntity) {
            $this->objectManager->remove($childEntity);
        }

        $this->objectManager->flush();
    }

    /**
     * Normalize
     *
     * @param mixed $data
     * @return array
     */
    public function normalize($data)
    {
        if ($data instanceof OgiveEntity) {
            $result = $this->doNormalize($data);
        } else if (is_array($data) || $data instanceof Traversable) {
            $result = array();
            foreach ($data as $entity) {
                $result[] = $this->doNormalize($entity);
            }
        } else {
            throw new Exception("Can't normalize unrecognized data type");
        }

        return $result;
    }

    /**
     * Do normalize
     *
     * @param OgiveEntity $entity
     * @return array
     */
    private function doNormalize(OgiveEntity $entity)
    {
        return json_decode($this->getSerializer()->serialize($entity, 'json'));
    }

    /**
     * Resolve the classname of the managed entity
     *
     * @return string
     */
    private function resolveEntityClassName()
    {
        return str_replace('Manager', '', (new \ReflectionClass($this))->getShortName());
    }
}
