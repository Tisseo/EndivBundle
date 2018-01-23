<?php

namespace Tisseo\EndivBundle\Entity;

/**
 * Waypoint
 */
class Waypoint
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var \Tisseo\EndivBundle\Entity\Stop
     */
    private $stop = null;

    /**
     * @var \Tisseo\EndivBundle\Entity\OdtArea
     */
    private $odtArea = null;

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
     * Set stop
     *
     * @param \Tisseo\EndivBundle\Entity\Stop $stop
     *
     * @return Waypoint
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

    /**
     * Get OdtArea
     *
     * @return \Tisseo\EndivBundle\Entity\OdtArea
     */
    public function getOdtArea()
    {
        return $this->odtArea;
    }

    /**
     * set OdtArea
     *
     * @param \Tisseo\EndivBundle\Entity\OdtArea $odtArea
     *
     * @return Waypoint
     */
    public function setOdtArea(\Tisseo\EndivBundle\Entity\OdtArea $odtArea = null)
    {
        $this->odtArea = $odtArea;

        return $this;
    }

    public function isOdtAreaWaypoint()
    {
        return is_null($this->waypoint->getStop()) and !is_null($this->waypoint->getOdtArea());
    }
}
