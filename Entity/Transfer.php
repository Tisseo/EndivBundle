<?php

namespace Tisseo\EndivBundle\Entity;


/**
 * Transfer
 */
class Transfer
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $duration;

    /**
     * @var integer
     */
    private $distance;

    /**
     * @var geometry
     */
    private $theGeom;

    /**
     * @var string
     */
    private $longName;

    /**
     * @var \Tisseo\EndivBundle\Entity\Stop
     */
    private $endStop;

    /**
     * @var \Tisseo\EndivBundle\Entity\Stop
     */
    private $startStop;


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
     * Set duration
     *
     * @param  integer $duration
     * @return Transfer
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * Get duration
     *
     * @return integer
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Set distance
     *
     * @param  integer $distance
     * @return Transfer
     */
    public function setDistance($distance)
    {
        $this->distance = $distance;

        return $this;
    }

    /**
     * Get distance
     *
     * @return integer
     */
    public function getDistance()
    {
        return $this->distance;
    }

    /**
     * Set theGeom
     *
     * @param  geometry $theGeom
     * @return Transfer
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
     * Set longName
     *
     * @param  string $longName
     * @return StopHistory
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
     * Set endStop
     *
     * @param  \Tisseo\EndivBundle\Entity\Stop $endStop
     * @return Transfer
     */
    public function setEndStop(\Tisseo\EndivBundle\Entity\Stop $endStop = null)
    {
        $this->endStop = $endStop;

        return $this;
    }

    /**
     * Get endStop
     *
     * @return \Tisseo\EndivBundle\Entity\Stop
     */
    public function getEndStop()
    {
        return $this->endStop;
    }

    /**
     * Set startStop
     *
     * @param  \Tisseo\EndivBundle\Entity\Stop $startStop
     * @return Transfer
     */
    public function setStartStop(\Tisseo\EndivBundle\Entity\Stop $startStop = null)
    {
        $this->startStop = $startStop;

        return $this;
    }

    /**
     * Get startStop
     *
     * @return \Tisseo\EndivBundle\Entity\Stop
     */
    public function getStartStop()
    {
        return $this->startStop;
    }
}
