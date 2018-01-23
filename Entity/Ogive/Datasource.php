<?php

namespace Tisseo\EndivBundle\Entity\Ogive;

/**
 * Datasource
 */
class Datasource extends OgiveEntity
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
    private $isEditable;

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
     * @return Datasource
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
     * Set isEditable
     *
     * @param bool $isEditable
     *
     * @return Datasource
     */
    public function setIsEditable($isEditable)
    {
        $this->isEditable = $isEditable;

        return $this;
    }

    /**
     * Get isEditable
     *
     * @return bool
     */
    public function getIsEditable()
    {
        return $this->isEditable;
    }
}
