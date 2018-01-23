<?php

namespace Tisseo\EndivBundle\Entity;

/**
 * PoiAddress
 */
class PoiAddress
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $address;

    /**
     * @var bool
     */
    private $isEntrance;

    /**
     * @var geometry
     */
    private $theGeom;

    /**
     * @var \Tisseo\EndivBundle\Entity\Poi
     */
    private $poi;

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
     * Set address
     *
     * @param string $address
     *
     * @return PoiAddress
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set isEntrance
     *
     * @param bool $isEntrance
     *
     * @return PoiAddress
     */
    public function setIsEntrance($isEntrance)
    {
        $this->isEntrance = $isEntrance;

        return $this;
    }

    /**
     * Get isEntrance
     *
     * @return bool
     */
    public function getIsEntrance()
    {
        return $this->isEntrance;
    }

    /**
     * Set theGeom
     *
     * @param geometry $theGeom
     *
     * @return PoiAddress
     */
    public function setTheGeom($theGeom)
    {
        $this->theGeom = $theGeom;

        return $this;
    }

    /**
     * Get theGeom
     *
     * @return geometry
     */
    public function getTheGeom()
    {
        return $this->theGeom;
    }

    /**
     * Set poi
     *
     * @param \Tisseo\EndivBundle\Entity\Poi $poi
     *
     * @return PoiAddress
     */
    public function setPoi(\Tisseo\EndivBundle\Entity\Poi $poi = null)
    {
        $this->poi = $poi;

        return $this;
    }

    /**
     * Get poi
     *
     * @return \Tisseo\EndivBundle\Entity\Poi
     */
    public function getPoi()
    {
        return $this->poi;
    }
}
