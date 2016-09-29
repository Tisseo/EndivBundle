<?php

namespace Tisseo\EndivBundle\Entity;


/**
 * NonConcurrency
 */
class NonConcurrency
{
    /**
     * @var integer
     */
    private $time;

    /**
     * @var \Tisseo\EndivBundle\Entity\Line
     */
    private $priorityLine;

    /**
     * @var \Tisseo\EndivBundle\Entity\Line
     */
    private $nonPriorityLine;


    public function getId()
    {
        return (string)($this->priorityLine->getId())."/".(string)($this->nonPriorityLine->getId());
    }

    /**
     * Set time
     *
     * @param  integer $time
     * @return NonConcurrency
     */
    public function setTime($time)
    {
        $this->time = $time;

        return $this;
    }

    /**
     * Get time
     *
     * @return integer
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * Set priorityLine
     *
     * @param  \Tisseo\EndivBundle\Entity\Line $priorityLine
     * @return NonConcurrency
     */
    public function setPriorityLine(\Tisseo\EndivBundle\Entity\Line $priorityLine)
    {
        $this->priorityLine = $priorityLine;

        return $this;
    }

    /**
     * Get priorityLine
     *
     * @return \Tisseo\EndivBundle\Entity\Line
     */
    public function getPriorityLine()
    {
        return $this->priorityLine;
    }

    /**
     * Set nonPriorityLine
     *
     * @param  \Tisseo\EndivBundle\Entity\Line $nonPriorityLine
     * @return NonConcurrency
     */
    public function setNonPriorityLine(\Tisseo\EndivBundle\Entity\Line $nonPriorityLine)
    {
        $this->nonPriorityLine = $nonPriorityLine;

        return $this;
    }

    /**
     * Get nonPriorityLine
     *
     * @return \Tisseo\EndivBundle\Entity\Line
     */
    public function getNonPriorityLine()
    {
        return $this->nonPriorityLine;
    }
}
