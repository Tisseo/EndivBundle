<?php
namespace Tisseo\EndivBundle\Services\Ogive;

use Tisseo\EndivBundle\Entity\Ogive\Event;
use Tisseo\EndivBundle\Entity\Ogive\EventStepStatus;

class EventManager extends OgiveManager
{
    /**
     * Find parent event if exists
     *
     * @param  $disruptionId
     * @return Event|null
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

    public function findEventList($archive = false) {
        $status = ($archive === true) ? Event::STATUS_CLOSED : Event::STATUS_OPEN;

        $queryBuilder = $this->objectManager->createQueryBuilder()
            ->select('event')
            ->from('Tisseo\EndivBundle\Entity\Ogive\Event', 'event')
            ->leftJoin('event.periods', 'p')
            ->leftJoin('event.eventSteps', 'es')
            ->leftJoin('es.statuses', 's')
            ->where('event.status = :status')
            ->setParameter('status', $status)
            ->addSelect('p, es', 's');

        if ($archive === true) {
            $queryBuilder
                ->leftJoin('event.objects', 'eo')->addSelect('eo');
        }

        return $queryBuilder->getQuery()->getResult();
    }


    /**
     * Manage event data and save it
     *
     * @param  Event   $event
     * @param  integer $previousStatus
     * @return Event
     */
    public function manage(Event $event, $previousStatus, $login, $message)
    {
        $createdEventSteps = array();
        $eventClosed = ($previousStatus == Event::STATUS_OPEN && $event->getStatus() != Event::STATUS_OPEN);

        foreach ($event->getEventSteps() as $eventStep) {
            $scenarioStepParentId = $eventStep->getScenarioStepParentId();
            $scenarioStepId = $eventStep->getScenarioStepId();

            if (isset($scenarioStepId)) {
                if (isset($scenarioStepParentId) && array_key_exists($scenarioStepParentId, $createdEventSteps)) {
                    $eventStep->setEventStepParent($createdEventSteps[$scenarioStepParentId]);
                }
            }

            // Manage event status: If status goes to rejected or closed change event steps status
            if ($eventClosed) {
                $eventStepStatus = $eventStep->getLastStatus();

                if ($eventStepStatus->getStatus() == EventStepStatus::STATUS_TODO) {
                    $eventStepStatus->setLogin($login);
                    $eventStepStatus->setdateTime(new \DateTime());
                    $eventStepStatus->setUserComment($message);
                    $eventStepStatus->setStatus(EventStepStatus::STATUS_REJECTED);

                    $eventStep->addStatus($eventStepStatus);
                }
            }

            // Save eventStep (edit or add if new)
            $this->objectManager->persist($eventStep);

            if (isset($scenarioStepId)) {
                $createdEventSteps[$scenarioStepId] = $eventStep;
            }
        }

        return $this->save($event);
    }
}
