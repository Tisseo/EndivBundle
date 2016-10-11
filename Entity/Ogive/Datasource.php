<?php

namespace Tisseo\EndivBundle\Entity\Ogive;

/**
 * Datasource
 */
class Datasource extends OgiveEntity
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
     * @var boolean
     */
    private $editable;


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
     * Set editable
     *
     * @param  boolean $editable
     * @return Datasource
     */
    public function setEditable($editable)
    {
        $this->editable = $editable;

        return $this;
    }

    /**
     * Is editable
     *
     * @return boolean
     */
    public function isEditable()
    {
        return $this->editable;
    }
}
