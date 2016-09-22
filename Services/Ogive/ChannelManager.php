<?php
/**
 * Created by PhpStorm.
 * User: cedric
 * Date: 22/09/16
 * Time: 10:40
 */

namespace Tisseo\EndivBundle\Services\Ogive;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Query\ResultSetMapping;
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
     * @param null $channelName
     * @return null| Array
     */
    public function findByChannelName($channelName = null)
    {
        if (is_null($channelName)) {
            return null;
        }

        $rsm = new ResultSetMapping();
        $rsm->addEntityResult('TisseoEndivBundle:Ogive\Channel', 'channel');
        $rsm->addFieldResult('channel', 'channel.id', 'id');
        $rsm->addFieldResult('channel', 'channel.name', 'name');
        $rsm->addFieldResult('channel', 'channel.maxSize', 'max_size');

        $SQL_Query = "SELECT channel.id, channel.name, channel.maxSize 
                      FROM TisseoEndivBundle:Ogive\Channel channel
                      WHERE channel.name=:channelName";

        $doctrineQuery = $this->objectManager->createQuery($SQL_Query, $rsm);
        $SQLResult = $doctrineQuery
            ->setParameter('channelName', $channelName)
            ->getResult();

        return $SQLResult;
    }

}