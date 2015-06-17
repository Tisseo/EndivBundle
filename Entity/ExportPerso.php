<?php

namespace Tisseo\EndivBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ExportPerso
 */
class ExportPerso
{
    /**
     * @var string
     */
    private $tableName;


    /**
     * Get tableName
     *
     * @return string
     */
    public function getTableName()
    {
        return $this->tableName;
    }
}
