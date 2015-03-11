<?php

namespace Tisseo\EndivBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * GridCalendar
 */
class GridCalendar
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
     * @var boolean
     */
    private $monday;

    /**
     * @var boolean
     */
    private $tuesday;

    /**
     * @var boolean
     */
    private $wednesday;

    /**
     * @var boolean
     */
    private $thursday;

    /**
     * @var boolean
     */
    private $friday;

    /**
     * @var boolean
     */
    private $saturday;

    /**
     * @var boolean
     */
    private $sunday;

    /**
     * @var \Tisseo\EndivBundle\Entity\LineVersion
     */
    private $lineVersion;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $gridLinkCalendarMaskTypes;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->gridLinkCalendarMaskTypes = new ArrayCollection();
    }

    /**
     * merge
     * @param GridCalendar
     * @param LineVersion
     *
     * Fill GridCalendar attributes using another GridCalendar
     */
    public function merge(GridCalendar $gridCalendar, LineVersion $lineVersion)
    {
        $this->name = $gridCalendar->getName();
        $this->monday = $gridCalendar->getMonday();
        $this->tuesday = $gridCalendar->getTuesday();
        $this->wednesday = $gridCalendar->getWednesday();
        $this->thursday = $gridCalendar->getThursday();
        $this->friday = $gridCalendar->getFriday();
        $this->saturday = $gridCalendar->getSaturday();
        $this->sunday = $gridCalendar->getSunday();
        $this->lineVersion = $lineVersion;
    }

    /*
     * hasLinkToGridMaskType
     * @param integer $gridMaskTypeId
     * @return boolean
     * 
     * Check this GridCalendar is linked to a GridMaskType using GMT's id passed 
     * as parameter and checking it is present in its GLCMTs.
     *
     * return true if linked, false otherwise.
     */
    public function hasLinkToGridMaskType($gridMaskTypeId)
    {
        foreach ($this->gridLinkCalendarMaskTypes as $gridLinkCalendarMaskType)
        {
            if ($gridLinkCalendarMaskType->getGridMaskType()->getId() == $gridMaskTypeId)
                return true;
        }
        return false;
    }

    /*
     * updateLinks
     * @param array $gridMaskTypesIds
     * @return boolean
     *
     * Synchronize relation between the GridCalendar and its 
     * GridLinkCalendarMaskTypes. (i.e. delete GLCMT which aren't present in the 
     * array passed as parameter)
     *
     * return true if some GLCMT have been deleted, false otherwise.
     */
    public function updateLinks($gridMaskTypeIds)
    {
        $sync = false;
        foreach($this->gridLinkCalendarMaskTypes as $gridLinkCalendarMaskType)
        {
            if (!in_array($gridLinkCalendarMaskType->getGridMaskType()->getId(), $gridMaskTypeIds))
            {
                $this->removeGridLinkCalendarMaskType($gridLinkCalendarMaskType);
                $sync = true;
            }
        }
        return $sync;
    }

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
     * @param string $name
     * @return GridCalendar
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
     * Set monday
     *
     * @param boolean $monday
     * @return GridCalendar
     */
    public function setMonday($monday)
    {
        $this->monday = $monday;

        return $this;
    }

    /**
     * Get monday
     *
     * @return boolean 
     */
    public function getMonday()
    {
        return $this->monday;
    }

    /**
     * Set tuesday
     *
     * @param boolean $tuesday
     * @return GridCalendar
     */
    public function setTuesday($tuesday)
    {
        $this->tuesday = $tuesday;

        return $this;
    }

    /**
     * Get tuesday
     *
     * @return boolean 
     */
    public function getTuesday()
    {
        return $this->tuesday;
    }

    /**
     * Set wednesday
     *
     * @param boolean $wednesday
     * @return GridCalendar
     */
    public function setWednesday($wednesday)
    {
        $this->wednesday = $wednesday;

        return $this;
    }

    /**
     * Get wednesday
     *
     * @return boolean 
     */
    public function getWednesday()
    {
        return $this->wednesday;
    }

    /**
     * Set thursday
     *
     * @param boolean $thursday
     * @return GridCalendar
     */
    public function setThursday($thursday)
    {
        $this->thursday = $thursday;

        return $this;
    }

    /**
     * Get thursday
     *
     * @return boolean 
     */
    public function getThursday()
    {
        return $this->thursday;
    }

    /**
     * Set friday
     *
     * @param boolean $friday
     * @return GridCalendar
     */
    public function setFriday($friday)
    {
        $this->friday = $friday;

        return $this;
    }

    /**
     * Get friday
     *
     * @return boolean 
     */
    public function getFriday()
    {
        return $this->friday;
    }

    /**
     * Set saturday
     *
     * @param boolean $saturday
     * @return GridCalendar
     */
    public function setSaturday($saturday)
    {
        $this->saturday = $saturday;

        return $this;
    }

    /**
     * Get saturday
     *
     * @return boolean 
     */
    public function getSaturday()
    {
        return $this->saturday;
    }

    /**
     * Set sunday
     *
     * @param boolean $sunday
     * @return GridCalendar
     */
    public function setSunday($sunday)
    {
        $this->sunday = $sunday;

        return $this;
    }

    /**
     * Get sunday
     *
     * @return boolean 
     */
    public function getSunday()
    {
        return $this->sunday;
    }

    /**
     * Set lineVersion
     *
     * @param LineVersion $lineVersion
     * @return GridCalendar
     */
    public function setLineVersion(LineVersion $lineVersion = null)
    {
        $this->lineVersion = $lineVersion;

        return $this;
    }

    /**
     * Get lineVersion
     *
     * @return LineVersion 
     */
    public function getLineVersion()
    {
        return $this->lineVersion;
    }

    /**
     * Get gridLinkCalendarMaskTypes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getGridLinkCalendarMaskTypes()
    {
        return $this->gridLinkCalendarMaskTypes;
    }

    /**
     * Add gridLinkCalendarMaskTypes
     *
     * @param GridLinkCalendarMaskType $gridLinkCalendarMaskType
     * @return GridCalendar
     */
    public function addGridLinkCalendarMaskType(GridLinkCalendarMaskType $gridLinkCalendarMaskType)
    {
        $this->gridLinkCalendarMaskTypes[] = $gridLinkCalendarMaskType;

        return $this;
    }

    /**
     * Remove gridLinkCalendarMaskTypes
     *
     * @param GridLinkCalendarMaskType $gridLinkCalendarMaskType
     */
    public function removeGridLinkCalendarMaskType(GridLinkCalendarMaskType $gridLinkCalendarMaskType)
    {
        $this->gridLinkCalendarMaskTypes->removeElement($gridLinkCalendarMaskType);
    }
}
