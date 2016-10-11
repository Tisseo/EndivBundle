<?php

namespace Tisseo\EndivBundle\Entity\Ogive;

/**
 * Board
 */
class Board extends OgiveEntity
{
    const OPEN = 1;
    const CLOSED = 2;
    const DEFINITIVELY_CLOSED = 3;

    /**
     * @var static array
     */
    public static $statuses = array(
        self::OPEN => self::OPEN,
        self::CLOSED => self::CLOSED,
        self::DEFINITIVELY_CLOSED => self::DEFINITIVELY_CLOSED
    );

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $shortName;

    /**
     * @var string
     */
    private $longName;

    /**
     * @var integer
     */
    private $nbBoards;

    /**
     * @var integer
     */
    private $status;

    /**
     * @var boolean
     */
    private $office;

    /**
     * @var boolean
     */
    private $waitingRoom;


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
     * Set shortName
     *
     * @param  string $shortName
     * @return Board
     */
    public function setShortName($shortName)
    {
        $this->shortName = $shortName;

        return $this;
    }

    /**
     * Get shortName
     *
     * @return string
     */
    public function getShortName()
    {
        return $this->shortName;
    }

    /**
     * Set longName
     *
     * @param  string $longName
     * @return Board
     */
    public function setLongName($longName)
    {
        $this->longName = $longName;

        return $this;
    }

    /**
     * Get longName
     *
     * @return string
     */
    public function getLongName()
    {
        return $this->longName;
    }

    /**
     * Set nbBoards
     *
     * @param  integer $nbBoards
     * @return Board
     */
    public function setNbBoards($nbBoards)
    {
        $this->nbBoards = $nbBoards;

        return $this;
    }

    /**
     * Get nbBoards
     *
     * @return integer
     */
    public function getNbBoards()
    {
        return $this->nbBoards;
    }

    /**
     * Set status
     *
     * @param  integer $status
     * @return Board
     */
    public function setStatus($status)
    {
        if (!in_array($status, self::$statuses)) {
            throw new \Exception(sprintf('Status %s does not exist', $status));
        }

        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set office
     *
     * @param  boolean $office
     * @return Board
     */
    public function setOffice($office)
    {
        $this->office = $office;

        return $this;
    }

    /**
     * Is office
     *
     * @return boolean
     */
    public function isOffice()
    {
        return $this->office;
    }

    /**
     * Set waitingRoom
     *
     * @param  boolean $waitingRoom
     * @return Board
     */
    public function setWaitingRoom($waitingRoom)
    {
        $this->waitingRoom = $waitingRoom;

        return $this;
    }

    /**
     * Is waitingRoom
     *
     * @return boolean
     */
    public function isWaitingRoom()
    {
        return $this->waitingRoom;
    }
}
