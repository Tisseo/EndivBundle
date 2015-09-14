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

    /*
     * Save
     * @param Schematic $schematic
     * @return array(boolean, string, Schematic)
     *
     * Persist and save a Schematic into database.
     */
    public function save(Schematic $schematic)
    {
        $this->om->persist($schematic);
        $this->om->flush();
    }

    public function remove($schematicId)
    {
        $schematic = $this->find($schematicId);
        $this->om->remove($schematic);
        $this->om->flush();
    }
}
