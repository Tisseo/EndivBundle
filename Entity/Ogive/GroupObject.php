<?php

namespace Tisseo\EndivBundle\Entity\Ogive;

/**
 * GroupObject
 */
class GroupObject
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
    private $groupType;

    /**
     * @var boolean
     */
    private $private;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $objects;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->objects = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * @return GroupObject
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
     * Set groupType
     *
     * @param  string $groupType
     * @return GroupObject
     */
    public function setGroupType($groupType)
    {
        $this->groupType = $groupType;

        return $this;
    }

    /**
     * Get groupType
     *
     * @return string
     */
    public function getGroupType()
    {
        return $this->groupType;
    }

    /**
     * Set private
     *
     * @param  boolean $private
     * @return GroupObject
     */
    public function setPrivate($private)
    {
        $this->private = $private;

        return $this;
    }

    /**
     * Is private
     *
     * @return boolean
     */
    public function isPrivate()
    {
        return $this->private;
    }

    /**
     * Add objects
     *
     * @param  OgiveObject $objects
     * @return GroupObject
     */
    public function addObject(OgiveObject $objects)
    {
        $this->objects[] = $objects;

        return $this;
    }

    /**
     * Remove objects
     *
     * @param OgiveObject $objects
     */
    public function removeObject(OgiveObject $objects)
    {
        $this->objects->removeElement($objects);
    }

    /**
     * Get objects
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getObject()
    {
        return $this->objects;
    }
}
