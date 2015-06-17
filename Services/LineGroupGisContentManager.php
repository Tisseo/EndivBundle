<?php

namespace Tisseo\EndivBundle\Services;

use Doctrine\Common\Persistence\ObjectManager;
use Tisseo\EndivBundle\Entity\LineGroupGisContent;


class LineGroupGisContentManager extends SortManager
{
    /**
     * @var ObjectManager $om
     */
    private $om = null;

    /** @var \Doctrine\ORM\EntityRepository $repository */
    private $repository = null;

    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
        $this->repository = $om->getRepository('TisseoEndivBundle:LineGroupGisContent');
    }

    public function findAll()
    {
        return ($this->repository->findAll());
    }

    public function find($lineGroupGisContentId)
    {
        return empty($lineGroupGisContentId) ? null : $this->repository->find($lineGroupGisContentId);
    }

    /**
     * @param $lineId
     * @return array
     */
    public function findByLine($lineId)
    {
        return $this->repository->findBy(array(
           'line' => $lineId,
        ));
    }

    /**
     * save
     * @param LineGroupGisContent $lineGroupGisContent
     * @return array(boolean, string message, LineGroupGisContent)
     *
     * Persist and save a LineGroupGisContent into database.
     */
    public function save(LineGroupGisContent $lineGroupGisContent)
    {
        try {
            $this->om->persist($lineGroupGisContent);
            $this->om->flush();
        } catch(\Exception $e) {
            return array($lineGroupGisContent, 'line_group_gis_content.error_persist', $e->getMessage());
        }

        return array($lineGroupGisContent, 'line_group_gis_content.persisted', null);
    }
}
