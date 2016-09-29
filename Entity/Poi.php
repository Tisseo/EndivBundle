<?php

namespace Tisseo\EndivBundle\Entity;


/**
 * Poi
 */
class Poi
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
    private $cityId;

    /**
     * @var \Tisseo\EndivBundle\Entity\PoiType
     */
    private $poiType;

    /**
     * @var integer
     */
    private $priority;

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
     * @return Poi
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
     * Set cityId
     *
     * @param  integer $cityId
     * @return Poi
     */
    public function setCityId($cityId)
    {
        $this->cityId = $cityId;

        return $this;
    }

    /**
     * Get cityId
     *
     * @return integer
     */
    public function getCityId()
    {
        return $this->cityId;
    }

    /**
     * Set poiType
     *
     * @param  \Tisseo\EndivBundle\Entity\PoiType $poiType
     * @return Poi
     */
    public function setPoiType(\Tisseo\EndivBundle\Entity\PoiType $poiType = null)
    {
        $this->poiType = $poiType;

        return $this;
    }

    /**
     * Get poiType
     *
     * @return \Tisseo\EndivBundle\Entity\PoiType
     */
    public function getPoiType()
    {
        return $this->poiType;
    }

    /**
     * Set priority
     *
     * @param  integer $priority
     * @return Poi
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Get priority
     *
     * @return integer
     */
    public function getPriority()
    {
        return $this->priority;
    }
}
