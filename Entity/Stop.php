<?php

namespace Tisseo\EndivBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Stop
 */
class Stop
{
    /**
     * @var \Tisseo\EndivBundle\Entity\Waypoint
     */
    private $id;

    /**
     * @var \Tisseo\EndivBundle\Entity\Stop
     */
    private $masterStop;

    /**
     * @var \Tisseo\EndivBundle\Entity\StopArea
     */
    private $stopArea;


    /**
     * Set id
     *
     * @param \Tisseo\EndivBundle\Entity\Waypoint $id
     * @return Stop
     */
    public function setId(\Tisseo\EndivBundle\Entity\Waypoint $id = null)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id
     *
     * @return \Tisseo\EndivBundle\Entity\Waypoint 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set masterStop
     *
     * @param \Tisseo\EndivBundle\Entity\Stop $masterStop
     * @return Stop
     */
    public function setMasterStop(\Tisseo\EndivBundle\Entity\Stop $masterStop = null)
    {
        $this->masterStop = $masterStop;

        return $this;
    }

    /**
     * Get masterStop
     *
     * @return \Tisseo\EndivBundle\Entity\Stop 
     */
    public function getMasterStop()
    {
        return $this->masterStop;
    }

    /**
     * Set stopArea
     *
     * @param \Tisseo\EndivBundle\Entity\StopArea $stopArea
     * @return Stop
     */
    public function setStopArea(\Tisseo\EndivBundle\Entity\StopArea $stopArea = null)
    {
        $this->stopArea = $stopArea;

        return $this;
    }

    /**
     * Get stopArea
     *
     * @return \Tisseo\EndivBundle\Entity\StopArea 
     */
    public function getStopArea()
    {
        return $this->stopArea;
    }
    /**
     * @var \Tisseo\EndivBundle\Entity\Waypoint
     */
    private $waypoint;


    /**
     * Set waypoint
     *
     * @param \Tisseo\EndivBundle\Entity\Waypoint $waypoint
     * @return Stop
     */
    public function setWaypoint(\Tisseo\EndivBundle\Entity\Waypoint $waypoint = null)
    {
        $this->waypoint = $waypoint;

        return $this;
    }

    /**
     * Get waypoint
     *
     * @return \Tisseo\EndivBundle\Entity\Waypoint 
     */
    public function getWaypoint()
    {
        return $this->waypoint;
    }
}
