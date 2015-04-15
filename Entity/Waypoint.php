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
}
