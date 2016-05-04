<?php

namespace Tisseo\EndivBundle\Entity\Ogive;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Object
 */
class Object extends OgiveEntity
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $objectType;

    /**
     * @var string
     */
    private $objectRef;

    /**
     * @var Collection
     */
    private $groupObject;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->groupObject = new ArrayCollection();
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
     * Set objectType
     *
     * @param string $objectType
     * @return Object
     */
    public function setObjectType($objectType)
    {
        $this->objectType = $objectType;

        return $this;
    }

    /**
     * Get objectType
     *
     * @return string
     */
    public function getObjectType()
    {
        return $this->objectType;
    }

    /**
     * Set objectRef
     *
     * @param string $objectRef
     * @return Object
     */
    public function setObjectRef($objectRef)
    {
        $this->objectRef = $objectRef;

        return $this;
    }

    /**
     * Get objectRef
     *
     * @return string
     */
    public function getObjectRef()
    {
        return $this->objectRef;
    }

    /**
     * Add groupObject
     *
     * @param GroupObject $groupObject
     * @return Object
     */
    public function addGroupObject(GroupObject $groupObject)
    {
        $this->groupObject->add($groupObject);

        return $this;
    }

    /**
     * Remove groupObject
     *
     * @param GroupObject $groupObject
     * @return Object
     */
    public function removeGroupObject(GroupObject $groupObject)
    {
        $this->groupObject->removeElement($groupObject);

        return $this;
    }

    /**
     * Get groupObject
     *
     * @return Collection
     */
    public function getGroupObject()
    {
        return $this->groupObject;
    }
}
