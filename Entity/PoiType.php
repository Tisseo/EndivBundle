<?php

namespace Tisseo\EndivBundle\Entity;

/**
 * PoiType
 */
class PoiType
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $long_name;


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
     * @return PoiType
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
     * @return string
     */
    public function getLongName()
    {
        return $this->long_name;
    }

    /**
     * @param string $long_name
     */
    public function setLongName($long_name)
    {
        $this->long_name = $long_name;
    }
}
