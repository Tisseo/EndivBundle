<?php

namespace Tisseo\EndivBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Schematic
 */
class Schematic
{
    /**
     * @var integer
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
     * @var boolean
     */
    private $deprecated;

    /**
     * @var boolean
     */
    private $groupGis;

    /**
     * @var \Tisseo\EndivBundle\Entity\Line
     */
    private $line;


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
     * Set name
     *
     * @param string $name
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
        return __DIR__.'/../../../../../../web/'.$this->getUploadDir();
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
                $this->getLine()->getNumber() .
                '_' . date_format($this->getDate(), 'Ymd') .
                '_' . sha1(uniqid(mt_rand(), true)) .
                '.' . $this->file->guessExtension()
            );
        }
    }

    /**
     * Set line
     *
     * @param \Tisseo\EndivBundle\Entity\Line $line
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
     * @param boolean $deprecated
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
     * @return boolean
     */
    public function getDeprecated()
    {
        return $this->deprecated;
    }

    /**
     * Set groupGis
     *
     * @param boolean $groupGis
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
     * @return boolean
     */
    public function getGroupGis()
    {
        return $this->groupGis;
    }
}
