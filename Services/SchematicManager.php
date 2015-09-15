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
     * Find multiple by Id
     * @param array $schematicIds
     */
    public function findMultipleById(array $schematicIds)
    {
        return $this->repository->findById($schematicIds);
    }

    /*
     * Save
     * @param Schematic $schematic
     *
     * Saving a Schematic into database.
     */
    public function save(Schematic $schematic)
    {
        $this->om->persist($schematic);
        $this->om->flush();
    }

    /*
     * Remove
     * @param integer $schematicId
     *
     * Removing a Schematic from database.
     */
    public function remove($schematicId)
    {
        $schematic = $this->find($schematicId);
        $this->om->remove($schematic);
        $this->om->flush();
    }

    /**
     * Update group gis
     * @param array $schematicIds
     * @param boolean $groupGis
     *
     * Updating groupGis attribute for specified Schematics.
     */
    public function updateGroupGis(array $schematics, $groupGis)
    {
        $schematicsCollection = $this->findMultipleById($schematics);

        $sync = false;
        foreach ($schematicsCollection as $schematic)
        {
            if ($schematic->getGroupGis() !== $groupGis)
            {
                $schematic->setGroupGis($groupGis);
                $this->om->persist($schematic);
                $sync = true;
            }
        }

        if ($sync)
            $this->om->flush();
    }
}
