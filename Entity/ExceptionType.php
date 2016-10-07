<?php

namespace Tisseo\EndivBundle\Entity;

/**
 * ExceptionType
 */
class ExceptionType
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $label;

    /**
     * @var string
     */
    private $exceptionText;

    /**
     * @var string
     */
    private $gridCalendarPattern;

    /**
     * @var string
     */
    private $tripCalendarPattern;


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
     * Set label
     *
     * @param  string $label
     * @return ExceptionType
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set exceptionText
     *
     * @param  string $exceptionText
     * @return ExceptionType
     */
    public function setExceptionText($exceptionText)
    {
        $this->exceptionText = $exceptionText;

        return $this;
    }

    /**
     * Get exceptionText
     *
     * @return string
     */
    public function getExceptionText()
    {
        return $this->exceptionText;
    }

    /**
     * Set gridCalendarPattern
     *
     * @param  string $gridCalendarPattern
     * @return ExceptionType
     */
    public function setGridCalendarPattern($gridCalendarPattern)
    {
        $this->gridCalendarPattern = $gridCalendarPattern;

        return $this;
    }

    /**
     * Get gridCalendarPattern
     *
     * @return string
     */
    public function getGridCalendarPattern()
    {
        return $this->gridCalendarPattern;
    }

    /**
     * Set tripCalendarPattern
     *
     * @param  string $tripCalendarPattern
     * @return ExceptionType
     */
    public function setTripCalendarPattern($tripCalendarPattern)
    {
        $this->tripCalendarPattern = $tripCalendarPattern;

        return $this;
    }

    /**
     * Get tripCalendarPattern
     *
     * @return string
     */
    public function getTripCalendarPattern()
    {
        return $this->tripCalendarPattern;
    }
}
