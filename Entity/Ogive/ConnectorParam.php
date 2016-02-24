<?php

namespace Tisseo\EndivBundle\Entity\Ogive;

use Doctrine\ORM\Mapping as ORM;

/**
 * ConnectorParam
 */
class ConnectorParam
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $paramType;

    /**
     * @var string
     */
    private $param;

    /**
     * @var ConnectorParamList
     */
    private $connectorParamList;


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
     * Set paramType
     *
     * @param string $paramType
     * @return ConnectorParam
     */
    public function setParamType($paramType)
    {
        $this->paramType = $paramType;

        return $this;
    }

    /**
     * Get paramType
     *
     * @return string 
     */
    public function getParamType()
    {
        return $this->paramType;
    }

    /**
     * Set param
     *
     * @param string $param
     * @return ConnectorParam
     */
    public function setParam($param)
    {
        $this->param = $param;

        return $this;
    }

    /**
     * Get param
     *
     * @return string 
     */
    public function getParam()
    {
        return $this->param;
    }

    /**
     * Set connectorParamList
     *
     * @param ConnectorParamList $connectorParamList
     * @return ConnectorParam
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
}
