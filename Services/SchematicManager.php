<?php

namespace Tisseo\EndivBundle\Services;

use Doctrine\Common\Persistence\ObjectManager;
use Tisseo\EndivBundle\Entity\Line;
use Tisseo\EndivBundle\Entity\LineVersion;
use Tisseo\EndivBundle\Entity\Schematic;
use Tisseo\TidBundle\Form\Type\LineSchemaType;


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

    /**
     * @param $lineId
     * @param int $limit
     * @return array Tisseo\EndivBundle\Entity\Schematic
     */
    public function findLineSchematics($lineId, $limit=5)
    {
        $finalResult = array();
        if (empty($lineId)) return $finalResult;

        $query = $this->repository->createQueryBuilder('sc')
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
    public function save(Schematic $schematic)
    {
        $this->om->persist($schematic);
        $this->om->flush();

        return array(true,'schematic.persisted');
    }
}
