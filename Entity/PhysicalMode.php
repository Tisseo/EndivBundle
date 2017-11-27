<?php

namespace Tisseo\EndivBundle\Entity;

/**
 * PhysicalMode
 */
class PhysicalMode
{
    const PHYSICAL_MODE_METRO = 1;
    const PHYSICAL_MODE_TRAM = 2;
    const PHYSICAL_MODE_BUS = 3;
    const PHYSICAL_MODE_TAD = 4;
    const PHYSICAL_MODE_TRAIN = 5;
    const PHYSICAL_MODE_CAR = 7;
    const PHYSICAL_MODE_SCOL = 8;
    const PHYSICAL_MODE_NAV = 9;
    const PHYSICAL_MODE_LINEO = 10;

    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $type;

    /**
     * @var float
     */
    private $co2Emission;

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
     * @return PhysicalMode
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
     * @param string $type
     *
     * @return PhysicalMode
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get co2Emission
     *
     * @return float
     */
    public function getCo2Emission()
    {
        return $this->co2Emission;
    }

    /**
     * Set co2Emission
     *
     * @param  float co2Emission
     *
     * @return PhysicalMode
     */
    public function setCo2Emission($co2Emission)
    {
        $this->co2Emission = $co2Emission;

        return $this;
    }
}
