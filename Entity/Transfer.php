<?php

namespace Tisseo\EndivBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

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
     * @var boolean
     */
    private $accessibility;

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
     * @param integer $duration
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
     * @param integer $distance
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
     * @param geometry $theGeom
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
     * Set accessibility
     *
     * @param boolean $accessibility
     * @return Transfer
     */
    public function setAccessibility($accessibility)
    {
        $this->accessibility = $accessibility;

        return $this;
    }

    /**
     * Get accessibility
     *
     * @return boolean 
     */
    public function getAccessibility()
    {
        return $this->accessibility;
    }

    /**
     * Set endStop
     *
     * @param \Tisseo\EndivBundle\Entity\Stop $endStop
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
     * @param \Tisseo\EndivBundle\Entity\Stop $startStop
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
