<?php

namespace Tisseo\EndivBundle\Services;

use Doctrine\Common\Persistence\ObjectManager;
use Tisseo\EndivBundle\Entity\Log;

class LogManager extends AbstractManager
{
    /**
     * Count log entries
     *
     * @return array
     */
    public function count()
    {
        $query = $this->getRepository()->createQueryBuilder('l')
            ->select('count(l)');

        return $query->getQuery()->getSingleScalarResult();
    }
}
