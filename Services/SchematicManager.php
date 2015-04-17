<?php

namespace Tisseo\EndivBundle\Services;

use Doctrine\Common\Persistence\ObjectManager;
use Tisseo\EndivBundle\Entity\Line;


class SchematicManager extends SortManager
{
    private $om = null;

    /** @var \Doctrine\ORM\EntityRepository $repository */
    private $repository = null;

    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
        $this->repository = $om->getRepository('TisseoEndivBundle:Schematic');
    }

    public function findAll()
    {
        return ($this->repository->findAll());
    }

    public function find($schematicId)
    {
        return empty($schematicId) ? null : $this->repository->find($schematicId);
    }

    public function findLineSchematics($lineId, $limit=5)
    {
        $finalResult = null;
        if (empty($lineId)) return $finalResult;

        $query = $this->repository->createQueryBuilder('sc')
            //->leftJoin('Tisseo\EndivBundle\Entity\Line','line')
            ->where('sc.line = :lineId')
            ->orderBy('sc.date', 'DESC')
            ->setMaxResults((int) $limit)
            ->setParameter('lineId', $lineId)
            ->getQuery();

        try {
            $results = $query->getResult();
        } catch(\Exception $e) {
            return $finalResult;
        }

        return $results;
    }
    /*
     * save
     * @param Schematic $schematic
     * @return array(boolean, string)
     *
     * Persist and save a Schematic into database.
     */
    public function save(LineVersion $lineVersion)
    {
        $this->om->persist($lineVersion);
        $this->om->flush();

        return array(true,'schematic.persisted');
    }
}
