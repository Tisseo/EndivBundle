<?php

namespace Tisseo\EndivBundle\Services\Ogive;

use \Traversable;
use Tisseo\EndivBundle\Entity\Ogive\Object as OgiveObject;
use Tisseo\EndivBundle\Entity\Ogive\Event;

class ObjectManager extends OgiveManager
{
    /**
    * set event object meta information
    *
    * @param Event $event
    */
    public function setEventObjectsMetaInformation(Event $event)
    {
        // TODO: could use inheritance here and directly bring the relation when instanciating Object class
        $extrema = null;
        foreach ($event->getEventObjects() as $eventObject) {
            if ($eventObject->getObject()->getObjectType() === OgiveObject::STOP && empty($extrema)) {
                $extrema = $eventObject->getEvent()->getExtremaPeriodDates();
            }
            $this->setMetaInformation($eventObject->getObject(), $extrema);
        }
    }

    /**
     * Set object meta informations
     *
     * @param OgiveObject $object
     */
    public function setMetaInformation(OgiveObject $object, $extrema = null)
    {
        $meta = new \stdclass;
        $meta->label = 'N/A';

        $objectRef = $this->getObjectReference($object);

        if (empty($objectRef)) {
            $object->setMeta($meta);
            continue;
        }

        switch ($object->getObjectType()) {
            case OgiveObject::AGENCY:
                $meta->label = $objectRef->getName();
                break;
            case OgiveObject::LINE:
                $lineVersion = $objectRef->getCurrentLineVersion();
                $meta->label = $objectRef->getNumber();
                $meta->background_color = $lineVersion->getBgColor()->getHtml();
                $meta->foreground_color = $lineVersion->getFgColor()->getHtml();
                $meta->physical_mode = $objectRef->getPhysicalMode()->getName();
                break;
            case OgiveObject::STOP:
                $meta->label = $objectRef->getCurrentStopHistory($extrema['min'])->getShortName();
                $meta->code = $objectRef->getStopDatasources()->first()->getCode();
                $meta->city = ucfirst(strtolower($objectRef->getStopArea()->getCity()->getName()));
                break;
            default:
                break;
        }

        $object->setMeta($meta);
    }

    /**
     * Get object in TID referential
     *
     * @param OgiveObject $object
     * @return mixed
     */
    public function getObjectReference(OgiveObject $object)
    {
        $entityClass = sprintf('TisseoEndivBundle:%s', ucfirst($object->getObjectType()));
        return $this->objectManager->getRepository($entityClass)->find($object->getObjectRef());
    }
}
