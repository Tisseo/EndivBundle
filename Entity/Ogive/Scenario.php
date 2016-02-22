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
    private $chaosType;

    /**
     * @var guid
     */
    private $chaosCause;


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
     * Set chaosType
     *
     * @param guid $chaosType
     * @return Scenario
     */
    public function setChaosType($chaosType)
    {
        $this->chaosType = $chaosType;

        return $this;
    }

    /**
     * Get chaosType
     *
     * @return guid 
     */
    public function getChaosType()
    {
        return $this->chaosType;
    }

    /**
     * Set chaosCause
     *
     * @param guid $chaosCause
     * @return Scenario
     */
    public function setChaosCause($chaosCause)
    {
        $this->chaosCause = $chaosCause;

        return $this;
    }

    /**
     * Get chaosCause
     *
     * @return guid 
     */
    public function getChaosCause()
    {
        return $this->chaosCause;
    }
}
