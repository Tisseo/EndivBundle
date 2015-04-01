<?php

namespace Tisseo\EndivBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use \Doctrine\Common\Collections\ArrayCollection;
use \Doctrine\Common\Collections\Collection;

/**
 * Line
 */
class Line
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $number;

    /**
     * @var integer
     */
    private $priority;

    /**
     * @var PhysicalMode
     */
    private $physicalMode;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $lineDatasources;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $lineVersions;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->lineDatasources = new ArrayCollection();
        $this->lineVersions = new ArrayCollection();
    }

    /**
     * Get PhysicalMode name
     */
    public function getPhysicalModeName()
    {
        return $this->getPhysicalMode()->getName();
    }

    /**
     * getLastLineVersion
     * 
     * @return LineVersion
     */
    public function getLastLineVersion()
    {
        $result = null;
        foreach($this->lineVersions as $lineVersion)
        {
            if ($lineVersion->getEndDate() === null)
                return $lineVersion;
            else if($result !== null && $lineVersion->getEndDate() < $result->getEndDate())
                continue;
            $result = $lineVersion;
        }
        return $result;
    }

    /**
     * getHistoryLineVersions
     * 
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getHistoryLineVersions()
    {
        $result = new ArrayCollection();
        $now = new \Datetime();
        foreach($this->lineVersions as $lineVersion)
        {
            if ($lineVersion->getEndDate() !== null)
                $result[] = $lineVersion;
        }
        return $result;
    }

    /**
     * Define priority
     */
    public function definePriority()
    {
        $priorities = array("Métro" => 1, "Tramway" => 2, "Bus" => 3, "TAD" => 4);
        $this->priority = $priorities[$this->getPhysicalMode()->getName()];
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
     * Set number
     *
     * @param string $number
     * @return Line
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return string 
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set priority
     *
     * @param integer $priority
     * @return Line
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Get priority
     *
     * @return integer 
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Set physicalMode
     *
     * @param PhysicalMode $physicalMode
     * @return Line
     */
    public function setPhysicalMode(PhysicalMode $physicalMode = null)
    {
        $this->physicalMode = $physicalMode;

        return $this;
    }

    /**
     * Get physicalMode
     *
     * @return PhysicalMode 
     */
    public function getPhysicalMode()
    {
        return $this->physicalMode;
    }

    /**
     * Get physicalMode
     *
     * @return PhysicalMode 
     */
    public function getPhysicalModeId()
    {
        return $this->physicalMode->getId();
    }

    /**
     * Get lineDatasources
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getLineDatasources()
    {
        return $this->lineDatasources;
    }

    /**
     * Set lineDatasources
     *
     * @param \Doctrine\Common\Collections\Collection $lineDatasources
     * @return Line
     */
    public function setLineDatasources(Collection $lineDatasources)
    {
        $this->lineDatasources = $lineDatasources;
        foreach ($this->lineDatasources as $lineDatasource) {
            $lineDatasource->setLine($this);
        }
        return $this;
    }

    /**
     * Add lineDatasources
     *
     * @param LineDatasource $lineDatasources
     * @return Line
     */
    public function addLineDatasources(LineDatasource $lineDatasources)
    {
        $this->lineDatasources[] = $lineDatasources;
        $lineDatasources->setLine($this);
        return $this;
    }

    /**
     * Remove lineDatasources
     *
     * @param LineDatasource $lineDatasources
     */
    public function removeLineDatasources(LineDatasource $lineDatasources)
    {
        $this->lineDatasources->removeElement($lineDatasources);
    }

    /**
     * Get lineVersions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getLineVersions()
    {
        return $this->lineVersions;
    }

    /**
     * Set lineVersions
     *
     * @param \Doctrine\Common\Collections\Collection $lineVersions
     * @return Line
     */
    public function setLineVersions(Collection $lineVersions)
    {
        $this->lineVersions = $lineVersions;
        foreach ($this->lineVersions as $lineVersion) {
            $lineVersion->setLine($this);
        }
        return $this;
    }

    /**
     * Add lineVersions
     *
     * @param LineVersion $lineVersion
     * @return Line
     */
    public function addLineVersions(LineVersion $lineVersion)
    {
        $this->lineVersions[] = $lineVersion;
        $lineVersion->setLine($this);
        return $this;
    }

    /**
     * Remove lineVersions
     *
     * @param LineVersion $lineVersion
     */
    public function removeLineVersions(LineVersion $lineVersion)
    {
        $this->lineVersions->removeElement($lineVersion);
    }

    /**
     * Add lineDatasources
     *
     * @param LineDatasource $lineDatasources
     * @return Line
     */
    public function addLineDatasource(LineDatasource $lineDatasources)
    {
        $this->lineDatasources[] = $lineDatasources;

        return $this;
    }

    /**
     * Remove lineDatasources
     *
     * @param LineDatasource $lineDatasources
     */
    public function removeLineDatasource(LineDatasource $lineDatasources)
    {
        $this->lineDatasources->removeElement($lineDatasources);
    }

    /**
     * Add lineVersions
     *
     * @param LineVersion $lineVersions
     * @return Line
     */
    public function addLineVersion(LineVersion $lineVersions)
    {
        $this->lineVersions[] = $lineVersions;

        return $this;
    }

    /**
     * Remove lineVersions
     *
     * @param LineVersion $lineVersions
     */
    public function removeLineVersion(LineVersion $lineVersions)
    {
        $this->lineVersions->removeElement($lineVersions);
    }
}
