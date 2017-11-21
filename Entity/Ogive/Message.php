<?php
namespace Tisseo\EndivBundle\Entity\Ogive;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;

/**
 * Message
 */
class Message extends OgiveEntity
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
     * @var \DateTime
     */
    private $startDatetime;

    /**
     * @var \DateTime
     */
    private $endDatetime;

    /**
     * @var \DateTime
     */
    private $modificationDatetime;

    /**
     * @var boolean
     */
    private $prehome;

    /**
     * @var boolean
     */
    private $home;

    /**
     * @var integer
     */
    private $priority;

    /**
     * @var push
     */
    private $push;

    /**
     * @var Collection
     */
    private $files;

    /**
     * @var Event
     */
    private $event;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->files = new ArrayCollection();
        $this->modificationDatetime = new \Datetime();
        $this->prehome = false;
        $this->push = false;
        $this->home = false;
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
     * @param string $title
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
     * @param string $subtitle
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
     * @param string $content
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
     * @param \DateTime $startDatetime
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
     * @param \DateTime $endDatetime
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
     * Get modificationDatetime
     *
     * @return \Datetime
     */
    public function getModificationDatetime()
    {
        return $this->modificationDatetime;
    }

    /**
     * Set modificationDatetime
     *
     * @param \Datetime $modificationDatetime
     * @return Message
     */
    public function setModificationDatetime($modificationDatetime)
    {
        $this->modificationDatetime = $modificationDatetime;

        return $this;
    }

    /**
     * Add file
     *
     * @param  MessageFile $file
     * @return Message
     */
    public function addFile(MessageFile $file)
    {
        if (!$this->files->contains($file)) {
            $this->files->add($file);
        }

        return $this;
    }

    /**
     * Remove file
     *
     * @param  MessageFile $file
     * @return Message
     */
    public function removeFile(MessageFile $file)
    {
        $this->files->removeElement($file);

        return $this;
    }

    /**
     * Get files
     *
     * @return Collection
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * Set files
     *
     * @param  Collection $files
     * @return Message
     */
    public function setFiles($files)
    {
        if ($files instanceof Collection) {
            $this->files = $files;
        } else if (is_array($files)) {
            $this->files = new ArrayCollection($files);
        }

        return $this;
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
     * @param Event event
     * @return Message
     */
    public function setEvent($event)
    {
        $this->event = $event;

        return $this;
    }

    /**
     * Get prehome
     *
     * @return boolean
     */
    public function isPrehome()
    {
        return $this->prehome;
    }

    /**
     * Set prehome
     *
     * @param  boolean $prehome
     * @return Message
     */
    public function setPrehome($prehome)
    {
        $this->prehome = $prehome;

        return $this;
    }

    /**
     * Get home
     *
     * @return boolean
     */
    public function isHome()
    {
        return $this->home;
    }

    /**
     * Set home
     *
     * @param  boolean $home
     * @return Message
     */
    public function setHome($home)
    {
        $this->home = $home;

        return $this;
    }

    /**
     * Get priority
     *
     * @return integer
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Set priority
     *
     * @param  integer $priority
     * @return Message
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Is push
     *
     * @return boolean
     */
    public function isPush()
    {
        return $this->push;
    }

    /**
     * Set push
     *
     * @param  boolean push
     * @return Message
     */
    public function setPush($push)
    {
        $this->push = $push;

        return $this;
    }
}
