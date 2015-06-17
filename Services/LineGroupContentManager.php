<?php

namespace Tisseo\EndivBundle\Services;

use Doctrine\Common\Persistence\ObjectManager;
use Tisseo\EndivBundle\Entity\LineGroupContent;

class LineGroupContentManager extends SortManager
{
    private $om = null;
    private $repository = null;

    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
        $this->repository = $om->getRepository('TisseoEndivBundle:LineGroupContent');
    }

    public function findAll()
    {
        return ($this->repository->findAll());
    }

    public function find($lineGroupContentId)
    {
        return empty($lineGroupContentId) ? null : $this->repository->find($lineGroupContentId);
    }

    /*
     * save
     * @param LineGroupContent $LineGroupContent
     * @return array(boolean, string)
     *
     * Persist and save a LineGroupContent into database.
     */
    public function save(LineGroupContent $lineGroupContent)
    {
        $this->om->persist($lineGroupContent);
        $this->om->flush();

        return array(true,'line_group_content.persisted');
    }
}
