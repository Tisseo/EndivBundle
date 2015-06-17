<?php

namespace Tisseo\EndivBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Waypoint
 */
class Waypoint
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Tisseo\EndivBundle\Entity\Stop
     */
    private $stop = null;

    /**
     * @var \Tisseo\EndivBundle\Entity\OdtArea
     */
    private $area = null;

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
     * Set stop
     *
     * @param \Tisseo\EndivBundle\Entity\Stop $stop
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
        return $this->area;

    }

    /**
     * set OdtArea
     *
     * @param \Tisseo\EndivBundle\Entity\OdtArea $area
     * @return Waypoint
     */
    public function setOdtArea(\Tisseo\EndivBundle\Entity\OdtArea $area = null)
    {
        $this->area = $area;
        return $this;
    }
}
