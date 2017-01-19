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
    public function findNetworkPublications(array $channels = array(), array $objectTypes = array())
    {
        $queryBuilder = $this->objectManager->createQueryBuilder('m');
        $expr = $queryBuilder->expr();

        $queryBuilder
            ->select('m, o, c')
            ->from('TisseoEndivBundle:Ogive\Message', 'm')
            ->where($expr->lte('m.startDatetime', 'current_timestamp()'))
            ->andWhere($expr->gt('m.endDatetime', 'current_timestamp()'));

        // Filter by channels if requested
        if (count($channels) > 0) {
            $queryBuilder
                ->join('m.channels', 'c', 'with', $expr->in('c.name', ':names'))
                ->setParameter('names', $channels);
        } else {
            $queryBuilder
                ->join('m.channels', 'c');
        }

        // Filter by objects type if requested
        if (count($objectTypes) > 0) {
            $queryBuilder
                ->join('m.object', 'o', 'with', $expr->in('o.objectType', ':types'))
                ->setParameter('types', $objectTypes);
        } else {
            $queryBuilder
                ->join('m.object', 'o');
        }

        return $queryBuilder->getQuery()->getResult();
    }
}
