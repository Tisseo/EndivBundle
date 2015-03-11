<?php

namespace Tisseo\EndivBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OdtArea
 */
class OdtArea
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $comment;

    /**
     * @var \Tisseo\EndivBundle\Entity\Waypoint
     */
    private $id;


    /**
     * Set name
     *
     * @param string $name
     * @return OdtArea
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
     * Set comment
     *
     * @param string $comment
     * @return OdtArea
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string 
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set id
     *
     * @param \Tisseo\EndivBundle\Entity\Waypoint $id
     * @return OdtArea
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
     * @var \Tisseo\EndivBundle\Entity\Waypoint
     */
    private $waypoint;


    /**
     * Set waypoint
     *
     * @param \Tisseo\EndivBundle\Entity\Waypoint $waypoint
     * @return OdtArea
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
