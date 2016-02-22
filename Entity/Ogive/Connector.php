<?php

namespace Tisseo\EndivBundle\Entity\Ogive;

use Doctrine\ORM\Mapping as ORM;

/**
 * Connector
 */
class Connector
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
     * @var string
     */
    private $connectorType;

    /**
     * @var string
     */
    private $details;


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
     * @return Connector
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
     * Set connectorType
     *
     * @param string $connectorType
     * @return Connector
     */
    public function setConnectorType($connectorType)
    {
        $this->connectorType = $connectorType;

        return $this;
    }

    /**
     * Get connectorType
     *
     * @return string 
     */
    public function getConnectorType()
    {
        return $this->connectorType;
    }

    /**
     * Set details
     *
     * @param string $details
     * @return Connector
     */
    public function setDetails($details)
    {
        $this->details = $details;

        return $this;
    }

    /**
     * Get details
     *
     * @return string 
     */
    public function getDetails()
    {
        return $this->details;
    }
}
