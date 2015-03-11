<?php

namespace Tisseo\EndivBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * City
 */
class City
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $insee;

    /**
     * @var string
     */
    private $name;

    /**
     * @var geometry
     */
    private $theGeom;

    /**
     * @var \Tisseo\EndivBundle\Entity\StopArea
     */
    private $mainStopArea;

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
     * Set insee
     *
     * @param integer $insee
     * @return City
     */
    public function setInsee($insee)
    {
        $this->insee = $insee;

        return $this;
    }

    /**
     * Get insee
     *
     * @return integer 
     */
    public function getInsee()
    {
        return $this->insee;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return City
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
     * Set theGeom
     *
     * @param geometry $theGeom
     * @return City
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
     * Set mainStopArea
     *
     * @param \Tisseo\EndivBundle\Entity\StopArea $mainStopArea
     * @return City
     */
    public function setMainStopArea(\Tisseo\EndivBundle\Entity\StopArea $mainStopArea = null)
    {
        $this->mainStopArea = $mainStopArea;

        return $this;
    }

    /**
     * Get mainStopArea
     *
     * @return \Tisseo\EndivBundle\Entity\StopArea 
     */
    public function getMainStopArea()
    {
        return $this->mainStopArea;
    }
}
