<?php

namespace Tisseo\EndivBundle\Entity;

/**
 * LineVersionProperty
 */
class LineVersionProperty
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var \Tisseo\EndivBundle\Entity\LineVersion
     */
    private $lineVersion;

    /**
     * @var \Tisseo\EndivBundle\Entity\Property
     */
    private $property;

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
     * Set lineVersion
     *
     * @param \Tisseo\EndivBundle\Entity\LineVersion $lineVersion
     *
     * @return LineVersionProperty
     */
    public function setLineVersion(\Tisseo\EndivBundle\Entity\LineVersion $lineVersion = null)
    {
        $this->lineVersion = $lineVersion;

        return $this;
    }

    /**
     * Get lineVersion
     *
     * @return \Tisseo\EndivBundle\Entity\LineVersion
     */
    public function getLineVersion()
    {
        return $this->lineVersion;
    }

    /**
     * Set property
     *
     * @param \Tisseo\EndivBundle\Entity\Property $property
     *
     * @return LineVersionProperty
     */
    public function setProperty(\Tisseo\EndivBundle\Entity\Property $property = null)
    {
        $this->property = $property;

        return $this;
    }

    /**
     * Get property
     *
     * @return \Tisseo\EndivBundle\Entity\Property
     */
    public function getProperty()
    {
        return $this->property;
    }

    /**
     * @var int
     */
    private $value;

    /**
     * Set value
     *
     * @param int $value
     *
     * @return LineVersionProperty
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return int
     */
    public function getValue()
    {
        return $this->value;
    }
}
