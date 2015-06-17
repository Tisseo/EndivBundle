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
     * @param int $limit, if 0, no result limitation
     * @param boolean $isFile default false
     * @return array Tisseo\EndivBundle\Entity\Schematic
     */
    public function findLineSchematics($lineId, $limit=5, $isFile = false)
    {
        $finalResult = array();
        if (empty($lineId)) return $finalResult;

        $qBuilder = $this->repository->createQueryBuilder('sc')
            ->where('sc.line = :lineId');


        if ($isFile == true) {
            $qBuilder->andWhere("sc.filePath != '' ");
        }

        if ($limit != 0) {
            $qBuilder->setMaxResults((int)$limit);
        }

        $qBuilder->setParameter('lineId', $lineId);
        $qBuilder->orderBy('sc.date', 'DESC');

        $query = $qBuilder->getQuery();

        try {
            $results = $query->getResult();
        } catch(\Exception $e) {
            return $finalResult;
        }

        return $results;
    }

    /*
     * Save
     * @param Schematic $schematic
     * @return array(boolean, string, Schematic)
     *
     * Persist and save a Schematic into database.
     */
    public function save(Schematic $schematic)
    {
        try {
            $this->om->persist($schematic);
            $this->om->flush();
        } catch(\Exception $e) {
            return array($schematic, 'line_schema.error_persist', $e->getMessage());
        }

        return array($schematic, 'line_schema.persisted', null);
    }
}
