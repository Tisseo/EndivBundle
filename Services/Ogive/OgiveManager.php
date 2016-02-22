<?php

namespace Tisseo\EndivBundle\Services\Ogive;

use Doctrine\Common\Persistence\ObjectManager;
use Tisseo\EndivBundle\Entity\Ogive\OgiveEntity;

abstract class OgiveManager
{
    protected $objectManager = null;
    protected $repository = null;

    /**
     * Constructor
     *
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
        $entityClassName = $this->resolveEntityClassName();
        $this->repository = $objectManager
            ->getRepository(sprintf('TisseoEndivBundle:Ogive\%s', $entityClassName));
    }

    /**
     * Get the repository
     *
     * @return Repository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * Save the entity in database
     * 
     * @param OgiveEntity $entity
     */
    public function save(OgiveEntity $entity)
    {
        $this->objectManager->persist($entity);
        $this->objectManager->flush();
    }

    /**
     * Remove the entity with specific identifier
     *
     * @param integer $identifier
     * @throws \Exception if entity not found
     */
    public function remove($identifier)
    {
        $entity = $this->repository->find($identifier);

        if (empty($entity)) {
            throw new \Exception(sprintf('The entity %s was not found', $identifier));
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
     * Resolve the classname of the managed entity
     *
     * @return string
     */
    private function resolveEntityClassName()
    {
        return str_replace('Manager', '', (new \ReflectionClass($this))->getShortName());
    }
}
