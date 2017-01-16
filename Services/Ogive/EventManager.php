<?php
namespace Tisseo\EndivBundle\Services\Ogive;

use Tisseo\EndivBundle\Entity\Ogive\Event;
use Tisseo\EndivBundle\Entity\Ogive\EventStepStatus;
use Tisseo\EndivBundle\Types\Ogive\MomentType;

class EventManager extends OgiveManager
{
    /**
     * Find parent event if exists
     *
     * @param $disruptionId
     * @return Event|null
     */
    public function findParentEvent($disruptionId)
    {
        $subQueryBuilder = $this->objectManager->createQueryBuilder()
            ->select('identity(e.eventParent)')
            ->from('Tisseo\EndivBundle\Entity\Ogive\Event', 'e')
            ->where('e.eventParent is not null')
            ->distinct();

        $queryBuilder = $this->objectManager->createQueryBuilder();
        $queryBuilder
            ->select('event')
            ->from('Tisseo\EndivBundle\Entity\Ogive\Event', 'event')
            ->where('event.chaosDisruptionId = :disruptionId')
            ->andWhere('event.status = :status')
            ->andWhere($queryBuilder->expr()->notIn('event.id', $subQueryBuilder->getDQL()))
            ->setParameters(array(
                'disruptionId' => $disruptionId,
                'status' => Event::STATUS_OPEN
            ))
            ->setMaxResults(1);

        return $queryBuilder->getQuery()->getOneOrNullResult();
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
            ->addSelect('p, es, s');

        if ($archive === true) {
            $queryBuilder
                ->leftJoin('event.objects', 'eo')->addSelect('eo');
        }

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * Check alerts and nbr of finished event steps.
     *
     * @param  array $events
     * @return array
     */
    public function checkEventSteps(array $events)
    {
        $eventsInfos = array();
        $now = new \Datetime();

        foreach ($events as $event) {
            $extrema = $event->getExtremaPeriodDates();
            $nbrFinished = 0;
            $alert = false;

            foreach ($event->getEventSteps() as $eventStep) {
                if ($eventStep->getLastStatus()->getStatus() != EventStepStatus::STATUS_TODO) {
                    $nbrFinished++;
                } elseif (!$alert) {
                    $moment = $eventStep->getMoment();

                    if ($moment == MomentType::BEFORE) {
                        $alert = true;
                    } elseif (!empty($extrema) && (
                        ($moment == MomentType::NOW && $extrema['min'] < $now) ||
                        ($moment == MomentType::AFTER && $extrema['max'] < $now)
                    )) {
                        $alert = true;
                    }
                }
            }

            $eventsInfos[$event->getId()] = array(
                'nbrFinished' => $nbrFinished,
                'alert' => $alert,
            );
        }

        return $eventsInfos;
    }

    /**
     * Update event data and save it
     * @param  Event $event
     * @return Event
     */
    public function update(Event $event)
    {
        $createdEventSteps = array();
        foreach ($event->getEventSteps() as $eventStep) {
            $scenarioStepParentId = $eventStep->getScenarioStepParentId();
            $scenarioStepId = $eventStep->getScenarioStepId();

            if (isset($scenarioStepId)) {
                if (isset($scenarioStepParentId) && array_key_exists($scenarioStepParentId, $createdEventSteps)) {
                    $eventStep->setEventStepParent($createdEventSteps[$scenarioStepParentId]);
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

    /**
     * Closing an event
     *
     * @param  Event $event
     * @param  string $login
     * @param  string $message
     * @return Event
     */
    public function close(Event $event, $login, $message)
    {
        $event->setStatus(Event::STATUS_CLOSED);

        // Manage event status: If status goes to rejected or closed change event steps status
        foreach ($event->getEventSteps() as $eventStep) {
            $eventStepStatus = $eventStep->getLastStatus();

            if ($eventStepStatus->getStatus() !== EventStepStatus::STATUS_VALIDATED) {
                $eventStepStatus->setLogin($login);
                $eventStepStatus->setdateTime(new \DateTime());
                $eventStepStatus->setUserComment($message);
                $eventStepStatus->setStatus(EventStepStatus::STATUS_REJECTED);

                $eventStep->addStatus($eventStepStatus);
            }

            $this->objectManager->persist($eventStep);
        }

        $now = new \Datetime();
        foreach ($event->getPeriods() as $period) {
            $period->setEndDate($now);
            $this->objectManager->persist($period);
        }

        foreach ($event->getMessages() as $msg) {
            foreach ($msg->getChannels() as $channel) {
                $msg->removeChannel($channel);
            }
            $event->removeMessage($msg);
            $this->objectManager->remove($msg);
        }

        return $this->save($event);
    }
}
