<?php
namespace Tisseo\EndivBundle\Services\Ogive;

use Doctrine\Common\Persistence\ObjectManager as DoctrineObjectManager;
use Tisseo\EndivBundle\Entity\Ogive\EventStepStatus;
use Tisseo\EndivBundle\Entity\Ogive\EventStep;

class EventStepManager extends OgiveManager
{

    private $repository = null;

    public function __construct(DoctrineObjectManager $objectManager)
    {
        parent::__construct($objectManager);

        $this->repository = $objectManager->getRepository('TisseoEndivBundle:Ogive\EventStep');
    }

    public function setStatus(
        EventStep $eventStep,
        $status,
        $login,
        $less = null,
        $comment = null
    )
    {
        try {

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

            return true;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }


    /**
     * Find child steps
     *
     * @param $parentStepId
     * @return array
     * @throws \UnexpectedValueException
     */
    public function findChildSteps($parentStepId)
    {
        $all = $this->repository->findBy(
            array(
                'eventStepParent' => $parentStepId
            )
        );
        if (count($all) > 0) {
            return $all;
        }

        return null;
    }
}
