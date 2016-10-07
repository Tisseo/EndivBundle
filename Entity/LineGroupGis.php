<?php

namespace Tisseo\EndivBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * LineGroupGis
 */
class LineGroupGis
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
     * @var integer
     */
    private $nbBus;

    /**
     * @var string
     */
    private $comment;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $lines;

    /**
     * @var Collection
     */
    private $printings;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->lines = new ArrayCollection();
        $this->printings = new ArrayCollection();
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
     * Set name
     *
     * @param  string $name
     * @return LineGroupGis
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
     * Add line
     *
     * @param  \Tisseo\EndivBundle\Entity\Line $line
     * @return LineGroupGis
     */
    public function addLine(Line $line)
    {
        $this->lines->add($line);
        
        return $this;
    }

    /**
     * Remove line
     *
     * @param \Tisseo\EndivBundle\Entity\Line $line
     */
    public function removeLine(Line $line)
    {
        $this->lines->removeElement($line);
    }

    /**
     * Get lines
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLines()
    {
        return $this->lines;
    }

    public function setLines(Collection $lines)
    {
        $this->lines = $lines;
    }

    public function clearLines()
    {
        $this->lines->clear();
    }

    /**
     * Set nbBus
     *
     * @param  string $nbBus
     * @return LineGroupGis
     */
    public function setNbBus($nbBus)
    {
        $this->nbBus = $nbBus;

        return $this;
    }

    /**
     * Get nbBus
     *
     * @return string
     */
    public function getNbBus()
    {
        return $this->nbBus;
    }

    /**
     * Set comment
     *
     * @param  string $comment
     * @return LineGroupGis
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
     * Calculating the total amount of printings (i.e. printing.quantity)
     *
     * @return integer
     */
    public function getTotalPrintings()
    {
        $amount = 0;
        foreach ($this->printings as $printing) {
            $amount += $printing->getQuantity();
        }

        return $amount;
    }

    /**
     * Set printings
     *
     * @param  Collection $printings
     * @return LineGroupGis
     */
    public function setPrintings(Collection $printings)
    {
        $this->printings = $printings;
        foreach ($this->printings as $printing) {
            $printing->setLineGroupGis($this);
        }
        return $this;
    }

    /**
     * Get printings
     *
     * @return Collection
     */
    public function getPrintings()
    {
        return $this->printings;
    }

    /**
     * Add printings
     *
     * @param  Printing $printing
     * @return LineGroupGis
     */
    public function addPrintings(Printing $printing)
    {
        $this->printings[] = $printing;
        $printing->setLineGroupGis($this);
        return $this;
    }

    /**
     * Remove printings
     *
     * @param Printing $printing
     */
    public function removePrintings(Printing $printing)
    {
        $this->printings->removeElement($printing);
    }

    /**
     * Clear printings
     *
     * @return LineVersion
     */
    public function clearPrintings()
    {
        $this->printings->clear();

        return $this;
    }
}
