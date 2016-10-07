<?php

namespace Tisseo\EndivBundle\Services;

use Doctrine\ORM\Query\ResultSetMapping;

class StoredProcedureManager
{
    /**
     * Clean LineVersion
     *
     * @param integer $lineVersionId
     */
    public function cleanLineVersion($lineVersionId)
    {
        $rsm = new ResultSetMapping();
        $query = $this->getObjectManager()->createNativeQuery('SELECT purge_fh_data(?)', $rsm);
        $query->setParameter(1, intval($lineVersionId));
        $query->getResult();
    }
}
