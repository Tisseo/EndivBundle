<?php

namespace Tisseo\EndivBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * StopHistory
 */
class StopHistory
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $startDate;

    /**
     * @var \DateTime
     */
    private $endDate;

    /**
     * @var string
     */
    private $shortName;

    /**
     * @var string
     */
    private $longName;

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
    private $stop;


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
     * Set startDate
     *
     * @param \DateTime $startDate
     * @return StopHistory
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get startDate
     *
     * @return \DateTime 
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set endDate
     *
     * @param \DateTime $endDate
     * @return StopHistory
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get endDate
     *
     * @return \DateTime 
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Set shortName
     *
     * @param string $shortName
     * @return StopHistory
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
     * Set theGeom
     *
     * @param geometry $theGeom
     * @return StopHistory
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
     * @return StopHistory
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
     * Set stop
     *
     * @param \Tisseo\EndivBundle\Entity\Stop $stop
     * @return StopHistory
     */
    public function setStop(\Tisseo\EndivBundle\Entity\Stop $stop = null)
    {
        $this->stop = $stop;

        return $this;
    }

    /**
     * Get stop
     *
     * @return \Tisseo\EndivBundle\Entity\Stop 
     */
    public function getStop()
    {
        return $this->stop;
    }
}
