<?php

namespace Tisseo\EndivBundle\Entity\Ogive;

use Doctrine\ORM\Mapping as ORM;

/**
 * Object
 */
class Object
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
     * @var \Doctrine\Common\Collections\Collection
     */
    private $groupObject;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->groupObject = new \Doctrine\Common\Collections\ArrayCollection();
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
        $this->groupObject[] = $groupObject;

        return $this;
    }

    /**
     * Remove groupObject
     *
     * @param GroupObject $groupObject
     */
    public function removeGroupObject(GroupObject $groupObject)
    {
        $this->groupObject->removeElement($groupObject);
    }

    /**
     * Get groupObject
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getGroupObject()
    {
        return $this->groupObject;
    }
}
