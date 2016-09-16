<?php

namespace Tisseo\EndivBundle\Services;

use Doctrine\Common\Persistence\ObjectManager;
use Tisseo\EndivBundle\Entity\Datasource;
use Tisseo\EndivBundle\Entity\ObjectDatasource;

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

    public function find($datasourceId)
    {
        return empty($datasourceId) ? null : $this->repository->find($datasourceId);
    }

    /**
     * Filling an object datasource with specified content
     *
     * @param ObjectDatasource $object
     * @param string $name
     * @param string $code
     * @return $source
     */
    public function fill(ObjectDatasource $object, $name, $code)
    {
        $datasource = $this->repository->findOneBy(array('name' => $name));

        if (empty($datasource)) {
            throw new \Exception(sprintf('Datasource %s not found', $name));
        }

        $objectSrc = $object->linkNewDatasource();
        $objectSrc->setDatasource($datasource);
        $objectSrc->setCode($code);
    }

    public function fillDatasource($objectSrc, $name, $code)
    {
        $datasource = $this->repository->findOneBy(array('name' => $name));

        if (empty($datasource)) {
            throw new \Exception(sprintf('Datasource %s not found', $name));
        }

        $objectSrc->setDatasource($datasource);
        $objectSrc->setCode($code);
    }

    public function save(Datasource $Datasource)
    {
        $this->om->persist($Datasource);
        $this->om->flush();
    }
}
