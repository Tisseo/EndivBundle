<?php

namespace Tisseo\EndivBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * LineVersion
 */
class LineVersion
{
    const NW = "new";
    const WP = "wip";
    const PB = "published";

    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $version;

    /**
     * @var \DateTime
     */
    private $startDate;

    /**
     * @var \DateTime
     */
    private $endDate;

    /**
     * @var \DateTime
     */
    private $plannedEndDate;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $forwardDirection;

    /**
     * @var string
     */
    private $backwardDirection;

    /**
     * @var Color
     */
    private $bgColor;

    /**
     * @var Color
     */
    private $fgColor;

    /**
     * @var string
     */
    private $cartoFile;

    /**
     * @var boolean
     */
    private $accessibility;

    /**
     * @var boolean
     */
    private $airConditioned;

    /**
     * @var boolean
     */
    private $certified;

    /**
     * @var string
     */
    private $comment;

    /**
     * @var string
     */
    private $depot;

    /**
     * @var integer
     */
    private $childLineId;

    /**
     * @var status
     */
    private $status;

    /**
     * @var Line
     */
    private $line;

    /**
     * @var Line
     */
    private $childLine;

    /**
     * @var Schematic
     */
    private $schematic;

    /**
     * @var Collection
     */
    private $gridCalendars;

    /**
     * @var Collection
     */
    private $printings;

    /**
     * @var Collection
     */
    private $modifications;

    /**
     * @var Collection
     */
    private $routes;



    /**
     * Constructor
     * @param LineVersion $previousLineVersion = null
     * @param Line $line = null
     *
     * Build a LineVersion with default values
     * Add information from $previousLineVersion if not null
     * Link to a specific Line if $line is not null
     */
    public function __construct(LineVersion $previousLineVersion = null, Line $line = null)
    {
        $this->gridCalendars = new ArrayCollection();
        $this->printings = new ArrayCollection();
        $this->modifications = new ArrayCollection();
        $this->routes = new ArrayCollection();
        $this->startDate = new \Datetime();

        $this->version = 1;

        if ($previousLineVersion !== null)
        {
            if ($previousLineVersion->getEndDate() !== null)
            {
                $this->startDate = $previousLineVersion->getEndDate();
                $this->startDate->modify('+1 day');
            }
            $this->version = $previousLineVersion->getVersion() + 1;
            $this->name = $previousLineVersion->getName();
            $this->forwardDirection = $previousLineVersion->getForwardDirection();
            $this->backwardDirection = $previousLineVersion->getBackwardDirection();
            $this->fgColor = $previousLineVersion->getFgColor();
            $this->bgColor = $previousLineVersion->getBgColor();
            $this->accessibility = $previousLineVersion->getAccessibility();
            $this->airConditioned = $previousLineVersion->getAirConditioned();
            $this->certified = $previousLineVersion->getCertified();
            $this->depot = $previousLineVersion->getDepot();
            $this->setLine($previousLineVersion->getLine());
        }

        if ($line !== null)
        {
            $this->setLine($line);
        }

        $this->processStatus();
    }

    /**
     * Get PhysicalMode name
     */
    public function getPhysicalModeName()
    {
        return $this->getLine()->getPhysicalMode()->getName();
    }

    /**
     * Merge GridCalendars
     * @param LineVersion $lineVersion
     *
     * Attach gridCalendars passed from another LineVersion
     */
    public function mergeGridCalendars(LineVersion $lineVersion)
    {
        foreach($lineVersion->getGridCalendars() as $gridCalendar)
        {
            $newGridCalendar = new GridCalendar();
            $newGridCalendar->merge($gridCalendar, $this);
            $this->addGridCalendar($newGridCalendar);
        }
    }

    /**
     * Process Status
     *
     * Set status according to startDate and gridCalendars values
     */
    public function processStatus()
    {
        $now = new \Datetime();
        if ($this->startDate < $now)
            $this->status = self::PB;
        else if ($this->gridCalendars->isEmpty())
            $this->status = self::NW;
        else
        {
            foreach($this->gridCalendars as $gridCalendar)
            {
                if ($gridCalendar->getGridLinkCalendarMaskTypes()->isEmpty())
                {
                    $this->status = self::WP;
                    break;
                }
            }
        }
    }

    /**
     * Close Date
     * @param Datetime $date
     *
     * Set the endDate with the date passed as parameter
     */
    public function closeDate(\Datetime $date)
    {
        $this->endDate = new \Datetime($date->format('Y-m-d'));
        $this->endDate->modify('-1 day');
    }

    /**
     * isLocked
     * @return boolean
     *
     * A LineVersion is locked if :
     *  - it has started (i.e. startDate < now)
     *  - startDate is less than 20 days left
     */
    public function isLocked()
    {
        $now = new \Datetime();
        if ($this->startDate < $now)
            return true;
        else
        {
            $diff = intval($this->startDate->diff($now)->format('%a'));
            return ($diff < 20);
        }
    }

    /**
     * isNew
     *
     * @return boolean
     *
     * A LineVersion is new if no gridCalendars are linked to it
     */
    public function isNew()
    {
        return ($this->gridCalendars->isEmpty());
    }

    /**
     * isActive
     *
     * @return boolean
     *
     * A LineVersion is active if now is between its startDate/endDate
     */
    public function isActive()
    {
        $now = new \Datetime();
        return ($this->startDate < $now && ($this->endDate > $now || $this->endDate === null));
    }

    /**
     * getTotalPrintings
     *
     * @return integer
     *
     * Return the total amount of printings (i.e. printing.quantity)
     */
    public function getTotalPrintings()
    {
        $printings = 0;
        foreach($this->printings as $printing)
            $printings += $printing->getQuantity();

        return $printings;
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
     * Set version
     *
     * @param integer $version
     * @return LineVersion
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Get version
     *
     * @return integer
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set startDate
     *
     * @param \DateTime $startDate
     * @return LineVersion
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get startDate
     *
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set endDate
     *
     * @param \DateTime $endDate
     * @return LineVersion
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get endDate
     *
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Set plannedEndDate
     *
     * @param \DateTime $plannedEndDate
     * @return LineVersion
     */
    public function setPlannedEndDate($plannedEndDate)
    {
        $this->plannedEndDate = $plannedEndDate;

        return $this;
    }

    /**
     * Get plannedEndDate
     *
     * @return \DateTime
     */
    public function getPlannedEndDate()
    {
        return $this->plannedEndDate;
    }

    /**
     * Set childLine
     *
     * @param Line $childLine
     * @return LineVersion
     */
    public function setChildLine(Line $childLine = null)
    {
        $this->childLine = $childLine;

        return $this;
    }

    /**
     * Get childLine
     *
     * @return Line
     */
    public function getChildLine()
    {
        return $this->childLine;
    }

    /**
     * Set childLineId
     *
     * @param integer $childLineId
     * @return LineVersion
     */
    public function setChildLineId($childLineId)
    {
        $this->childLineId = $childLineId;

        return $this;
    }

    /**
     * Get childLineId
     *
     * @return integer
     */
    public function getChildLineId()
    {
        return $this->childLineId;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return LineVersion
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
     * Set forwardDirection
     *
     * @param string $forwardDirection
     * @return LineVersion
     */
    public function setForwardDirection($forwardDirection)
    {
        $this->forwardDirection = $forwardDirection;

        return $this;
    }

    /**
     * Get forwardDirection
     *
     * @return string
     */
    public function getForwardDirection()
    {
        return $this->forwardDirection;
    }

    /**
     * Set backwardDirection
     *
     * @param string $backwardDirection
     * @return LineVersion
     */
    public function setBackwardDirection($backwardDirection)
    {
        $this->backwardDirection = $backwardDirection;

        return $this;
    }

    /**
     * Get backwardDirection
     *
     * @return string
     */
    public function getBackwardDirection()
    {
        return $this->backwardDirection;
    }

    /**
     * Set bgColor
     *
     * @param Color $bgColor
     * @return LineVersion
     */
    public function setBgColor(Color $bgColor = null)
    {
        $this->bgColor = $bgColor;

        return $this;
    }

    /**
     * Get bgColor
     *
     * @return Color 
     */
    public function getBgColor()
    {
        return $this->bgColor;
    }

    /**
     * Set fgColor
     *
     * @param Color $fgColor
     * @return LineVersion
     */
    public function setFgColor(Color $fgColor = null)
    {
        $this->fgColor = $fgColor;

        return $this;
    }

    /**
     * Get fgColor
     *
     * @return Color 
     */
    public function getFgColor()
    {
        return $this->fgColor;
    }

    /**
     * Set cartoFile
     *
     * @param string $cartoFile
     * @return LineVersion
     */
    public function setCartoFile($cartoFile)
    {
        $this->cartoFile = $cartoFile;

        return $this;
    }

    /**
     * Get cartoFile
     *
     * @return string
     */
    public function getCartoFile()
    {
        return $this->cartoFile;
    }

    /**
     * Set accessibility
     *
     * @param boolean $accessibility
     * @return LineVersion
     */
    public function setAccessibility($accessibility)
    {
        $this->accessibility = $accessibility;

        return $this;
    }

    /**
     * Get accessibility
     *
     * @return boolean
     */
    public function getAccessibility()
    {
        return $this->accessibility;
    }

    /**
     * Set certified
     *
     * @param boolean $certified
     * @return LineVersion
     */
    public function setCertified($certified)
    {
        $this->certified = $certified;

        return $this;
    }

    /**
     * Get certified
     *
     * @return boolean
     */
    public function getCertified()
    {
        return $this->certified;
    }

    /**
     * Set airConditioned
     *
     * @param boolean $airConditioned
     * @return LineVersion
     */
    public function setAirConditioned($airConditioned)
    {
        $this->airConditioned = $airConditioned;

        return $this;
    }

    /**
     * Get airConditioned
     *
     * @return boolean
     */
    public function getAirConditioned()
    {
        return $this->airConditioned;
    }

    /**
     * Set comment
     *
     * @param string $comment
     * @return LineVersion
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
     * Set depot
     *
     * @param string $depot
     * @return LineVersion
     */
    public function setDepot($depot)
    {
        $this->depot = $depot;

        return $this;
    }

    /**
     * Get depot
     *
     * @return string
     */
    public function getDepot()
    {
        return $this->depot;
    }

    /**
     * Set line
     *
     * @param Line $line
     * @return LineVersion
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

    /**
     * Set line
     *
     * @param Schematic $schematic
     * @return LineVersion
     */
    public function setSchematic(Schematic $schematic = null)
    {
        $this->schematic = $schematic;

        return $this;
    }

    /**
     * Get Schematic
     *
     * @return Schematic
     */
    public function getSchematic()
    {
        return $this->schematic;
    }

    /**
     * Set status
     *
     * @param string
     * @return LineVersion
     */
    public function setStatus($satus)
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
     * Set gridCalendars
     *
     * @param Collection $gridCalendars
     * @return LineVersion
     */
    public function setGridCalendars(Collection $gridCalendars)
    {
        $this->gridCalendars = $gridCalendars;
        return $this;
    }

    /**
     * Get gridCalendars
     *
     * @return Collection
     */
    public function getGridCalendars()
    {
        return $this->gridCalendars;
    }

    /**
     * Add gridCalendars
     *
     * @param GridCalendar $gridCalendar
     * @return LineVersion
     */
    public function addGridCalendars(GridCalendar $gridCalendar)
    {
        $this->gridCalendars[] = $gridCalendar;
        return $this;
    }

    /**
     * Remove gridCalendars
     *
     * @param GridCalendar $gridCalendar
     */
    public function removeGridCalendars(GridCalendar $gridCalendar)
    {
        $this->gridCalendars->removeElement($gridCalendar);
    }

    /**
     * Clear gridCalendars
     *
     * @return LineVersion
     */
    public function clearGridCalendars()
    {
        $this->gridCalendars->clear();
        return $this;
    }

    /**
     * Set modifications
     *
     * @param Collection $modifications
     * @return LineVersion
     */
    public function setModifications(Collection $modifications)
    {
        $this->modifications = $modifications;
        return $this;
    }

    /**
     * Get modifications
     *
     * @return Collection
     */
    public function getModifications()
    {
        return $this->modifications;
    }

    /**
     * Add modifications
     *
     * @param Modification $modification
     * @return LineVersion
     */
    public function addModification(Modification $modification)
    {
        $this->modifications[] = $modification;
        $modification->setLineVersion($this);
        return $this;
    }

    /**
     * Remove modifications
     *
     * @param Modification $modification
     */
    public function removeModification(Modification $modification)
    {
        $this->modifications->removeElement($modification);
    }

    /**
     * Clear modifications
     *
     * @return LineVersion
     */
    public function clearModifications()
    {
        $this->modifications->clear();
        return $this;
    }

    /**
     * Set printings
     *
     * @param Collection $printings
     * @return Line
     */
    public function setPrintings(Collection $printings)
    {
        $this->printings = $printings;
        foreach ($this->printings as $printing) {
            $printing->setLineVersion($this);
        }
        return $this;
    }

    /**
     * Get printings
     *
     * @return Collection
     */
    public function getPrintings()
    {
        return $this->printings;
    }

    /**
     * Add printings
     *
     * @param Printing $printing
     * @return LineVersion
     */
    public function addPrintings(Printing $printing)
    {
        $this->printings[] = $printing;
        $printing->setLineVersion($this);
        return $this;
    }

    /**
     * Remove printings
     *
     * @param Printing $printing
     */
    public function removePrintings(Printing $printing)
    {
        $this->printings->removeElement($printing);
    }

    /**
     * Clear printings
     *
     * @return LineVersion
     */
    public function clearPrintings()
    {
        $this->printings->clear();

        return $this;
    }

    /**
     * Set routes
     *
     * @param Collection $routes
     * @return LineVersion
     */
    public function setRoutes(Collection $routes)
    {
        $this->routes = $routes;
        foreach ($this->routes as $route) {
            $route->setLineVersion($this);
        }
        return $this;
    }

    /**
     * Get routes
     *
     * @return Collection
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * Add route
     *
     * @param Route $route
     * @return LineVersion
     */
    public function addRoute(Route $route)
    {
        $this->routes[] = $route;
        $route->setLineVersion($this);

        return $this;
    }

    /**
     * Remove route
     *
     * @param Route $route
     */
    public function removeRoute(Route $route)
    {
        $this->routes->removeElement($route);
    }

    /**
     * Clear routes
     *
     * @return LineVersion
     */
    public function clearRoutes()
    {
        $this->routes->clear();
    }

    /**
     * Add gridCalendars
     *
     * @param GridCalendar $gridCalendars
     * @return LineVersion
     */
    public function addGridCalendar(GridCalendar $gridCalendars)
    {
        $this->gridCalendars[] = $gridCalendars;

        return $this;
    }

    /**
     * Remove gridCalendars
     *
     * @param GridCalendar $gridCalendars
     */
    public function removeGridCalendar(GridCalendar $gridCalendars)
    {
        $this->gridCalendars->removeElement($gridCalendars);
    }

    /**
     * Add printings
     *
     * @param Printing $printings
     * @return LineVersion
     */
    public function addPrinting(Printing $printings)
    {
        $this->printings[] = $printings;

        return $this;
    }

    /**
     * Remove printings
     *
     * @param Printing $printings
     */
    public function removePrinting(Printing $printings)
    {
        $this->printings->removeElement($printings);
    }
	
    /**
     * Remove printings
     *
     * @param Printing $printings
     */
	public function getFormattedLineVersion()	
	{
		return $this->getLine()->getNumber() .'_'.$this->getVersion();
	}
}
