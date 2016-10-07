<?php

namespace Tisseo\EndivBundle\Entity;

/**
 * Color
 */
class Color
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
     * @var string
     */
    private $html;

    /**
     * @var string
     */
    private $pantoneOc;

    /**
     * @var string
     */
    private $hoxis;

    /**
     * @var integer
     */
    private $cmykCyan;

    /**
     * @var integer
     */
    private $cmykMagenta;

    /**
     * @var integer
     */
    private $cmykYellow;

    /**
     * @var integer
     */
    private $cmykBlack;

    /**
     * @var integer
     */
    private $rgbRed;

    /**
     * @var integer
     */
    private $rgbGreen;

    /**
     * @var integer
     */
    private $rgbBlue;


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
     * @return Color
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
     * Set html
     *
     * @param  string $html
     * @return Color
     */
    public function setHtml($html)
    {
        $this->html = $html;

        return $this;
    }

    /**
     * Get html
     *
     * @return string
     */
    public function getHtml()
    {
        return $this->html;
    }

    /**
     * Set pantoneOc
     *
     * @param  string $pantoneOc
     * @return Color
     */
    public function setPantoneOc($pantoneOc)
    {
        $this->pantoneOc = $pantoneOc;

        return $this;
    }

    /**
     * Get pantoneOc
     *
     * @return string
     */
    public function getPantoneOc()
    {
        return $this->pantoneOc;
    }

    /**
     * Set hoxis
     *
     * @param  string $hoxis
     * @return Color
     */
    public function setHoxis($hoxis)
    {
        $this->hoxis = $hoxis;

        return $this;
    }

    /**
     * Get hoxis
     *
     * @return string
     */
    public function getHoxis()
    {
        return $this->hoxis;
    }

    /**
     * Set cmykCyan
     *
     * @param  integer $cmykCyan
     * @return Color
     */
    public function setCmykCyan($cmykCyan)
    {
        $this->cmykCyan = $cmykCyan;

        return $this;
    }

    /**
     * Get cmykCyan
     *
     * @return integer
     */
    public function getCmykCyan()
    {
        return $this->cmykCyan;
    }

    /**
     * Set cmykMagenta
     *
     * @param  integer $cmykMagenta
     * @return Color
     */
    public function setCmykMagenta($cmykMagenta)
    {
        $this->cmykMagenta = $cmykMagenta;

        return $this;
    }

    /**
     * Get cmykMagenta
     *
     * @return integer
     */
    public function getCmykMagenta()
    {
        return $this->cmykMagenta;
    }

    /**
     * Set cmykYellow
     *
     * @param  integer $cmykYellow
     * @return Color
     */
    public function setCmykYellow($cmykYellow)
    {
        $this->cmykYellow = $cmykYellow;

        return $this;
    }

    /**
     * Get cmykYellow
     *
     * @return integer
     */
    public function getCmykYellow()
    {
        return $this->cmykYellow;
    }

    /**
     * Set cmykBlack
     *
     * @param  integer $cmykBlack
     * @return Color
     */
    public function setCmykBlack($cmykBlack)
    {
        $this->cmykBlack = $cmykBlack;

        return $this;
    }

    /**
     * Get cmykBlack
     *
     * @return integer
     */
    public function getCmykBlack()
    {
        return $this->cmykBlack;
    }

    /**
     * Set rgbRed
     *
     * @param  integer $rgbRed
     * @return Color
     */
    public function setRgbRed($rgbRed)
    {
        $this->rgbRed = $rgbRed;

        return $this;
    }

    /**
     * Get rgbRed
     *
     * @return integer
     */
    public function getRgbRed()
    {
        return $this->rgbRed;
    }

    /**
     * Set rgbGreen
     *
     * @param  integer $rgbGreen
     * @return Color
     */
    public function setRgbGreen($rgbGreen)
    {
        $this->rgbGreen = $rgbGreen;

        return $this;
    }

    /**
     * Get rgbGreen
     *
     * @return integer
     */
    public function getRgbGreen()
    {
        return $this->rgbGreen;
    }

    /**
     * Set rgbBlue
     *
     * @param  integer $rgbBlue
     * @return Color
     */
    public function setRgbBlue($rgbBlue)
    {
        $this->rgbBlue = $rgbBlue;

        return $this;
    }

    /**
     * Get rgbBlue
     *
     * @return integer
     */
    public function getRgbBlue()
    {
        return $this->rgbBlue;
    }
}
