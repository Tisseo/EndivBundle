<?php

namespace Tisseo\EndivBundle\Entity\Ogive;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;

/**
 * EventStep
 */
class EventStep extends OgiveEntity
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $rank;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $moment;

    /**
     * @var boolean
     */
    private $mandatory;

    /**
     * @var EventStep
     */
    private $eventStepParent;

    /**
     * @var Connector
     */
    private $connector;

    /**
     * @var ConnectorParamList
     */
    private $connectorParamList;

    /**
     * @var Event
     */
    private $event;

    /**
     * @var Collection
     */
    private $statuses;

    /**
     * @var Collection
     */
    private $texts;

    /**
     * Non mapped property to get eventStep original scenario when adding an eventStep
     */
    private $scenarioStepId;

    /**
     * Non mapped property to get the parent of the original scenario when adding an eventStepParent
     */
    private $scenarioStepParentId;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->statuses = new ArrayCollection();
        $this->texts = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id
     * @param $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Set rank
     *
     * @param integer $rank
     * @return EventStep
     */
    public function setRank($rank)
    {
        $this->rank = $rank;

        return $this;
    }

    /**
     * Get rank
     *
     * @return integer
     */
    public function getRank()
    {
        return $this->rank;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return EventStep
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set moment
     *
     * @param string $moment
     * @return EventStep
     */
    public function setMoment($moment)
    {
        $this->moment = $moment;

        return $this;
    }

    /**
     * Get moment
     *
     * @return string
     */
    public function getMoment()
    {
        return $this->moment;
    }

    /**
     * Set mandatory
     *
     * @param boolean $mandatory
     * @return EventStep
     */
    public function setMandatory($mandatory)
    {
        $this->mandatory = $mandatory;

        return $this;
    }

    /**
     * Get mandatory
     *
     * @return boolean
     */
    public function getMandatory()
    {
        return $this->mandatory;
    }

    /**
     * Set eventStepParent
     *
     * @param EventStep $eventStepParent
     * @return EventStep
     */
    public function setEventStepParent(EventStep $eventStepParent = null)
    {
        $this->eventStepParent = $eventStepParent;

        return $this;
    }

    /**
     * Get eventStepParent
     *
     * @return EventStep
     */
    public function getEventStepParent()
    {
        return $this->eventStepParent;
    }

    /**
     * Set connector
     *
     * @param Connector $connector
     * @return EventStep
     */
    public function setConnector(Connector $connector = null)
    {
        $this->connector = $connector;

        return $this;
    }

    /**
     * Get connector
     *
     * @return Connector
     */
    public function getConnector()
    {
        return $this->connector;
    }

    /**
     * Set connectorParamList
     *
     * @param ConnectorParamList $connectorParamList
     * @return EventStep
     */
    public function setConnectorParamList(ConnectorParamList $connectorParamList = null)
    {
        $this->connectorParamList = $connectorParamList;

        return $this;
    }

    /**
     * Get connectorParamList
     *
     * @return ConnectorParamList
     */
    public function getConnectorParamList()
    {
        return $this->connectorParamList;
    }

    /**
     * Set event
     *
     * @param Event $event
     * @return EventStep
     */
    public function setEvent(Event $event = null)
    {
        $this->event = $event;

        return $this;
    }

    /**
     * Get event
     *
     * @return Event
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * Add status
     *
     * @param EventStepStatus $status
     * @return EventStep
     */
    public function addStatus(EventStepStatus $status)
    {
        $this->statuses->add($status);

        return $this;
    }

    /**
     * Remove status
     *
     * @param EventStepStatus $status
     */
    public function removeStatus(EventStepStatus $status)
    {
        $this->statuses->removeElement($status);
    }

    /**
     * Get statuses
     *
     * @return Collection
     */
    public function getStatuses()
    {
        return $this->statuses;
    }

    /**
     * Set statuses
     *
     * @param Collection $statuses
     * @return EventStep
     */
    public function setStatuses(Collection $statuses)
    {
        $this->statuses = $statuses;

        return $this;
    }

    /**
     * Get last selected status for the event step
     *
     * @return EventStepStatus
     */
    public function getLastStatus(){
        if ($this->statuses->count() == 0) {
            return null;
        }

        return $this->statuses->first();
    }

    /**
     * Add text
     *
     * @param EventStepText $text
     * @return EventStep
     */
    public function addText(EventStepText $text)
    {
        $this->texts->add($text);

        return $this;
    }

    /**
     * Remove text
     *
     * @param EventStepText $text
     */
    public function removeText(EventStepText $text)
    {
        $this->texts->removeElement($text);
    }

    /**
     * Get texts
     *
     * @return Collection
     */
    public function getTexts()
    {
        return $this->texts;
    }

    /**
     * Set texts
     *
     * @param Collection $texts
     * @return EventStep
     */
    public function setTexts(Collection $texts)
    {
        $this->texts = $texts;

        return $this;
    }

    public function getText($type)
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('type', $type))
            ->setMaxResults(1)
        ;

        $texts = $this->texts->matching($criteria);

        if (!$texts->isEmpty())
            return $texts->first();
        else
            return '';
    }

    /**
     * Get scenarioStep Id
     *
     * @return integer
     */
    public function getScenarioStepId()
    {
        return $this->scenarioStepId;
    }

    /**
     * Set scenarioStep id
     *
     * @param integer $scenarioStepId
     * @return EventStep
     */
    public function setScenarioStepId($scenarioStepId)
    {
        $this->scenarioStepId = $scenarioStepId;

        return $this;
    }

    /**
     * Get scenarioStepParent Id
     *
     * @return integer
     */
    public function getScenarioStepParentId()
    {
        return $this->scenarioStepParentId;
    }

    /**
     * Set scenarioStepParent Id
     *
     * @param integer $scenarioStepParentId
     * @return EventStep
     */
    public function setScenarioStepParentId($scenarioStepParentId)
    {
        $this->scenarioStepParentId = $scenarioStepParentId;

        return $this;
    }
}
