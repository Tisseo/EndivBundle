<?php

namespace Tisseo\EndivBundle\Entity;

abstract class ObjectDatasource
{
    public function linkNewDatasource()
    {
        $reflexion = new \ReflectionClass($this);
        $objectClass = $reflexion->getShortName();
        $datasourceClass = get_class($this).'Datasource';
        $setter = 'set'.$objectClass;

        $datasource = new $datasourceClass();
        $datasource->$setter($this);

        $add = 'add'.$objectClass.'Datasource';

        $this->$add($datasource);

        return $datasource;
    }
}
