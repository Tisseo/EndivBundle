<?php

namespace Tisseo\EndivBundle\Entity\Ogive;

/**
 * EmergencyStatus
 */
class EmergencyStatus
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $rank;

    /**
     * @var guid
     */
    private $chaosSeverity;

    /**
     * @var string
     */
    private $color;

    /**
     * @var string
     */
    private $label;

    /**
     * @var string
     */
    private $description;


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
     * Set rank
     *
     * @param  integer $rank
     * @return EmergencyStatus
     */
    public function setRank($rank)
    {
        $this->rank = $rank;

        return $this;
    }

    /**
     * Get rank
     *
     * @return integer
     */
    public function getRank()
    {
        return $this->rank;
    }

    /**
     * Set chaosSeverity
     *
     * @param  guid $chaosSeverity
     * @return EmergencyStatus
     */
    public function setChaosSeverity($chaosSeverity)
    {
        $this->chaosSeverity = $chaosSeverity;

        return $this;
    }

    /**
     * Get chaosSeverity
     *
     * @return guid
     */
    public function getChaosSeverity()
    {
        return $this->chaosSeverity;
    }

    /**
     * Set color
     *
     * @param  string $color
     * @return EmergencyStatus
     */
    public function setColor($color)
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Get color
     *
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * Set label
     *
     * @param  string $label
     * @return EmergencyStatus
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
     * Set description
     *
     * @param  string $description
     * @return EmergencyStatus
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
}
