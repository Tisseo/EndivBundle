<?php

namespace Tisseo\EndivBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

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
     * @var \Doctrine\Common\Collections\Collection
     */
    private $stopAreaDatasources;	

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->stopAreaDatasources = new ArrayCollection();
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
	
    /**
     * Get stopAreaDatasources
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getStopAreaDatasources()
    {
        return $this->stopAreaDatasources;
    }

    /**
     * Set stopAreaDatasources
     *
     * @param \Doctrine\Common\Collections\Collection $stopAreaDatasources
     * @return Line
     */
    public function setStopAreaDatasources(Collection $stopAreaDatasources)
    {
        $this->stopAreaDatasources = $stopAreaDatasources;
        foreach ($this->stopAreaDatasources as $stopAreaDatasource) {
            $stopAreaDatasource->setStopArea($this);
        }
        return $this;
    }

    /**
     * Add stopAreaDatasource
     *
     * @param StopAreaDatasource $stopAreaDatasources
     * @return Line
     */
    public function addStopAreaDatasources(StopAreaDatasource $stopAreaDatasource)
    {
        $this->stopAreaDatasources[] = $stopAreaDatasource;
        $stopAreaDatasource->setStopArea($this);
        return $this;
    }

    /**
     * Remove stopAreaDatasources
     *
     * @param StopAreaDatasource $stopAreaDatasources
     */
    public function removeStopAreaDatasources(StopAreaDatasource $stopAreaDatasources)
    {
        $this->stopAreaDatasources->removeElement($stopAreaDatasources);
    }	

    public function getNameLabel()
    {
        return $this->shortName.' '.$this->city->getName();
    }	
}
