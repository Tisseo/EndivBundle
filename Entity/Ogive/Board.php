<?php

namespace Tisseo\EndivBundle\Entity\Ogive;

use Doctrine\ORM\Mapping as ORM;

/**
 * Board
 */
class Board
{
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
     * @var string
     */
    private $status;

    /**
     * @var boolean
     */
    private $isOffice;

    /**
     * @var boolean
     */
    private $isWaitingRoom;


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
     * @param string $shortName
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
     * @param integer $nbBoards
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
     * @param string $status
     * @return Board
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set isOffice
     *
     * @param boolean $isOffice
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
     * @return boolean
     */
    public function getIsOffice()
    {
        return $this->isOffice;
    }

    /**
     * Set isWaitingRoom
     *
     * @param boolean $isWaitingRoom
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
     * @return boolean
     */
    public function getIsWaitingRoom()
    {
        return $this->isWaitingRoom;
    }
}
