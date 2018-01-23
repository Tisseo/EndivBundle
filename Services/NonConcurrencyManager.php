<?php

namespace Tisseo\EndivBundle\Services;

use Doctrine\Common\Persistence\ObjectManager;
use Tisseo\EndivBundle\Entity\NonConcurrency;

class NonConcurrencyManager extends SortManager
{
    /**
     * @var ObjectManager
     */
    private $om = null;

    /** @var \Doctrine\ORM\EntityRepository $repository */
    private $repository = null;

    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
        $this->repository = $om->getRepository('TisseoEndivBundle:NonConcurrency');
    }

    public function findAll()
    {
        return $this->repository->findAll();
    }

    /**
     * @param $priorityLineId, $nonPriorityLineId
     *
     * @return array
     */
    public function find($priorityLineId, $nonPriorityLineId)
    {
        return $this->repository->findOneBy(array(
            'priorityLine' => $priorityLineId,
            'nonPriorityLine' => $nonPriorityLineId,
        ));
    }

    public function findById($nonConcurrencyId)
    {
        if ($nonConcurrencyId === null) {
            return null;
        }
        $idArray = explode('/', $nonConcurrencyId);

        return $this->find($idArray[0], $idArray[1]);
    }

    /**
     * delete
     *
     * @param NonConcurrency $nonConcurrency
     *
     * Delete a NonConcurrency from the database.
     */
    public function delete(NonConcurrency $nonConcurrency)
    {
        $this->om->remove($nonConcurrency);
        $this->om->flush();
    }

    /**
     * save
     *
     * @param NonConcurrency $nonConcurrency
     *
     * Persist and save a NonConcurrency into database.
     */
    public function save(NonConcurrency $nonConcurrency)
    {
        $this->om->persist($nonConcurrency);
        $this->om->flush();
    }
}
