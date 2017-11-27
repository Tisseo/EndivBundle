<?php

namespace Tisseo\EndivBundle\Entity\Ogive;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Exclude;

/**
 * Connector
 *
 * @ExclusionPolicy("none")
 */
class Connector extends OgiveEntity
{
    const MAIL = 2;
    const HOMEPAGE = 3;
    const NETWORK_INFO = 4;

    /**
     * @var static array
     */
    public static $connectorTypes = array(
        self::MAIL => self::MAIL,
        self::HOMEPAGE => self::HOMEPAGE,
        self::NETWORK_INFO => self::NETWORK_INFO
    );

    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $type;

    /**
     * @var string
     */
    private $details;

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
        $this->scenarioSteps = new ArrayCollection();
        $this->eventSteps = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
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
     * Set type
     *
     * @param int $type
     *
     * @return Connector
     */
    public function setType($type)
    {
        if (in_array($type, self::$connectorTypes)) {
            $this->type = $type;
        }

        return $this;
    }

    /**
     * Get type
     *
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set details
     *
     * @param string $details
     *
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

    /**
     * isLinked
     *
     * @return bool
     */
    public function isLinked()
    {
        return !($this->eventSteps->count() == 0 && $this->scenarioSteps->count() == 0);
    }
}
