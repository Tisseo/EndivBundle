<?php

namespace Tisseo\EndivBundle\Entity\Ogive;

class EventStepConnectorMessage extends OgiveEntity
{
    private $publishPreHome;
    private $pushMobileApp;
    private $startPublicationDate;
    private $endPublicationDate;
    private $attachment;


    /**
     * @return boolean
     */
    public function getPublishPreHome()
    {
        return $this->publishPreHome;
    }

    /**
     * @param boolean $publishPreHome
     */
    public function setPublishPreHome($publishPreHome)
    {
        $this->publishPreHome = $publishPreHome;
    }

    /**
     * @return boolean
     */
    public function getPushMobileApp()
    {
        return $this->pushMobileApp;
    }

    /**
     * @param boolean $pushMobileApp
     */
    public function setPushMobileApp($pushMobileApp)
    {
        $this->pushMobileApp = $pushMobileApp;
    }

    /**
     * @return string
     */
    public function getStartPublicationDate()
    {
        return $this->startPublicationDate;
    }

    /**
     * @param string $startPublicationDate
     */
    public function setStartPublicationDate($startPublicationDate)
    {
        $this->startPublicationDate = $startPublicationDate;
    }

    /**
     * @return string
     */
    public function getEndPublicationDate()
    {
        return $this->endPublicationDate;
    }

    /**
     * @param string $endPublicationDate
     */
    public function setEndPublicationDate($endPublicationDate)
    {
        $this->endPublicationDate = $endPublicationDate;
    }

    /**
     * @return mixed
     */
    public function getAttachment()
    {
        return $this->attachment;
    }

    /**
     * @param mixed $attachment
     */
    public function setAttachment($attachment)
    {
        $this->attachment = $attachment;
    }


}