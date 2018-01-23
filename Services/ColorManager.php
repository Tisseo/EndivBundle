<?php

namespace Tisseo\EndivBundle\Services;

use Doctrine\Common\Persistence\ObjectManager;
use Tisseo\EndivBundle\Entity\Color;

class ColorManager extends SortManager
{
    private $om = null;
    private $repository = null;

    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
        $this->repository = $om->getRepository('TisseoEndivBundle:Color');
    }

    public function findAll()
    {
        return $this->repository->findAll();
    }

    public function find($ColorId)
    {
        return empty($ColorId) ? null : $this->repository->find($ColorId);
    }

    public function save(Color $Color)
    {
        $this->om->persist($Color);
        $this->om->flush();
    }
}
