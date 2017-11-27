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
     * @var int
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
     * @var int
     */
    private $nbBoards;

    /**
     * @var int
     */
    private $status;

    /**
     * @var bool
     */
    private $isOffice;

    /**
     * @var bool
     */
    private $isWaitingRoom;

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
     * Set shortName
     *
     * @param string $shortName
     *
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
     * @param string $longName
     *
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
     * @param int $nbBoards
     *
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
     * @return int
     */
    public function getNbBoards()
    {
        return $this->nbBoards;
    }

    /**
     * Set status
     *
     * @param int $status
     *
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
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set isOffice
     *
     * @param bool $isOffice
     *
     * @return Board
     */
    public function setIsOffice($isOffice)
    {
        $this->isOffice = $isOffice;

        return $this;
    }

    /**
     * Get isOffice
     *
     * @return bool
     */
    public function getIsOffice()
    {
        return $this->isOffice;
    }

    /**
     * Set isWaitingRoom
     *
     * @param bool $isWaitingRoom
     *
     * @return Board
     */
    public function setIsWaitingRoom($isWaitingRoom)
    {
        $this->isWaitingRoom = $isWaitingRoom;

        return $this;
    }

    /**
     * Get isWaitingRoom
     *
     * @return bool
     */
    public function getIsWaitingRoom()
    {
        return $this->isWaitingRoom;
    }
}
