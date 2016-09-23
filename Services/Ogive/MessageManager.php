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
     * Retrieve Message by Object Type
     * @param PtObjectType $objectType
     * @return null
     */
    public function findMessagesByObjectType($objectType, $messageId = null)
    {

        if (!$objectType) {
            return null;
        }

        $queryBuilder = $this->objectManager->createQueryBuilder();
        $expr = $queryBuilder->expr();
        $queryBuilder
            ->select(['message'])
            ->from('TisseoEndivBundle:Ogive\Message', 'message')
            ->from('TisseoEndivBundle:Ogive\Object', 'object')
            ->join('message.object', 'WITH', 'object');

        if ($messageId) {
            $queryBuilder
                ->andWhere($expr->in('message.id', ":messageId"))
                ->setParameter('messageId', $messageId);
        }

        if (in_array($objectType, $this->lineTypes)) {
            $queryBuilder
                ->andWhere($expr->in('object.objectType', ":typeList"))
                ->setParameter('typeList', $this->lineTypes);
        } else {
            $queryBuilder
                ->andWhere($expr->eq('object.objectType', ":objectType"))
                ->setParameter('objectType', $objectType);
        }

        $SQLResult = $queryBuilder
            ->getQuery()
            ->getResult();

        return $SQLResult;
    }

}
