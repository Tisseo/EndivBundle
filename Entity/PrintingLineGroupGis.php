<?php

namespace Tisseo\EndivBundle\Entity;

/**
 * Printing
 */
class PrintingLineGroupGis
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
     * @var \Tisseo\EndivBundle\Entity\LineGroupGis
     */
    private $lineGroupGis;

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
     * Set lineGroupGis
     *
     * @param \Tisseo\EndivBundle\Entity\LineGroupGis $lineGroupGis
     *
     * @return PrintingLineGroupGis
     */
    public function setLineGroupGis(LineGroupGis $lineGroupGis = null)
    {
        $this->lineGroupGis = $lineGroupGis;

        return $this;
    }

    /**
     * Get lineGroupGis
     *
     * @return \Tisseo\EndivBundle\Entity\LineGroupGis
     */
    public function getLineGroupGis()
    {
        return $this->lineGroupGis;
    }
}
