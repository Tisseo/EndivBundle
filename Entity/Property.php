<?php

namespace Tisseo\EndivBundle\Entity;

/**
 * Property
 */
class Property
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var bool
     */
    private $is_default;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $lineVersionProperties;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->lineVersionProperties = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * Set name
     *
     * @param string $name
     *
     * @return Property
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
     * Set default
     *
     * @param int $default
     *
     * @return Property
     */
    public function setDefault($default)
    {
        $this->is_default = $default;

        return $this;
    }

    /**
     * Get default
     *
     * @return int
     */
    public function getDefault()
    {
        return $this->is_default;
    }

    /**
     * Add lineVersionProperties
     *
     * @param \Tisseo\EndivBundle\Entity\LineVersionProperty $lineVersionProperties
     *
     * @return Property
     */
    public function addLineVersionProperty(\Tisseo\EndivBundle\Entity\LineVersionProperty $lineVersionProperties)
    {
        $this->lineVersionProperties[] = $lineVersionProperties;

        return $this;
    }

    /**
     * Remove lineVersionProperties
     *
     * @param \Tisseo\EndivBundle\Entity\LineVersionProperty $lineVersionProperties
     */
    public function removeLineVersionProperty(\Tisseo\EndivBundle\Entity\LineVersionProperty $lineVersionProperties)
    {
        $this->lineVersionProperties->removeElement($lineVersionProperties);
    }

    /**
     * Get lineVersionProperties
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLineVersionProperties()
    {
        return $this->lineVersionProperties;
    }
}
