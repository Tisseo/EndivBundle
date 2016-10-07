<?php

namespace Tisseo\EndivBundle\Services;

use Tisseo\EndivBundle\Entity\StopTime;

class StopTimeManager extends AbstractManager
{
    //TODO: This function may be improved
    public function save(StopTime $Stop)
    {
        $objectManager = $this->getObjectManager();

        if (!$Stop->getId()) {
            // new stop + new stop_history
            $objectManager->persist($Stop);
            $objectManager->flush();
            $objectManager->refresh($Stop);
            $newId = $Stop->getId();
            $Stop->setId($newId);
        }

        $objectManager->persist($Stop);
        $objectManager->flush();
        $objectManager->refresh($Stop);
    }
}
