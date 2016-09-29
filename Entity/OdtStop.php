<?php

namespace Tisseo\EndivBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OdtStop
 */
class OdtStop
{
    /**
     * @var \DateTime
     */
    private $startDate;

    /**
     * @var \DateTime
     */
    private $endDate;

    /**
     * @var boolean
     */
    private $pickup;

    /**
     * @var boolean
     */
    private $dropOff;

    /**
     * @var \Tisseo\EndivBundle\Entity\OdtArea
     */
    private $odtArea;

    /**
     * @var \Tisseo\EndivBundle\Entity\Stop
     */
    private $stop;

    public function getId()
    {
        return $this->startDate->format('Y-m-d').'/'.$this->stop->getId().'/'.$this->odtArea->getId();
    }

    /**
     * Set startDate
     *
     * @param  \DateTime $startDate
     * @return OdtStop
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
     * @param  \DateTime $endDate
     * @return OdtStop
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
     * Set pickup
     *
     * @param  boolean $pickup
     * @return OdtStop
     */
    public function setPickup($pickup)
    {
        $this->pickup = $pickup;

        return $this;
    }

    /**
     * Get pickup
     *
     * @return boolean
     */
    public function getPickup()
    {
        return $this->pickup;
    }

    /**
     * Set dropOff
     *
     * @param  boolean $dropOff
     * @return OdtStop
     */
    public function setDropOff($dropOff)
    {
        $this->dropOff = $dropOff;

        return $this;
    }

    /**
     * Get dropOff
     *
     * @return boolean
     */
    public function getDropOff()
    {
        return $this->dropOff;
    }

    /**
     * Set odtArea
     *
     * @param  \Tisseo\EndivBundle\Entity\OdtArea $odtArea
     * @return OdtStop
     */
    public function setOdtArea(\Tisseo\EndivBundle\Entity\OdtArea $odtArea = null)
    {
        $this->odtArea = $odtArea;

        return $this;
    }

    /**
     * Get odtArea
     *
     * @return \Tisseo\EndivBundle\Entity\OdtArea
     */
    public function getOdtArea()
    {
        return $this->odtArea;
    }

    /**
     * Set stop
     *
     * @param  \Tisseo\EndivBundle\Entity\Stop $stop
     * @return OdtStop
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
