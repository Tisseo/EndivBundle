<?php

namespace Tisseo\EndivBundle\Entity\Ogive;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Exclude;

/**
 * ConnectorParamList
 *
 * @ExclusionPolicy("none")
 */
class ConnectorParamList extends OgiveEntity
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var integer
     */
    private $sort;

    /**
     * @var Collection
     * @Exclude
     */
    private $ownerCpls;

    /**
     * @var Collection
     * @Exclude
     */
    private $includedCpls;

    /**
     * @var Collection
     * @Exclude
     */
    private $connectorParams;

    /**
     * @var Collection
     * @Exclude
     */
    private $scenarioSteps;

    /**
     * @var Collection
     * @Exclude
     */
    private $eventSteps;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->includedCpls = new ArrayCollection();
        $this->ownerCpls = new ArrayCollection();
        $this->connectorParams = new ArrayCollection();
        $this->scenarioSteps = new ArrayCollection();
        $this->eventSteps = new ArrayCollection();
    }

    /**
     * Set id
     *
     * @param  integer $identifier
     * @return ScenarioStep
     */
    public function setId($identifier)
    {
        $this->id = $identifier;

        return $this;
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
     * Set name
     *
     * @param  string $name
     * @return ConnectorParamList
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
     * Set sort
     *
     * @param  integer $sort
     * @return ConnectorParamList
     */
    public function setSort($sort)
    {
        $this->sort = $sort;

        return $this;
    }

    /**
     * Get sort
     *
     * @return integer
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * Add includedCpl
     *
     * @param  ConnectorParamList $connectorParamList
     * @return ConnectorParamList
     */
    public function addIncludedCpl(ConnectorParamList $connectorParamList)
    {
        $this->includedCpls->add($connectorParamList);

        return $this;
    }

    /**
     * Remove includedCpl
     *
     * @param  ConnectorParamList $connectorParamList
     * @return ConnectorParamList
     */
    public function removeIncludedCpl(ConnectorParamList $connectorParamList)
    {
        $this->includedCpls->removeElement($connectorParamList);

        return $this;
    }

    /**
     * Set includedCpls
     *
     * @return ConnectorParamList
     */
    public function setIncludedCpls(Collection $includedCpls)
    {
        $this->includedCpls = $includedCpls;

        return $this;
    }

    /**
     * Get includedCpls
     *
     * @return Collection
     */
    public function getIncludedCpls()
    {
        return $this->includedCpls;
    }

    /**
     * Add ownerCpl
     *
     * @param  ConnectorParamList $connectorParamList
     * @return ConnectorParamList
     */
    public function addOwnerCpl(ConnectorParamList $connectorParamList)
    {
        $this->ownerCpls->add($connectorParamList);

        return $this;
    }

    /**
     * Remove ownerCpl
     *
     * @param  ConnectorParamList $connectorParamList
     * @return ConnectorParamList
     */
    public function removeOwnerCpl(ConnectorParamList $connectorParamList)
    {
        $this->ownerCpls->removeElement($connectorParamList);

        return $this;
    }

    /**
     * Set ownerCpls
     *
     * @return ConnectorParamList
     */
    public function setOwnerCpls(Collection $ownerCpls)
    {
        $this->ownerCpls = $ownerCpls;

        return $this;
    }

    /**
     * Get ownerCpls
     *
     * @return Collection
     */
    public function getOwnerCpls()
    {
        return $this->ownerCpls;
    }

    /**
     * Get connectorParams
     *
     * @return Collection
     */
    public function getConnectorParams()
    {
        return $this->connectorParams;
    }

    /**
     * Set connectorParams
     *
     * @param  Collection connectorParams
     * @return ConnectorParamList
     */
    public function setConnectorParams($connectorParams)
    {
        $this->connectorParams = $connectorParams;

        return $this;
    }

    /**
     * Add connectorParam
     *
     * @param  ConnectorParam $connectorParam
     * @return ConnectorParamList
     */
    public function addConnectorParam(ConnectorParam $connectorParam)
    {
        $this->connectorParams->add($connectorParam);
        $connectorParam->setConnectorParamList($this);

        return $this;
    }

    /**
     * Remove connectorParam
     *
     * @param  ConnectorParam $connectorParam
     * @return ConnectorParamList
     */
    public function removeConnectorParam(ConnectorParam $connectorParam)
    {
        $this->connectorParams->removeElement($connectorParam);

        return $this;
    }

    /**
     * isLinked
     *
     * @return boolean
     */
    public function isLinked()
    {
        return !($this->eventSteps->count() == 0 && $this->scenarioSteps->count() == 0);
    }
}
