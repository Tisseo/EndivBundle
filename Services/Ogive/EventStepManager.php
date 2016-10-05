<?php
namespace Tisseo\EndivBundle\Services\Ogive;

use Tisseo\EndivBundle\Entity\Ogive\LinkEventStepStatus;
use Tisseo\EndivBundle\Entity\Ogive\EventStep;

class EventStepManager extends OgiveManager
{
    public function setStatus(
        EventStep $eventStep,
        $status,
        $login,
        $less = null,
        $comment = null
    ) {
        if (!($less instanceof LinkEventStepStatus)) {
            $less = new LinkEventStepStatus;
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
}
