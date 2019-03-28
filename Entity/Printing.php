<?php

namespace Tisseo\EndivBundle\Entity;

/**
 * Printing
 */
class Printing
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $quantity;

    /**
     * @var \DateTime
     */
    private $date;

    /**
     * @var string
     */
    private $comment;

    /**
     * @var \Tisseo\EndivBundle\Entity\LineVersion
     */
    private $lineVersion;

    /**
     * @var \Tisseo\EndivBundle\Entity\PrintingType
     */
    private $printingType;

    private $format;

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
     * Set quantity
     *
     * @param int $quantity
     *
     * @return Printing
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Printing
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
     * Set comment
     *
     * @param string $comment
     *
     * @return Printing
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
     * Set lineVersion
     *
     * @param \Tisseo\EndivBundle\Entity\LineVersion $lineVersion
     *
     * @return Printing
     */
    public function setLineVersion(LineVersion $lineVersion = null)
    {
        $this->lineVersion = $lineVersion;

        return $this;
    }

    /**
     * Get lineVersion
     *
     * @return \Tisseo\EndivBundle\Entity\LineVersion
     */
    public function getLineVersion()
    {
        return $this->lineVersion;
    }

    /**
     * Set PrintingType
     *
     * @param \Tisseo\EndivBundle\Entity\PrintingType $printingType
     *
     * @return Printing
     */
    public function setPrintingType(PrintingType $printingType = null)
    {
        $this->printingType = $printingType;

        return $this;
    }

    /**
     * Get PrintingType
     *
     * @return \Tisseo\EndivBundle\Entity\PrintingType
     */
    public function getPrintingType()
    {
        return $this->printingType;
    }

    /**
     * Set Format
     *
     * @return Printing
     */
    public function setFormat($format = 1)
    {
        $this->format = $format;

        return $this;
    }

    /**
     * Get Format
     *
     * @return int
     */
    public function getFormat()
    {
        return $this->format;
    }
}
