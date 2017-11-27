<?php

namespace Tisseo\EndivBundle\Services;

use Doctrine\Common\Persistence\ObjectManager;
use Tisseo\EndivBundle\Entity\LineGroup;
use Tisseo\EndivBundle\Entity\LineGroupContent;
use Tisseo\EndivBundle\Entity\LineVersion;

class LineGroupManager extends SortManager
{
    private $om = null;

    /** @var \Doctrine\ORM\EntityRepository $repository */
    private $repository = null;

    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
        $this->repository = $om->getRepository('TisseoEndivBundle:LineGroup');
    }

    public function findAll()
    {
        return $this->repository->findAll();
    }

    public function find($LineGroupId)
    {
        return empty($LineGroupId) ? null : $this->repository->find($LineGroupId);
    }

    /**
     * save
     *
     * @param LineGroup $LineGroup
     *
     * Persist and save a LineGroup into database.
     */
    public function save(LineGroup $LineGroup)
    {
        $this->om->persist($LineGroup);
        $this->om->flush();
    }

    /**
     * @param LineGroup $LineGroup
     *
     * Remove LineGroup into database
     */
    public function remove(LineGroup $LineGroup)
    {
        $this->om->remove($LineGroup);
        $this->om->flush();
    }

    /*
     * Get ChildLine
     * @param LineVersion $lineVersion
     * @return LineVersion or NULL
     *
     * return the child line
     */
    public function getChildLine(LineVersion $lineVersion)
    {
        $query = $this->om->createQuery("
            SELECT IDENTITY(lgc.lineVersion)
            FROM Tisseo\EndivBundle\Entity\LineGroupContent lgc
            JOIN lgc.lineGroup lg
            JOIN lg.lineGroupContent lgc2
            WHERE lgc2.lineVersion = :lv
            AND lgc2.isParent")
        ->setParameter('lv', $lineVersion);

        return $query->getOneOrNullResult();
    }
}
