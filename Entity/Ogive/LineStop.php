<?php

namespace Tisseo\EndivBundle\Entity\Ogive;


/**
 * LineStop
 */
class LineStop
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $stopId;

    /**
     * @var integer
     */
    private $lineId;

    /**
     * @var string
     */
    private $directionName;


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
     * Set stopId
     *
     * @param  integer $stopId
     * @return LineStop
     */
    public function setStopId($stopId)
    {
        $this->stopId = $stopId;

        return $this;
    }

    /**
     * Get stopId
     *
     * @return integer
     */
    public function getStopId()
    {
        return $this->stopId;
    }

    /**
     * Set lineId
     *
     * @param  integer $lineId
     * @return LineStop
     */
    public function setLineId($lineId)
    {
        $this->lineId = $lineId;

        return $this;
    }

    /**
     * Get lineId
     *
     * @return integer
     */
    public function getLineId()
    {
        return $this->lineId;
    }

    /**
     * Set directionName
     *
     * @param  string $directionName
     * @return LineStop
     */
    public function setDirectionName($directionName)
    {
        $this->directionName = $directionName;

        return $this;
    }

    /**
     * Get directionName
     *
     * @return string
     */
    public function getDirectionName()
    {
        return $this->directionName;
    }
}
