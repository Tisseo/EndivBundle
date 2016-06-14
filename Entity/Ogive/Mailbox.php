<?php

namespace Tisseo\EndivBundle\Entity\Ogive;

use Doctrine\ORM\Mapping as ORM;

/**
 * Mailbox
 */
class Mailbox
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
    private $mailText;

    /**
     * @var boolean
     */
    private $isForWebsite;

    /**
     * @var boolean
     */
    private $isForPti;

    /**
     * @var boolean
     */
    private $pushApps;

    /**
     * @var boolean
     */
    private $prehome;

    /**
     * @var string
     */
    private $urlPj;

    /**
     * @var integer
     */
    private $eventId;

    /**
     * @var \DateTime
     */
    private $startDatetime;

    /**
     * @var \DateTime
     */
    private $endDatetime;


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
     * @param string $title
     * @return Mailbox
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
     * @param string $subtitle
     * @return Mailbox
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
     * Set mailText
     *
     * @param string $mailText
     * @return Mailbox
     */
    public function setMailText($mailText)
    {
        $this->mailText = $mailText;

        return $this;
    }

    /**
     * Get mailText
     *
     * @return string
     */
    public function getMailText()
    {
        return $this->mailText;
    }

    /**
     * Set isForWebsite
     *
     * @param boolean $isForWebsite
     * @return Mailbox
     */
    public function setIsForWebsite($isForWebsite)
    {
        $this->isForWebsite = $isForWebsite;

        return $this;
    }

    /**
     * Get isForWebsite
     *
     * @return boolean
     */
    public function getIsForWebsite()
    {
        return $this->isForWebsite;
    }

    /**
     * Set isForPti
     *
     * @param boolean $isForPti
     * @return Mailbox
     */
    public function setIsForPti($isForPti)
    {
        $this->isForPti = $isForPti;

        return $this;
    }

    /**
     * Get isForPti
     *
     * @return boolean
     */
    public function getIsForPti()
    {
        return $this->isForPti;
    }

    /**
     * Set eventId
     *
     * @param integer $eventId
     * @return Mailbox
     */
    public function setEventId($eventId)
    {
        $this->eventId = $eventId;

        return $this;
    }

    /**
     * Get eventId
     *
     * @return integer
     */
    public function getEventId()
    {
        return $this->eventId;
    }

    /**
     * Set startDatetime
     *
     * @param \DateTime $startDatetime
     * @return Mailbox
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
     * @param \DateTime $endDatetime
     * @return Mailbox
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
     * @param string $urlPj
     * @return Mailbox
     */
    public function setUrlPj($urlPj)
    {
        $this->urlPj = $urlPj;

        return $this;
    }

    /**
     * Get prehome
     *
     * @return boolean
     */
    public function getPrehome()
    {
        return $this->prehome;
    }

    /**
     * Set prehome
     *
     * @param boolean $prehome
     * @return Mailbox
     */
    public function setPrehome($prehome)
    {
        $this->prehome = $prehome;

        return $this;
    }

    /**
     * Get pushApps
     *
     * @return boolean pushApps
     */
    public function getPushApps()
    {
        return $this->pushApps;
    }

    /**
     * Set pushApps
     *
     * @param boolean pushApps
     * @return Mailbox
     */
    public function setPushApps($pushApps)
    {
        $this->pushApps = $pushApps;

        return $this;
    }
}
