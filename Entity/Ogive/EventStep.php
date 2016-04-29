<?php

namespace Tisseo\EndivBundle\Entity\Ogive;

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
     * @var \Doctrine\Common\Collections\Collection
     */
    private $status;

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
        $this->status = new \Doctrine\Common\Collections\ArrayCollection();
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
     *
     * @param integer $id
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
    public function addStatus(LinkEventStepStatus $status)
    {
        $this->status[] = $status;

        return $this;
    }

    /**
     * Remove status
     *
     * @param EventStepStatus $status
     */
    public function removeStatus(LinkEventStepStatus $status)
    {
        $this->status->removeElement($status);
    }

    /**
     * Get status
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Get last selected status for the event step
     *
     * @return LinkEventStepStatus
     */
    public function getLastStatus(){
        if ($this->status->count() === 0) {
            return null;
        }
        $criteria = Criteria::create()
            ->orderBy(array('dateTime' => Criteria::DESC))
            ->setMaxResults(1);
        return $this->status->matching($criteria)->first();
    }

    /**
     * Add last selected status to event step status list
     *
     * @param EventStepStatus $lastStatus
     */
    public function setLastStatus(LinkEventStepStatus $lastStatus){
        //TODO remove this method
        return $this->addStatus($lastStatus);
    }

    /**
     * Get original scenarioStep Id
     */
    public function getScenarioStepId()
    {
        return $this->scenarioStepId;
    }

    /**
     * Set original scenarioStep id
     * @param integer $scenarioStepId
     */
    public function setScenarioStepId($scenarioStepId)
    {
        $this->scenarioStepId = $scenarioStepId;
        return $this;
    }

    public function getScenarioStepParentId()
    {
        return $this->scenarioStepParentId;
    }

    public function setScenarioStepParentId($scenarioStepParentId)
    {
        $this->scenarioStepParentId = $scenarioStepParentId;
        return $this;
    }


}
