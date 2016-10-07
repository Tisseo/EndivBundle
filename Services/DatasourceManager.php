<?php

namespace Tisseo\EndivBundle\Services;

use Tisseo\EndivBundle\Entity\ObjectDatasource;

class DatasourceManager extends AbstractManager
{
    /**
     * Filling an object datasource with specified content
     *
     * @param  ObjectDatasource $object
     * @param  string           $name
     * @param  string           $code
     * @return $source
     */
    public function fill(ObjectDatasource $object, $name, $code)
    {
        $datasource = $this->findOneBy(array('name' => $name));

        if (empty($datasource)) {
            throw new \Exception(sprintf('Datasource %s not found', $name));
        }

        $objectSrc = $object->linkNewDatasource();
        $objectSrc->setDatasource($datasource);
        $objectSrc->setCode($code);
    }

    /**
     * Filling datasource to an object
     *
     * @param $objectSrc
     * @param string    $name
     * @param string    $code
     */
    public function fillDatasource($objectSrc, $name, $code)
    {
        $datasource = $this->findOneBy(array('name' => $name));

        if (empty($datasource)) {
            throw new \Exception(sprintf('Datasource %s not found', $name));
        }

        $objectSrc->setDatasource($datasource);
        $objectSrc->setCode($code);
    }
}
