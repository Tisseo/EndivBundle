<?php

namespace Tisseo\EndivBundle\Services;

use Doctrine\Common\Persistence\ObjectManager;
use Tisseo\EndivBundle\Entity\Depot;

class DepotManager extends SortManager
{
    private $om = null;
    private $repository = null;

    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
        $this->repository = $om->getRepository('TisseoEndivBundle:Depot');
    }

    public function findAll()
    {
        return ($this->repository->findAll());
    }

    public function find($depotId)
    {
        return empty($depotId) ? null : $this->repository->find($depotId);
    }

    public function save(Depot $depot)
    {
        $this->om->persist($depot);
        $this->om->flush();
    }

       /**
       * delete
       * @param Depot $depot
       *
       * Delete a Depot from the database.
       */
    public function delete(Depot $depot)
    {
        $this->om->remove($depot);
        $this->om->flush();
    }

}
