<?php

namespace Tisseo\EndivBundle\Entity;

/**
 * LineStatus
 */
class LineStatus
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $dateTime;

    /**
     * @var string
     */
    private $login;

    /**
     * @var int
     */
    private $status;

    /**
     * @var Line
     */
    private $line;

    /**
     * @var string
     */
    private $comment;

    public function __toString()
    {
        return sprintf('%s / %s / %s / %s version %s', $this->login, $this->status, $this->comment, $this->dateTime->format('d/m/Y'), $this->line->getId());
    }

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
     * Set login
     *
     * @param string $login
     *
     * @return LineStatus
     */
    public function setLogin($login)
    {
        $this->login = $login;

        return $this;
    }

    /**
     * Get login
     *
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * Set comment
     *
     * @param string $comment
     *
     * @return LineStatus
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
     * Set status
     *
     * @param int $status
     *
     * @return LineStatus
     */
    public function setStatus($status)
    {
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
     * Set dateTime
     *
     * @param \DateTime $dateTime
     *
     * @return LineStatus
     */
    public function setDateTime($dateTime)
    {
        $this->dateTime = $dateTime;

        return $this;
    }

    /**
     * Get dateTime
     *
     * @return \DateTime
     */
    public function getDateTime()
    {
        return $this->dateTime;
    }

    /**
     * Set line
     *
     * @param Line $line
     *
     * @return LineStatus
     */
    public function setLine(Line $line = null)
    {
        $this->line = $line;

        return $this;
    }

    /**
     * Get line
     *
     * @return Line
     */
    public function getLine()
    {
        return $this->line;
    }
}
