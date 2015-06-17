<?php

namespace Tisseo\EndivBundle\Services;

use Doctrine\Common\Persistence\ObjectManager;
use Tisseo\EndivBundle\Entity\Datasource;

class DatasourceManager extends SortManager
{
    private $om = null;
    private $repository = null;

    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
        $this->repository = $om->getRepository('TisseoEndivBundle:Datasource');
    }

    public function findAll()
    {
        return ($this->repository->findAll());
    }

    public function find($DatasourceId)
    {
        return empty($DatasourceId) ? null : $this->repository->find($DatasourceId);
    }

    public function save(Datasource $Datasource)
    {
        $this->om->persist($Datasource);
        $this->om->flush();
    }
}
