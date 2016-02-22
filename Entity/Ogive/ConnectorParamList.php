<?php

namespace Tisseo\EndivBundle\Entity\Ogive;

use Doctrine\ORM\Mapping as ORM;

/**
 * ConnectorParamList
 */
class ConnectorParamList
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
     * @var \Doctrine\Common\Collections\Collection
     */
    private $includedConnectorParamList;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->includedConnectorParamList = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @param string $name
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
     * @param integer $sort
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
     * Add includedConnectorParamList
     *
     * @param ConnectorParamList $includedConnectorParamList
     * @return ConnectorParamList
     */
    public function addIncludedConnectorParamList(ConnectorParamList $includedConnectorParamList)
    {
        $this->includedConnectorParamList[] = $includedConnectorParamList;

        return $this;
    }

    /**
     * Remove includedConnectorParamList
     *
     * @param ConnectorParamList $includedConnectorParamList
     */
    public function removeIncludedConnectorParamList(ConnectorParamList $includedConnectorParamList)
    {
        $this->includedConnectorParamList->removeElement($includedConnectorParamList);
    }

    /**
     * Get includedConnectorParamList
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getIncludedConnectorParamList()
    {
        return $this->includedConnectorParamList;
    }
}
