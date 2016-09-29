<?php

namespace Tisseo\EndivBundle\Services\Ogive;

use Tisseo\ChaosComponent\Type\PtObjectType;
use Doctrine\Common\Persistence\ObjectManager;

class MessageManager extends OgiveManager
{
    private $channelRepository = null;
    protected $objectManager = null;
    private $lineTypes = null;

    public function __construct(ObjectManager $objectManager)
    {
        parent::__construct($objectManager);

        $this->objectManager = $objectManager;
        $this->channelRepository = $objectManager->getRepository('TisseoEndivBundle:Ogive\Channel');
        $this->lineTypes = [PtObjectType::PT_OBJECT_LINE, PtObjectType::PT_OBJECT_LINE_SECTION];
    }

    /**
     * Retrieve Message by Object Type
     *
     * @param  PtObjectType $objectType
     * @return null
     */
    public function findMessagesByObjectType($objectType, $messageId = null, $filters = [])
    {
        if (!$objectType) {
            return null;
        }

        $query = $this->buildQuery(
            array(
            'filters' => $filters,
            'messageId' => $messageId
            )
        );

        $queryResults = $query->getResult();
        return $queryResults;
    }


    /**
     * Use to generate Custome DQL Query
     *
     * @param  $queryParameters
     * @return mixed
     */
    private function buildQuery($queryParameters)
    {
        $queryBuilder = $this->objectManager->createQueryBuilder();
        $expr = $queryBuilder->expr();
        $queryParams = [];

        $queryBuilder
            ->select('message')
            ->from('TisseoEndivBundle:Ogive\Message', 'message')
            ->join('TisseoEndivBundle:Ogive\Object', 'object', 'WITH', 'message.object = object');

        if (!empty($queryParameters['messageId'])) {
            $queryParams ['messageId'] = $queryParameters['messageId'];
            $queryBuilder
                ->where($expr->eq('message.id', ':messageId'));
        }

        if (!empty($queryParameters['objectType'])) {
            if (in_array($queryParameters['objectType'], $this->lineTypes)) {
                $queryParams ['objectType'] = $this->lineTypes;
            } else {
                $queryParams ['objectType'] = $queryParameters['objectType'];
            }
            $queryBuilder
                ->andWhere($expr->in('object.objectType', ':objectType'));
        }

        if (sizeof($queryParameters['filters']) > 0) {
            $channelFilter = $queryParameters['filters'];
            $channel = $this->getChannelByItsName($channelFilter);
            $queryParams ['channel'] = $channel;
            $queryBuilder
                ->andWhere(':channel MEMBER OF message.channels');
        }

        return $queryBuilder
            ->getQuery()
            ->setParameters($queryParams);
    }

    /**
     * Get a Channel by its Name
     *
     * @param  $name
     * @return null
     */
    private function getChannelByItsName($name)
    {
        if ($name) {
            $channels = $this->channelRepository->findByName($name);
            if (empty($channels)) {
                return null;
            }
            return $channels[0];
        }
        return null;
    }
}
