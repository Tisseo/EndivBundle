<?php

namespace Tisseo\EndivBundle\Services;

use Doctrine\Common\Persistence\ObjectManager;
use Tisseo\EndivBundle\Entity\Log;

class LogManager extends SortManager
{
    private $om = null;
    private $repository = null;

    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
        $this->repository = $om->getRepository('TisseoEndivBundle:Log');
    }

    public function findAll()
    {
        return ($this->repository->findBy(array(), array('id' => 'desc')));
    }

    public function find($LogId)
    {
        return empty($LogId) ? null : $this->repository->find($LogId);
    }

    public function count()
    {
        return $this->repository
                ->createQueryBuilder('l')
                ->select('count(l)')
                ->getQuery()
                ->getSingleScalarResult();
    }

    public function findLogEntries($offset, $limit)
    {
        return $this->repository->findBy(
            array(),
            array('id' => 'desc'),
            $limit,
            $offset
        );
    }

    public function save(Log $Log)
    {
        $this->om->persist($Log);
        $this->om->flush();
    }
}
