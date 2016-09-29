<?php

namespace Tisseo\EndivBundle\Entity;

/**
 * Modification
 */
class Modification
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var date
     */
    private $date;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $author;

    /**
     * @var LineVersion
     */
    private $lineVersion;

    /**
     * @var LineVersion
     */
    private $resolvedIn;

    public function __toString()
    {
        return sprintf("%s / %s / %s version %s", $this->description, $this->author, $this->date->format('d/m/Y'), $this->lineVersion->getVersion());
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
     * Set author
     *
     * @param  string $author
     * @return Modification
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set description
     *
     * @param  string $description
     * @return Modification
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set date
     *
     * @param  \DateTime $date
     * @return ModificationLink
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
     * Set lineVersion
     *
     * @param  LineVersion $lineVersion
     * @return ModificationLink
     */
    public function setLineVersion(LineVersion $lineVersion = null)
    {
        $this->lineVersion = $lineVersion;

        return $this;
    }

    /**
     * Get lineVersion
     *
     * @return LineVersion
     */
    public function getLineVersion()
    {
        return $this->lineVersion;
    }

    /**
     * Set resolvedIn
     *
     * @param  LineVersion $resolvedIn
     * @return ModificationLink
     */
    public function setResolvedIn(LineVersion $resolvedIn = null)
    {
        $this->resolvedIn = $resolvedIn;

        return $this;
    }

    /**
     * Get resolvedIn
     *
     * @return LineVersion
     */
    public function getResolvedIn()
    {
        return $this->resolvedIn;
    }
}
