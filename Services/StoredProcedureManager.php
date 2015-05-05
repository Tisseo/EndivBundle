<?php

namespace Tisseo\EndivBundle\Services;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\ResultSetMapping;

class StoredProcedureManager
{
    private $em = null;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Clean LineVersion
     * @param integer $lineVersionId
     *
     * Clean all timetable data related to this LineVersion
     */
    public function cleanLineVersion($lineVersionId)
    {
		$rsm = new ResultSetMapping();
        $query = $this->em->createNativeQuery('SELECT purge_fh_data(?)', $rsm);
        $query->setParameter(1, intval($lineVersionId));

        $result = true;
        try {
            $query->getResult();
        }
        catch (Exception $e)
        {
            $result = false;
        }

        return $result;
    }
}
