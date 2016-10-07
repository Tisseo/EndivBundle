<?php

namespace Tisseo\EndivBundle\Services\Ogive;

class ChannelManager extends OgiveManager
{
    /**
     * Find a particular channel by its channel's name
     *
     * @param  null $channelName
     * @return null| Channel
     */
    public function findByChannelName($channelName = null)
    {
        if (is_null($channelName)) {
            return null;
        }

        $queryBuilder = $this->objectManager->createQueryBuilder()
            ->select('channel')
            ->from('TisseoEndivBundle:Ogive\Channel', 'channel')
            ->where('channel.name = :channelName')
            ->setParameter('channelName', $channelName);

        $results = $queryBuilder->getQuery()->getResult();

        if (count($results) == 1) {
            return $results[0];
        }

        return null;
    }
}
