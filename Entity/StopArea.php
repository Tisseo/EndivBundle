<?php

namespace Tisseo\EndivBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * StopArea
 */
class StopArea
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $shortName;

    /**
     * @var string
     */
    private $longName;

    /**
     * @var integer
     */
    private $transferDuration;

    /**
     * @var geometry
     */
    private $theGeom;

    /**
     * @var \Tisseo\EndivBundle\Entity\City
     */
    private $city;


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
     * Set shortName
     *
     * @param string $shortName
     * @return StopArea
     */
    public function setShortName($shortName)
    {
        $this->shortName = $shortName;

        return $this;
    }

    /**
     * Get shortName
     *
     * @return string 
     */
    public function getShortName()
    {
        return $this->shortName;
    }

    /**
     * Set longName
     *
     * @param string $longName
     * @return StopArea
     */
    public function setLongName($longName)
    {
        $this->longName = $longName;

        return $this;
    }

    /**
     * Get longName
     *
     * @return string 
     */
    public function getLongName()
    {
        return $this->longName;
    }

    /**
     * Set transferDuration
     *
     * @param integer $transferDuration
     * @return StopArea
     */
    public function setTransferDuration($transferDuration)
    {
        $this->transferDuration = $transferDuration;

        return $this;
    }

    /**
     * Get transferDuration
     *
     * @return integer 
     */
    public function getTransferDuration()
    {
        return $this->transferDuration;
    }

    /**
     * Set theGeom
     *
     * @param geometry $theGeom
     * @return StopArea
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
     * Set city
     *
     * @param \Tisseo\EndivBundle\Entity\City $city
     * @return StopArea
     */
    public function setCity(\Tisseo\EndivBundle\Entity\City $city = null)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return \Tisseo\EndivBundle\Entity\City 
     */
    public function getCity()
    {
        return $this->city;
    }
}
