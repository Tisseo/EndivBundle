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
        $less = null,
        $comment = null
    ) {
        if (!($less instanceof EventStepStatus)) {
            $less = new EventStepStatus;
        }

        if (!empty($comment)) {
            $less->setUserComment($comment);
        }

        $less->setEventStep($eventStep);
        $less->setStatus($status);
        $less->setLogin($login);
        $less->setDateTime(new \Datetime());

        $eventStep->addStatus($less);
        $this->save($eventStep);
    }

    /**
     * Find child steps
     *
     * @param $parentStepId
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
     * @param array $originalTexts
     * @return \Tisseo\EndivBundle\Entity\Ogive\OgiveEntity
     */
    public function manage(EventStep $eventStep, array $originalTexts)
    {
        $this->updateCollection($eventStep, 'getTexts', $originalTexts);

        return $this->save($eventStep);
    }
}
