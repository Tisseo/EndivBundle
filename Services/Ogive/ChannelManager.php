<?php

namespace Tisseo\EndivBundle\Services\Ogive;

use Doctrine\Common\Persistence\ObjectManager;
use Tisseo\EndivBundle\Entity\Ogive\Channel;


class ChannelManager extends OgiveManager
{

    private $repository = null;
    protected $objectManager = null;

    public function __construct(ObjectManager $objectManager)
    {
        parent::__construct($objectManager);

        $this->objectManager = $objectManager;
        $this->repository = $objectManager->getRepository('TisseoEndivBundle:Ogive\Channel');
    }

    /**
     * Find a particular channel by its channel's name
     *
     * @param null $channelName
     * @return null| Array
     */
    public function findByChannelName($channelName = null)
    {
        if (is_null($channelName)) {
            return null;
        }

        $queryBuilder = $this->objectManager->createQueryBuilder();
        $expr = $queryBuilder->expr();
        $queryBuilder
            ->from('TisseoEndivBundle:Ogive\Channel', 'channel')
            ->where($expr->eq('channel.name', ":channelName"));

        $SQLResult = $queryBuilder
            ->setParameter('channelName', $channelName)
            ->getResult();

        return $SQLResult;
    }

}