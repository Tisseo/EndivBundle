<?php

namespace Tisseo\EndivBundle\Entity\Ogive;

use Doctrine\ORM\Mapping as ORM;

/**
 * Scenario
 */
class Scenario
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
     * @var guid
     */
    private $severityId;

    /**
     * @var guid
     */
    private $causeId;

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
     * @return Scenario
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
     * Set severityId
     *
     * @param guid $severityId
     * @return Scenario
     */
    public function setSeverityId($severityId)
    {
        $this->severityId = $severityId;

        return $this;
    }

    /**
     * Get severityId
     *
     * @return guid 
     */
    public function getSeverityId()
    {
        return $this->severityId;
    }

    /**
     * Set causeId
     *
     * @param guid $causeId
     * @return Scenario
     */
    public function setCauseId($causeId)
    {
        $this->causeId = $causeId;

        return $this;
    }

    /**
     * Get causeId
     *
     * @return guid 
     */
    public function getCauseId()
    {
        return $this->causeId;
    }
}
