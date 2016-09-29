<?php

namespace Tisseo\EndivBundle\Entity;


/**
 * Log
 */
class Log
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $datetime;

    /**
     * @var string
     */
    private $table;

    /**
     * @var string
     */
    private $action;

    /**
     * @var string
     */
    private $previousData;

    /**
     * @var string
     */
    private $insertedData;

    /**
     * @var string
     */
    private $user;


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
     * Set datetime
     *
     * @param  \DateTime $datetime
     * @return Log
     */
    public function setDatetime($datetime)
    {
        $this->datetime = $datetime;

        return $this;
    }

    /**
     * Get datetime
     *
     * @return \DateTime
     */
    public function getDatetime()
    {
        return $this->datetime;
    }

    /**
     * Set table
     *
     * @param  string $table
     * @return Log
     */
    public function setTable($table)
    {
        $this->table = $table;

        return $this;
    }

    /**
     * Get table
     *
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * Set action
     *
     * @param  string $action
     * @return Log
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Get action
     *
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Set previousData
     *
     * @param  string $previousData
     * @return Log
     */
    public function setPreviousData($previousData)
    {
        $this->previousData = $previousData;

        return $this;
    }

    /**
     * Get previousData
     *
     * @return string
     */
    public function getPreviousData()
    {
        return $this->previousData;
    }

    /**
     * Set insertedData
     *
     * @param  string $insertedData
     * @return Log
     */
    public function setInsertedData($insertedData)
    {
        $this->insertedData = $insertedData;

        return $this;
    }

    /**
     * Get insertedData
     *
     * @return string
     */
    public function getInsertedData()
    {
        return $this->insertedData;
    }

    /**
     * Set user
     *
     * @param  string $user
     * @return Log
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }
}
