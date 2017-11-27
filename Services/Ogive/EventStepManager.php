<?php

namespace Tisseo\EndivBundle\Services\Ogive;

use Tisseo\EndivBundle\Entity\Ogive\EventStep;
use Tisseo\EndivBundle\Entity\Ogive\EventStepStatus;

class EventStepManager extends OgiveManager
{
    public function setStatus(
        EventStep $eventStep,
        $status,
        $login,
        $comment
    ) {
        $less = new EventStepStatus();
        $less->setEventStep($eventStep);
        $less->setStatus((int) $status);
        $less->setLogin($login);
        $less->setUserComment($comment);
        $less->setDateTime(new \Datetime());

        $eventStep->addStatus($less);

        if ((int) $status !== EventStepStatus::STATUS_VALIDATED) {
            foreach ($this->findChildSteps($eventStep->getId()) as $step) {
                if ($step->getLastStatus()->getStatus() !== $status) {
                    $status = clone $less;
                    $status->setId(null);
                    $status->setEventStep($step);
                    $step->addStatus($status);
                    $this->objectManager->persist($step);
                }
            }
        }

        $this->save($eventStep);
    }

    /**
     * Find child steps
     *
     * @param $parentStepId
     *
     * @return array
     */
    public function findChildSteps($parentStepId)
    {
        return $this->getRepository()->findBy(
            array('eventStepParent' => $parentStepId)
        );
    }

    /**
     * Manage the collection of StepTexts
     *
     * @param EventStep $eventStep
     * @param array     $originalTexts
     *
     * @return \Tisseo\EndivBundle\Entity\Ogive\OgiveEntity
     */
    public function manage(EventStep $eventStep, array $originalTexts)
    {
        $this->updateCollection($eventStep, 'getTexts', $originalTexts);

        return $this->save($eventStep);
    }
}
