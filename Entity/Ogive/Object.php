<?php

namespace Tisseo\EndivBundle\Entity\Ogive;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Object
 */
class Object extends OgiveEntity
{
    const AGENCY = 'agency';
    const LINE = 'line';
    const STOP = 'stop';
    const STOP_AREA = 'stop_area';

    /**
     * @var static array
     */
    public static $objectTypes = array(
        self::AGENCY,
        self::LINE,
        self::STOP
    );

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
     * @var Collection
     */
    private $messages;

    /**
     * @var stdclass (unmapped)
     * References the meta info of the ENDIV object
     */
    // TODO: using inheritance here and bringing the name automatically could be great
    private $_meta;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->groupObject = new ArrayCollection();
        $this->messages = new ArrayCollection();
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
     * @param  string $objectType
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
     * @param  string $objectRef
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
     * @param  GroupObject $groupObject
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
     * @param  GroupObject $groupObject
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

    /**
     * Get meta
     *
     * @return stdclass
     */
    public function getMeta()
    {
        return $this->_meta;
    }

    /**
     * Set meta
     *
     * @param stdclass $meta
     */
    public function setMeta(\stdclass $meta)
    {
        $this->_meta = $meta;
    }

    /**
     * Add message
     *
     * @param  Message $message
     * @return Message
     */
    public function addMessage(Message $message)
    {
        $this->message->add($message);

        return $this;
    }

    /**
     * Remove message
     *
     * @param  Message $message
     * @return Message
     */
    public function removeMessage(Message $message)
    {
        $this->message->removeElement($message);

        return $this;
    }

    /**
     * Get messages
     *
     * @return Collection
     */
    public function getMessages()
    {
        return $this->messages;
    }
}
