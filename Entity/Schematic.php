<?php

namespace Tisseo\EndivBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Schematic
 */
class Schematic
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
    private $comment;

    /**
     * @var \DateTime
     */
    private $date;

    /**
     * @var string
     */
    private $filePath;

    /**
     * @var string
     */
    public $file;

    /**
     * @var bool
     */
    private $deprecated;

    /**
     * @var bool
     */
    private $groupGis;

    /**
     * @var \Tisseo\EndivBundle\Entity\Line
     */
    private $line;

    /**
     * @var Collection
     */
    private $lineVersions;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->lineVersions = new ArrayCollection();
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
     * @return Schematic
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
     * Set comment
     *
     * @param string $comment
     *
     * @return Schematic
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Schematic
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set filePath
     *
     * @param string $filePath
     *
     * @return Schematic
     */
    public function setFilePath($filePath)
    {
        $this->filePath = $filePath;

        return $this;
    }

    /**
     * Get filePath
     *
     * @return string
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    public function getAbsolutePath()
    {
        return null === $this->filePath ? null : $this->getUploadRootDir().'/'.$this->filePath;
    }

    public function getWebPath()
    {
        return null === $this->filePath ? null : $this->getUploadDir().'/'.$this->filePath;
    }

    protected function getUploadRootDir()
    {
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        return 'uploads/schematics';
    }

    public function upload()
    {
        if (null === $this->file) {
            return;
        }

        $this->file->move($this->getUploadRootDir(), $this->getFilePath());
        unset($this->file);
    }

    /**
     * Rename file
     */
    public function preUpload()
    {
        if (null !== $this->file) {
            $this->setFilePath(
                $this->getLine()->getNumber().
                '_'.date_format($this->getDate(), 'Ymd').
                '_'.sha1(uniqid(mt_rand(), true)).
                '.'.$this->file->guessExtension()
            );
        }
    }

    /**
     * Set line
     *
     * @param \Tisseo\EndivBundle\Entity\Line $line
     *
     * @return Schematic
     */
    public function setLine(\Tisseo\EndivBundle\Entity\Line $line = null)
    {
        $this->line = $line;

        return $this;
    }

    /**
     * Get line
     *
     * @return \Tisseo\EndivBundle\Entity\Line
     */
    public function getLine()
    {
        return $this->line;
    }

    /**
     * Set deprecated
     *
     * @param bool $deprecated
     *
     * @return Schematic
     */
    public function setDeprecated($deprecated)
    {
        $this->deprecated = $deprecated;

        return $this;
    }

    /**
     * Get deprecated
     *
     * @return bool
     */
    public function getDeprecated()
    {
        return $this->deprecated;
    }

    /**
     * Set groupGis
     *
     * @param bool $groupGis
     *
     * @return Schematic
     */
    public function setGroupGis($groupGis)
    {
        $this->groupGis = $groupGis;

        return $this;
    }

    /**
     * Get groupGis
     *
     * @return bool
     */
    public function getGroupGis()
    {
        return $this->groupGis;
    }

    /**
     * Get lineVersions
     *
     * @return Collection
     */
    public function getLineVersions()
    {
        return $this->lineVersions;
    }

    public function getDateString()
    {
        return $this->date->format('d/m/Y');
    }
}
