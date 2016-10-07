<?php

namespace Tisseo\EndivBundle\Entity\Ogive;

/**
 * Channel
 */
class Channel
{
    const CHANNEL_NAME_INFO_WEB = "info_web";
    const CHANNEL_NAME_PUSH_APPS = "push_apps";
    const CHANNEL_NAME_PRE_HOME = "pre_home";

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var integer
     */
    private $maxSize;

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
     * Set name
     *
     * @param  string $name
     * @return Channel
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
     * Set maxSize
     *
     * @param  integer $maxSize
     * @return Channel
     */
    public function setMaxSize($maxSize)
    {
        $this->maxSize = $maxSize;

        return $this;
    }

    /**
     * Get maxSize
     *
     * @return integer
     */
    public function getMaxSize()
    {
        return $this->maxSize;
    }
}
