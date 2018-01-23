<?php

namespace Tisseo\EndivBundle\Entity;

/**
 * Color
 */
class Color
{
    /**
     * @var int
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
     * @var int
     */
    private $cmykCyan;

    /**
     * @var int
     */
    private $cmykMagenta;

    /**
     * @var int
     */
    private $cmykYellow;

    /**
     * @var int
     */
    private $cmykBlack;

    /**
     * @var int
     */
    private $rgbRed;

    /**
     * @var int
     */
    private $rgbGreen;

    /**
     * @var int
     */
    private $rgbBlue;

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
     * Set name
     *
     * @param string $name
     *
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
     * @param string $html
     *
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
     * @param string $pantoneOc
     *
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
     * @param string $hoxis
     *
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
     * @param int $cmykCyan
     *
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
     * @return int
     */
    public function getCmykCyan()
    {
        return $this->cmykCyan;
    }

    /**
     * Set cmykMagenta
     *
     * @param int $cmykMagenta
     *
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
     * @return int
     */
    public function getCmykMagenta()
    {
        return $this->cmykMagenta;
    }

    /**
     * Set cmykYellow
     *
     * @param int $cmykYellow
     *
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
     * @return int
     */
    public function getCmykYellow()
    {
        return $this->cmykYellow;
    }

    /**
     * Set cmykBlack
     *
     * @param int $cmykBlack
     *
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
     * @return int
     */
    public function getCmykBlack()
    {
        return $this->cmykBlack;
    }

    /**
     * Set rgbRed
     *
     * @param int $rgbRed
     *
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
     * @return int
     */
    public function getRgbRed()
    {
        return $this->rgbRed;
    }

    /**
     * Set rgbGreen
     *
     * @param int $rgbGreen
     *
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
     * @return int
     */
    public function getRgbGreen()
    {
        return $this->rgbGreen;
    }

    /**
     * Set rgbBlue
     *
     * @param int $rgbBlue
     *
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
     * @return int
     */
    public function getRgbBlue()
    {
        return $this->rgbBlue;
    }
}
