<?php

namespace Tisseo\EndivBundle\Services\Ogive;

use \Traversable;
use Tisseo\EndivBundle\Entity\Ogive\Object as OgiveObject;

class ObjectManager extends OgiveManager
{
    /**
    * Get object names
    *
    * @param Event $event
    */
    public function getEventObjectNames(Traversable $eventObjects)
    {
        // TODO: could use inheritance here and directly bring the relation when instanciating Object class
        $extrema = null;
        foreach ($eventObjects as $eventObject) {
            $object = $eventObject->getObject();
            $entityClass = sprintf('TisseoEndivBundle:%s', ucfirst($object->getObjectType()));
            $objectRef = $this->objectManager->getRepository($entityClass)->find($object->getObjectRef());

            if (empty($objectRef)) {
                $object->setName('N/A');
                continue;
            }

            switch ($object->getObjectType()) {
                case OgiveObject::AGENCY:
                    $object->setName($objectRef->getName());
                    break;
                case OgiveObject::LINE:
                    $object->setName($objectRef->getNumber());
                    break;
                case OgiveObject::STOP:
                    if ($extrema === null) {
                        $extrema = $eventObject->getEvent()->getExtremaPeriodDates();
                    }
                    $object->setName(
                        $objectRef->getCurrentStopHistory($extrema['min'])->getShortName().
                        ' ('.$objectRef->getStopDatasources()->first()->getCode().')'
                    );
                    break;
                default:
                    break;
            }
        }
    }
}
