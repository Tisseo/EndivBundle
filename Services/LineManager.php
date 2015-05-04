<?php

namespace Tisseo\EndivBundle\Services;

use Doctrine\Common\Persistence\ObjectManager;
use Tisseo\EndivBundle\Entity\Line;

class LineManager extends SortManager
{
    private $om = null;
    private $repository = null;


    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
        $this->repository = $om->getRepository('TisseoEndivBundle:Line');
    }


    public function findAll()
    {
        return ($this->repository->findAll());
    }

    public function find($lineId)
    {
        return empty($lineId) ? null : $this->repository->find($lineId);
    }


    public function findExistingNumber($number, $id)
    {
        $query = $this->repository->createQueryBuilder('l')
            ->where('l.number = :number AND l.id != :id')
            ->setParameter('number', $number)
            ->setParameter('id', $id)
            ->getQuery();

        return $query->getResult();
    }


    public function findAllLinesByPriority()
    {
        $query = $this->repository->createQueryBuilder('l')
            ->addOrderBy('l.priority', 'ASC')
            ->getQuery();

        return $this->sortLinesByNumber($query->getResult());
    }


    public function findAllLinesWithSchematic($splitByPhysicalMode = false) {
        $query = $this->repository->createQueryBuilder('l')
            ->leftJoin('l.schematics', 'sc')
            ->getQuery();

        $result = $this->sortLinesByNumber($query->getResult());

        if ($splitByPhysicalMode) {
            $query = $this->om->createQuery(
                "SELECT p.name FROM Tisseo\EndivBundle\Entity\PhysicalMode p"
            );
            $physicalModes = $query->getResult();

            $result = $this->splitByPhysicalMode($result, $physicalModes);
        }

        return $result;
    }


    public function save(Line $line)
    {
        if ($line->getPriority() == null)
            $line->definePriority();
        $this->om->persist($line);
        $this->om->flush();
    }
}
