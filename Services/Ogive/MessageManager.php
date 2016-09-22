<?php

namespace Tisseo\EndivBundle\Services\Ogive;

use Tisseo\ChaosComponent\Type\PtObjectType;
use Doctrine\Common\Persistence\ObjectManager;

class MessageManager extends OgiveManager
{
    private $repository = null;
    protected $objectManager = null;
    private $lineTypes = null;

    public function __construct(ObjectManager $objectManager)
    {
        parent::__construct($objectManager);

        $this->objectManager = $objectManager;
        $this->repository = $objectManager->getRepository('TisseoEndivBundle:Ogive\Channel');
        $this->lineTypes = [PtObjectType::PT_OBJECT_LINE, PtObjectType::PT_OBJECT_LINE_SECTION];
    }

    /**
     * Retreive Message by Object Type
     * @param PtObjectType $objectType
     * @return null
     */
    public function findByObjectType($objectType)
    {

        if (!$objectType) {
            return null;
        }

//        $rsm = new ResultSetMapping();
//        $rsm->addEntityResult('TisseoEndivBundle:Ogive\Message', 'message');
//        $rsm->addFieldResult('message', 'message.id', 'id');
//        $rsm->addFieldResult('message', 'message.title', 'title');
//        $rsm->addFieldResult('message', 'message.subtitle', 'subtitle');
//        $rsm->addFieldResult('message', 'message.subtitle', 'subtitle');
//        $rsm->addFieldResult('message', 'message.subtitle', 'subtitle');
//        $rsm->addFieldResult('message', 'message.subtitle', 'subtitle');

//        $SQL_Query = "SELECT channel.id, channel.name, channel.maxSize
//                      FROM TisseoEndivBundle:Ogive\Message message
//                      WHERE
//                          channel.name=:channelName
//                      OR
//                          channel.name=:channelName
//                      GROUP BY
//                           ???";

        $queryBuilder = $this->objectManager->createQueryBuilder();
        $queryBuilder
            ->select(['message', 'object'])
            ->from('TisseoEndivBundle:Ogive\Message', 'message')
            ->join('TisseoEndivBundle:Ogive\Object', 'object', 'WITH', $queryBuilder->expr()->eq('message.objectId', 'object.id'));

        $SQLResult = $queryBuilder
            ->getQuery()
            ->getResult();


//        $doctrineQuery = $this->objectManager->createQuery($SQL_Query, $rsm);
//        $SQLResult = $doctrineQuery
//            ->setParameter('channelName', $objectType)
//            ->getResult();

        return $SQLResult;


    }

}
