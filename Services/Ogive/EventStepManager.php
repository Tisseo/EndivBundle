<?php
namespace Tisseo\EndivBundle\Services\Ogive;

use Doctrine\Common\Persistence\ObjectManager as DoctrineObjectManager;
use Tisseo\EndivBundle\Entity\Ogive\EventStep;
use Tisseo\EndivBundle\Entity\Ogive\EventStepStatus;
use Tisseo\OgiveBundle\Service\TextService;

class EventStepManager extends OgiveManager
{

    private $repository = null;
    private $textRepository;
    private $textService;

    /**
     * EventStepManager constructor.
     *
     * @param DoctrineObjectManager $objectManager
     * @param TextManager $textManager
     */
    public function __construct(
        DoctrineObjectManager $objectManager,
        TextManager $textManager,
        TextService $textService
    ) {
        parent::__construct($objectManager);

        $this->repository = $objectManager->getRepository('TisseoEndivBundle:Ogive\EventStep');
        $this->textRepository = $objectManager->getRepository('TisseoEndivBundle:Ogive\Text');
        $this->textService = $textService;
    }

    public function setStatus(
        EventStep $eventStep,
        $status,
        $login,
        $less = null,
        $comment = null
    ) {
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

    /**
     * Fill the EventStepText with correct data
     *
     * @param EventStep $entity
     * @param string $accessor
     * @param array $collection
     */
    public function updateCollection(EventStep $entity, $accessor, array $collection)
    {
        $eventStepTexts = $entity->getTexts()->toArray();
        if (count($eventStepTexts)) {
            foreach ($eventStepTexts as $eventStepText) {
                if (is_null($eventStepText->getId())) {
                    $text = $this->textRepository->findByLabel($eventStepText->getLabel())[0];
                    $eventStepText->setText($text->getText());
                    $eventStepText->setEventStep($entity);
                    $disruptionId = $entity->getEvent()->getChaosDisruptionId();
                    $this->textService->processTextFromEventStepText($eventStepText, $text, $disruptionId);
                }
            }
        }

        parent::updateCollection($entity, $accessor, $collection);
    }
}
