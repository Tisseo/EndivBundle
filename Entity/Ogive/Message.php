<?php

namespace Tisseo\EndivBundle\Entity\Ogive;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Message
 */
class Message
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $subtitle;

    /**
     * @var string
     */
    private $content;

    /**
     * @var string
     */
    private $urlPj;

    /**
     * @var \DateTime
     */
    private $startDatetime;

    /**
     * @var \DateTime
     */
    private $endDatetime;

    /**
     * @var Collection
     */
    private $channels;

    /**
     * @var Event
     */
    private $event;

    /**
     * @var OgiveObject
     */
    private $object;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->channels = new ArrayCollection();
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
     * Set title
     *
     * @param  string $title
     * @return Message
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set subtitle
     *
     * @param  string $subtitle
     * @return Message
     */
    public function setSubtitle($subtitle)
    {
        $this->subtitle = $subtitle;

        return $this;
    }

    /**
     * Get subtitle
     *
     * @return string
     */
    public function getSubtitle()
    {
        return $this->subtitle;
    }

    /**
     * Set content
     *
     * @param  string $content
     * @return Message
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set startDatetime
     *
     * @param  \DateTime $startDatetime
     * @return Message
     */
    public function setStartDatetime($startDatetime)
    {
        $this->startDatetime = $startDatetime;

        return $this;
    }

    /**
     * Get startDatetime
     *
     * @return \DateTime
     */
    public function getStartDatetime()
    {
        return $this->startDatetime;
    }

    /**
     * Set endDatetime
     *
     * @param  \DateTime $endDatetime
     * @return Message
     */
    public function setEndDatetime($endDatetime)
    {
        $this->endDatetime = $endDatetime;

        return $this;
    }

    /**
     * Get endDatetime
     *
     * @return \DateTime
     */
    public function getEndDatetime()
    {
        return $this->endDatetime;
    }

    /**
     * Get urlPj
     *
     * @return string
     */
    public function getUrlPj()
    {
        return $this->urlPj;
    }

    /**
     * Set urlPj
     *
     * @param  string $urlPj
     * @return Message
     */
    public function setUrlPj($urlPj)
    {
        $this->urlPj = $urlPj;

        return $this;
    }

    /**
     * Add channel
     *
     * @param  Channel $channel
     * @return Message
     */
    public function addChannel(Channel $channel)
    {
        $this->channels->add($channel);

        return $this;
    }

    /**
     * Remove channel
     *
     * @param  Channel $channel
     * @return Message
     */
    public function removeChannel(Channel $channel)
    {
        $this->channels->removeElement($channel);

        return $this;
    }

    /**
     * Get channels
     *
     * @return Collection
     */
    public function getChannels()
    {
        return $this->channels;
    }

    /**
     * Get event
     *
     * @return Event.
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * Set event
     *
     * @param  Event event
     * @return Message
     */
    public function setEvent($event)
    {
        $this->event = $event;

        return $this;
    }

    /**
     * Get object
     *
     * @return Object.
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * Set object
     *
     * @param  Object object
     * @return Message
     */
    public function setObject($object)
    {
        $this->object = $object;

        return $this;
    }
}
