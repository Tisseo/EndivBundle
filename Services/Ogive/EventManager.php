<?php
namespace Tisseo\EndivBundle\Services\Ogive;

use Tisseo\EndivBundle\Entity\Ogive\Event;

class EventManager extends OgiveManager
{
    /**
     * Find parent event if exists
     *
     * @param string $disruptionId
     */
    public function findParentEvent($disruptionId)
    {
        $query = $this->objectManager->createQuery(
            "
            SELECT event FROM Tisseo\EndivBundle\Entity\Ogive\Event event
            WHERE event.chaosDisruptionId = ?1
                AND event.id NOT IN
                (
                    SELECT IDENTITY(pEvent.eventParent) FROM Tisseo\EndivBundle\Entity\Ogive\Event pEvent
                    WHERE pEvent.eventParent IS NOT NULL
                )
            "
        )->setMaxResults(1);

        $query->setParameter(1, $disruptionId);
        $results = $query->getResult();

        return $results ? $results[0] : null;
    }

    /**
     * Find all closed events ie not open (closed or rejected)
     */
    public function findAllClosed()
    {
        $queryBuilder = $this->objectManager->createQueryBuilder()
            ->select('event')
            ->from('Tisseo\EndivBundle\Entity\Ogive\Event','event')
            ->where('event.status != :status')
            ->setParameter('status', Event::STATUS_OPEN);

        $results = $queryBuilder->getQuery()->getResult();

        return $results;
    }
}
