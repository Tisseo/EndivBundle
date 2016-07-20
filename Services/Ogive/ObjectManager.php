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
            return;
        }

        switch ($object->getObjectType()) {
            case OgiveObject::AGENCY:
                $meta->label = $objectRef->getName();
                break;
            case OgiveObject::LINE:
                $meta->label = $objectRef->getNumber();
                $meta->physical_mode = $objectRef->getPhysicalMode()->getName();

                $lineVersion = $objectRef->getCurrentLineVersion();
                if (empty($lineVersion)) {
                    $lineVersion = $objectRef->getLastLineVersion();
                }
                if (!empty($lineVersion)) {
                    $meta->background_color = $lineVersion->getBgColor()->getHtml();
                    $meta->foreground_color = $lineVersion->getFgColor()->getHtml();
                }

                break;
            case OgiveObject::STOP:
                $meta->label = $objectRef->getCurrentStopHistory($extrema['min'])->getShortName();
                $meta->code = $objectRef->getStopDatasources()->first()->getCode();
                $meta->city = ucfirst(strtolower($objectRef->getStopArea()->getCity()->getName()));
                break;
            case OgiveObject::STOP_AREA:
                $meta->label = $objectRef->getShortName();
                $meta->city = ucfirst(strtolower($objectRef->getCity()->getName()));
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
        $entityName = ucfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $object->getObjectType()))));
        $entityClass = sprintf('TisseoEndivBundle:%s', $entityName);
        return $this->objectManager->getRepository($entityClass)->find($object->getObjectRef());
    }
}
