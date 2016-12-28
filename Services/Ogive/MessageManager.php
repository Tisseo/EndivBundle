<?php

namespace Tisseo\EndivBundle\Services\Ogive;

use Tisseo\EndivBundle\Entity\Ogive\Object as OgiveObject;

class MessageManager extends OgiveManager
{
    /**
     * Retrieve Message linked to line objects
     * optional channels filter
     *
     * @param  array $channels
     * @return array
     */
    public function findNetworkPublications(array $channels = array())
    {
        $queryBuilder = $this->objectManager->createQueryBuilder('m');
        $expr = $queryBuilder->expr();

        $queryBuilder
            ->select('m')
            ->from('TisseoEndivBundle:Ogive\Message', 'm')
            ->join('m.object', 'o', 'with', $expr->eq('o.objectType', ':type'))
            ->where($expr->lte('m.startDatetime', 'current_timestamp()'))
            ->andWhere($expr->gt('m.endDatetime', 'current_timestamp()'))
            ->setParameter('type', OgiveObject::LINE);

        // If no channels provided, no filters added
        if (count($channels) > 0) {
            $queryBuilder
                ->join('m.channels', 'c', 'with', $expr->in('c.name', ':names'))
                ->setParameter('names', $channels);
        }

        return $queryBuilder->getQuery()->getResult();
    }
}
