<?php

namespace Tisseo\EndivBundle\Entity\Ogive;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * OgiveObject
 */
class OgiveObject extends OgiveEntity
{
    const AGENCY = 'agency';
    const LINE = 'line';
    const STOP = 'stop';
    const STOP_AREA = 'stop_area';

    /**
     * @var static array
     */
    public static $types = array(
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
    private $type;

    /**
     * @var string
     */
    private $ref;

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
     * Set type
     *
     * @param  string $type
     * @return Object
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set ref
     *
     * @param  string $ref
     * @return Object
     */
    public function setRef($ref)
    {
        $this->ref = $ref;

        return $this;
    }

    /**
     * Get ref
     *
     * @return string
     */
    public function getRef()
    {
        return $this->ref;
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
