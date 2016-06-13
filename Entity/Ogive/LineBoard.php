<?php

namespace Tisseo\EndivBundle\Entity\Ogive;

use Doctrine\ORM\Mapping as ORM;

/**
 * LineBoard
 */
class LineBoard
{
    /**
     * @var integer
     */
    private $lineId;

    /**
     * @var Board
     */
    private $board;


    /**
     * Set lineId
     *
     * @param integer $lineId
     * @return LineBoard
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
     * Set board
     *
     * @param Board $board
     * @return LineBoard
     */
    public function setBoard(Board $board = null)
    {
        $this->board = $board;

        return $this;
    }

    /**
     * Get board
     *
     * @return Board
     */
    public function getBoard()
    {
        return $this->board;
    }
}
