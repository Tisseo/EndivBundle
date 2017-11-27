<?php

namespace Tisseo\EndivBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * StopArea
 */
class StopArea extends ObjectDatasource
{
    /**
     * @var int
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
     * @var int
     */
    private $transferDuration = 3;

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
     * @var \Doctrine\Common\Collections\Collection
     */
    private $stops;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $aliases;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->stopAreaDatasources = new ArrayCollection();
        $this->stops = new ArrayCollection();
        $this->aliases = new ArrayCollection();
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
     * Set shortName
     *
     * @param string $shortName
     *
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
     *
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
     * @param int $transferDuration
     *
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
     * @return int
     */
    public function getTransferDuration()
    {
        return $this->transferDuration;
    }

    /**
     * Set theGeom
     *
     * @param geometry $theGeom
     *
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
     *
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
     *
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
     * @param StopAreaDatasource $stopAreaDatasource
     *
     * @return Line
     */
    public function addStopAreaDatasource(StopAreaDatasource $stopAreaDatasource)
    {
        $this->stopAreaDatasources->add($stopAreaDatasource);
        $stopAreaDatasource->setStopArea($this);

        return $this;
    }

    /**
     * Remove stopAreaDatasource
     *
     * @param StopAreaDatasource $stopAreaDatasource
     */
    public function removeStopAreaDatasource(StopAreaDatasource $stopAreaDatasource)
    {
        $this->stopAreaDatasources->removeElement($stopAreaDatasource);
    }

    /**
     * Get getStops
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getStops()
    {
        return $this->stops;
    }

    /**
     * Set stops
     *
     * @param \Doctrine\Common\Collections\Collection $stops
     *
     * @return Line
     */
    public function setStops(Collection $stops)
    {
        $this->stops = $stops;
        foreach ($this->stops as $stop) {
            $stop->setStopArea($this);
        }

        return $this;
    }

    /**
     * Add stop
     *
     * @param Stop $stop
     *
     * @return Line
     */
    public function addStop(Stop $stop)
    {
        $this->stops[] = $stop;
        $stop->setStopArea($this);

        return $this;
    }

    /**
     * Remove stop
     *
     * @param Stop $stop
     */
    public function removeStop(Stop $stop)
    {
        $this->stops->removeElement($stop);
    }

    /**
     * Get getAliases
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAliases()
    {
        return $this->aliases;
    }

    /**
     * Set aliases
     *
     * @param \Doctrine\Common\Collections\Collection $aliases
     *
     * @return StopArea
     */
    public function setAliases(Collection $aliases)
    {
        $this->aliases = $aliases;
        foreach ($this->aliases as $a) {
            $a->setStopArea($this);
        }

        return $this;
    }

    /**
     * Add alias
     *
     * @param Alias $alias
     *
     * @return StopArea
     */
    public function addAlias(Alias $alias)
    {
        $this->aliases[] = $alias;
        $alias->setStopArea($this);

        return $this;
    }

    /**
     * Remove aslias
     *
     * @param Alias $aliases
     */
    public function removeAlias(Alias $alias)
    {
        $this->aliases->removeElement($alias);
    }

    /**
     * Name label
     *
     * @return $string custom label
     */
    public function getNameLabel()
    {
        return $this->shortName.' '.$this->city->getName();
    }

    public function isMainOfCity()
    {
        return $this === $this->getCity()->getMainStopArea();
    }
}
