<?php

namespace Tisseo\EndivBundle\Services;

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
