<?php

namespace Tisseo\EndivBundle\Entity\Ogive;

/**
 * GroupObject
 */
class GroupObject
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
     * @var string
     */
    private $groupType;

    /**
     * @var bool
     */
    private $isPrivate;

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
     * @param string $groupType
     *
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
     * Set isPrivate
     *
     * @param bool $isPrivate
     *
     * @return GroupObject
     */
    public function setIsPrivate($isPrivate)
    {
        $this->isPrivate = $isPrivate;

        return $this;
    }

    /**
     * Get isPrivate
     *
     * @return bool
     */
    public function getIsPrivate()
    {
        return $this->isPrivate;
    }

    /**
     * Add objects
     *
     * @param object $objects
     *
     * @return GroupObject
     */
    public function addObject(Object $objects)
    {
        $this->objects[] = $objects;

        return $this;
    }

    /**
     * Remove objects
     *
     * @param object $objects
     */
    public function removeObject(Object $objects)
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
