<?php

namespace Tisseo\EndivBundle\Services;

use Doctrine\Common\Persistence\ObjectManager;
use Tisseo\EndivBundle\Entity\LineStatus;

class LineStatusManager
{
    private $om = null;
    private $repository = null;


    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
        $this->repository = $om->getRepository('TisseoEndivBundle:LineStatus');
    }


    public function findAll()
    {
        return ($this->repository->findAll());
    }

    /*
     * save
     * @param LineStatus $lineStatus
     *
     * Persist and save a LineStatus into database.
     */
    public function save(LineStatus $lineStatus)
    {
        $this->om->persist($lineStatus);
        $this->om->flush();
    }
}
