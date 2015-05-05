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
        return ($this->repository->findAll());
    }

    public function find($LineGroupId)
    {
        return empty($LineGroupId) ? null : $this->repository->find($LineGroupId);
    }

    /**
     * save
     * @param LineGroup $LineGroup
     * @return array(boolean, string message, LineGroup)
     *
     * Persist and save a LineGroup into database.
     */
    public function save(LineGroup $LineGroup)
    {
        $this->om->persist($LineGroup);
        $this->om->flush();

        return array(true,'line_group_gis.persisted', $LineGroup);
    }

    /**
     * @param LineGroup $LineGroup
     * @return array(boolean, string message)
     *
     * Remove LineGroup into database
     */
    public function remove(LineGroup $LineGroup)
    {
        $this->om->remove($LineGroup);
        $this->om->flush();

        return array(true,'line_group_gis.removed');
    }

    /*
     * setChildLine
     * @param LineVersion $lineVersion
     * @return LineVersion or NULL
     *
     * return the child line
     */
    public function getChildLine(LineVersion $lineVersion) {
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

    /*
     * setChildLine
     * @param LineVersion $lineVersion
     * @param LineVersion $childLine
     * @return array(boolean, string)
     *
     * !!! call only for creation !!!
     * Persist and save child line into database.
     */
    public function setChildLine(LineVersion $lineVersion, $childLine = null) {
        if( !$childLine ) {
            return array(true,'line_group.nothing_to_save');
        }
         
        //create a new line group
        $lineGroupName = $lineVersion->getLine()->getNumber()."_".$childLine->getLine()->getNumber()."_".$lineVersion->getStartDate()->format("Ymd");
        $lineGroup = new LineGroup();
        $lineGroup->setName($lineGroupName);
        $this->om->persist($lineGroup);

        // create father line group content
        $fatherLineGroupContent = new LineGroupContent();
        $fatherLineGroupContent->setLineVersion($lineVersion);
        $fatherLineGroupContent->setIsParent(true);
        $fatherLineGroupContent->setLineGroup($lineGroup);
        $lineGroup->addLineGroupContent($fatherLineGroupContent);
        $lineVersion->addLineGroupContent($fatherLineGroupContent);
        $this->om->persist($fatherLineGroupContent);
        $this->om->persist($lineVersion);

        // create child line group content
        $childLineGroupContent = new LineGroupContent();
        $childLineGroupContent->setLineVersion($childLine);
        $childLineGroupContent->setIsParent(false);
        $childLineGroupContent->setLineGroup($lineGroup);
        $lineGroup->addLineGroupContent($childLineGroupContent);
        $childLine->addLineGroupContent($childLineGroupContent);
        $this->om->persist($childLineGroupContent);
        $this->om->persist($childLine);  

        $this->om->flush();
        return array(true,'line_group.persisted');
    }
}
