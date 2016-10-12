<?php

namespace Tisseo\EndivBundle\Services;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;
use JMS\Serializer\Serializer;
use Tisseo\EndivBundle\Entity\Datasourced;

abstract class AbstractManager
{
    /**
     * @var ManagerRegistry
     */
    protected $managerRegistry;

    /**
     * @var string
     */
    protected $class;

    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     * @var array
     */
    protected $services;

    /**
     * Constructor
     *
     * @param ManagerRegistry $managerRegistry
     */
    public function __construct(
        ManagerRegistry $managerRegistry
    ) {
        $this->managerRegistry = $managerRegistry;
        $this->services = array();
    }

    /**
     * Create the managed entity
     */
    public function create()
    {
        $model = new $this->class();

        if ($model instanceof Datasourced) {
            $model->linkNewDatasource();
        }

        return $model;
    }

    /**
     * Set the target class
     *
     * @param string $class
     */
    public function setClass($class)
    {
        $this->class = $class;
    }

    /**
     * Add extra service
     *
     * @param $service
     */
    public function addService($name, $service)
    {
        if (!array_key_exists($name, $this->services)) {
            $this->services[$name] = $service;
        }
    }

    /**
     * Get a linked service
     *
     * @param  string $name
     * @return object
     */
    protected function getService($name)
    {
        if (array_key_exists($name, $this->services)) {
            return $this->services[$name];
        }

        throw new \Exception(sprintf('The service %s is not accessible from this manager', $name));
    }

    /**
     * Get the object manager
     *
     * @return \Doctrine\Common\Persistence\ObjectManager
     */
    protected function getObjectManager($className = null)
    {
        $className = $className === null ? $this->class : $className;

        return $this->managerRegistry->getManagerForClass($this->class);
    }

    /**
     * Get the repository
     *
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    public function getRepository($className = null)
    {
        $className = $className === null ? $this->class : $className;

        return $this->getObjectManager($className)->getRepository($className);
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
    protected function getSerializer()
    {
        if ($this->serializer instanceof Serializer) {
            return $this->serializer;
        }

        throw new \Exception("Serializer not instanciated in this manager");
    }

    /**
     * Check the current manager has a serializer or not
     *
     * @return boolean
     */
    protected function hasSerializer()
    {
        try {
            return $this->getSerializer() instanceof Serializer;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Find data with like function
     *
     * @param  string|array $property
     * @param  string       $term
     * @param  boolean      $specials
     * @param  integer      $identifier
     * @param  integer      $offset
     * @param  integer      $limit
     * @param  integer      $hydratationMode
     * @return mixed
     */
    public function findByLike(
        $properties,
        $term,
        $specials = false,
        $identifier = null,
        $offset = 0,
        $limit = 0,
        $normalized = false,
        $hydratationMode = Query::HYDRATE_OBJECT
    ) {
        if (!is_array($properties)) {
            $properties = array($properties);
        }

        $query = $this->createLikeQueryBuilder($properties, $term, $specials, $offset, $limit);

        if ($identifier !== null) {
            $query->andWhere('o.id != :identifier')->setParameter('identifier', $identifier);
        }

        $result = $query->getQuery()->getResult($hydratationMode);

        if ($normalized === true && $this->hasSerializer()) {
            return $this->normalize($result);
        }

        return $result;
    }

    /**
     * Encapsulated find
     */
    public function find($identifier)
    {
        if (empty($identifier)) {
            return null;
        }

        return $this->getRepository()->find($identifier);
    }

    /**
     * Encapsulated findAll
     */
    public function findAll()
    {
        return $this->getRepository()->findAll();
    }

    /**
     * Encapsulated findBy
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->getRepository()->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * Encapsulated findOneBy
     */
    public function findOneBy(array $criteria)
    {
        return $this->getRepository()->findOneBy($criteria);
    }

    /**
     * Create a query with a like statement in it
     *
     * @param  string  $property
     * @param  string  $term
     * @param  boolean $specials
     * @param  integer $offset
     * @param  integer $limit
     * @return Doctrine\ORM\QueryBuilder
     */
    protected function createLikeQueryBuilder(array $properties, $term, $specials = false, $offset = 0, $limit = 0)
    {
        if (count($properties) === 0) {
            throw new \Exception("You must add at least one property used in the find like");
        }

        $objectManager = $this->managerRegistry->getManagerForClass($this->class);

        // filter some characters from the sent term
        if ($specials === true) {
            $term = str_replace(array("-", " ", "'"), "_", $term);
        }

        $query = $this->getRepository()->createQueryBuilder('o');
        foreach ($properties as $property) {
            if (!$objectManager->getClassMetadata($this->class)->hasField($property)) {
                throw new \Exception("This property isn't mapped for this entity");
            }
            $query->orWhere(sprintf('unaccent(lower(o.%s)) like unaccent(:term)', $property));
        }
        $query->setParameter('term', '%' . strtolower($term) . '%');

        if ($offset > 0) {
            $query->setFirstResult($offset);
        }

        if ($limit > 0) {
            $query->setMaxResults($limit);
        }

        return $query;
    }

    /**
     * Check the entity is valid
     *
     * @param  $model
     * @throws Exception if model doesn't have the right class
     */
    protected function check($model)
    {
        if (get_class($model) !== $this->class) {
            throw new \Exception(sprintf("The entity %s can't be managed by this manager", get_class($model)));
        }
    }

    /**
     * Save an entity and check it belongs to the manager
     *
     * @param  $model
     * @param  boolean $flush
     * @return $model
     */
    public function save($model, $flush = true)
    {
        $this->check($model);

        $objectManager = $this->getObjectManager();
        $objectManager->persist($model);

        if ($flush) {
            $objectManager->flush();
        }

        $objectManager->refresh($model);

        return $model;
    }

    /**
     * Remove the model with specific identifier
     *
     * @param  $model
     * @param  boolean $flush
     * @throws Exception if model not found
     */
    public function remove($model, $flush = true)
    {
        if (!is_object($model)) {
            $model = $this->getRepository()->find($model);
        } else {
            $this->check($model);
        }

        if (empty($model)) {
            throw new \Exception("Entity not found");
        }

        $objectManager = $this->getObjectManager();

        $objectManager->remove($model);

        if ($flush) {
            $objectManager->flush();
        }
    }

    /**
     * Alias for remove function
     */
    public function delete($model)
    {
        $this->remove($model);
    }

    /**
     * Manage the deleted entities from a collection
     *
     * @param $model
     * @param string $accessor
     * @param array  $collection
     */
    public function updateCollection($model, $accessor, array $collection)
    {
        $objectManager = $this->getObjectManager();

        foreach ($model->$accessor() as $childEntity) {
            foreach ($collection as $key => $toDelete) {
                if ($toDelete->getId() === $childEntity->getId()) {
                    unset($collection[$key]);
                }
            }
        }

        foreach ($collection as $childEntity) {
            $objectManager->remove($childEntity);
        }

        $objectManager->flush();
    }

    /**
     * Transform data into stdclass objects
     *
     * @param  $data
     * @return array
     */
    public function normalize($data)
    {
        if (get_class($data) === $this->class) {
            $result = $this->doNormalize($data);
        } elseif (is_array($data) || $data instanceof \Traversable) {
            $result = array();
            foreach ($data as $model) {
                $result[] = $this->doNormalize($model);
            }
        } else {
            throw new \Exception("Can't normalize unrecognized data type");
        }

        return $result;
    }

    /**
     * basic normalize function
     *
     * @param  $model
     * @return array
     */
    protected function doNormalize($model)
    {
        return json_decode($this->getSerializer()->serialize($model, 'json'));
    }
}
