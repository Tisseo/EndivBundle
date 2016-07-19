<?php

namespace Tisseo\EndivBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Tisseo\EndivBundle\Entity\Ogive\Board;

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
     * @var \Doctrine\Common\Collections\Collection
     */
    private $schematics;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $lineGroupGisContents;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $boards;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->lineDatasources = new ArrayCollection();
        $this->lineVersions = new ArrayCollection();
        $this->schematics = new ArrayCollection();
        $this->lineGroupGisContents = new ArrayCollection();
        $this->boards = new ArrayCollection();
    }

    /**
     * Get PhysicalMode name
     */
    public function getPhysicalModeName()
    {
        return $this->getPhysicalMode()->getName();
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

    /**
     * Add schematic
     *
     * @param \Tisseo\EndivBundle\Entity\Schematic $schematic
     * @return Line
     */
    public function addSchematics(Schematic $schematic)
    {
        $this->schematics[] = $schematic;

        return $this;
    }

    /**
     * Set schematics
     *
     * @param \Doctrine\Common\Collections\Collection $schematics
     * @return Line
     */
    public function setSchematics(Collection $schematics)
    {
        $this->$schematics = $schematics;
        foreach ($this->$schematics as $schematic) {
            $schematic->setLine($this);
        }
        return $this;
    }

    /**
     * Remove schematics
     *
     * @param \Tisseo\EndivBundle\Entity\Schematic $schematic
     */
    public function removeSchematics(Schematic $schematic)
    {
        $this->schematics->removeElement($schematic);
    }

    /**
     * Get schematics
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSchematics()
    {
        return $this->schematics;
    }

    /**
     * Add lineGroupGisContents
     *
     * @param \Tisseo\EndivBundle\Entity\LineGroupGisContent $lineGroupGisContents
     * @return Line
     */
    public function addLineGroupGisContent(\Tisseo\EndivBundle\Entity\LineGroupGisContent $lineGroupGisContents)
    {
        $this->lineGroupGisContents[] = $lineGroupGisContents;

        return $this;
    }

    /**
     * Remove lineGroupGisContents
     *
     * @param \Tisseo\EndivBundle\Entity\LineGroupGisContent $lineGroupGisContents
     */
    public function removeLineGroupGisContent(\Tisseo\EndivBundle\Entity\LineGroupGisContent $lineGroupGisContents)
    {
        $this->lineGroupGisContents->removeElement($lineGroupGisContents);
    }

    /**
     * Get lineGroupGisContents
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLineGroupGisContents()
    {
        return $this->lineGroupGisContents;
    }

    /**
     * Get boards
     *
     * @return Collection
     */
    public function getBoards()
    {
        return $this->boards;
    }

    /**
     * Set boards
     *
     * @param Collection $boards
     * @return Line
     */
    public function setBoards(Collection $boards)
    {
        $this->boards = $boards;

        return $this;
    }

    /**
     * Add board
     *
     * @param Board $board
     * @return Line
     */
    public function addBoard(Board $board)
    {
        $this->boards->add($board);

        return $this;
    }

    /**
     * Remove board
     *
     * @param Board $board
     * @return Line
     */
    public function removeBoard(Board $board)
    {
        $this->boards->removeElement($board);

        return $this;
    }

    // LineVersion Criteria functions

    /**
     * getLastLineVersion
     *
     * @return LineVersion
     */
    public function getLastLineVersion()
    {
        $criteria = Criteria::create()
            ->orderBy(array('endDate' => Criteria::DESC))
            ->setMaxResults(1)
        ;

        $lineVersions = $this->lineVersions->matching($criteria);

        if (!$lineVersions->isEmpty())
            return $lineVersions->first();

        return null;
    }

    /**
     * getHistoryLineVersions
     *
     * @param \Datetime $now
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getHistoryLineVersions(\Datetime $now)
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->lt('endDate', $now))
            ->orderBy(array('version' => Criteria::ASC))
        ;

        return $this->lineVersions->matching($criteria);
    }

    /**
     * Get current LineVersion
     *
     * @param \Datetime $now
     * @return LineVersion
     */
    public function getCurrentLineVersion(\Datetime $now = null)
    {
        if (is_null($now)) {
            $now = new \Datetime();
        }

        $criteria = Criteria::create()
            ->where(Criteria::expr()->lte('startDate', $now))
            ->andWhere(Criteria::expr()->orX(
                Criteria::expr()->gt('endDate', $now),
                Criteria::expr()->andX(
                    Criteria::expr()->isNull('endDate'),
                    Criteria::expr()->gte('plannedEndDate', $now)
                )
            ))
            ->setMaxResults(1)
        ;

        $lineVersions = $this->lineVersions->matching($criteria);

        if ($lineVersions->count() === 1) {
            return $lineVersions->first();
        }

        return null;
    }

    // Schematics Criteria functions

    /**
     * Get last schematic (priority on schematic with a filePath).
     * @param boolean withFile
     * @return \Datetime
     */
    public function getLastSchematic($withFile = false)
    {
        if ($this->schematics->count() === 0)
            return null;

        if ($withFile)
        {
            $criteria = Criteria::create()
                ->where(Criteria::expr()->neq('filePath', null))
                ->setMaxResults(1)
            ;

            $schematics = $this->schematics->matching($criteria);

            if (!$schematics->isEmpty())
                return $schematics->first();
        }

        return $this->schematics->first();
    }

    /**
     * Get first valid schematic date
     * @return Schematic
     */
    public function getFirstValidSchematic()
    {
        if ($this->schematics->count() === 0)
            return null;

        $criteria = Criteria::create()
            ->where(Criteria::expr()->neq('deprecated', true))
            ->andWhere(Criteria::expr()->neq('filePath', null))
            ->orderBy(array('date' => Criteria::ASC))
            ->setMaxResults(1)
        ;

        $schematics = $this->schematics->matching($criteria);

        if ($schematics->isEmpty())
            return null;

        return $schematics->first();
    }

    /**
     * Get fileSchematics
     * @return ArrayCollection
     */
    public function getFileSchematics($max = null)
    {
        if ($this->schematics->count() === 0)
            return null;

        $criteria = Criteria::create()
            ->where(Criteria::expr()->neq('filePath', null));
        if (!is_null($max)) {
           $criteria->setMaxResults($max);
        }
        $schematics = $this->schematics->matching($criteria);

        if ($schematics->isEmpty())
            return null;

        return $schematics;
    }

    /**
     * Get last gisSchematic
     * @return Schematic
     */
    public function getLastGisSchematic()
    {
        if ($this->schematics->count() === 0)
            return null;

        $criteria = Criteria::create()
            ->where(Criteria::expr()->neq('filePath', null))
            ->andWhere(Criteria::expr()->neq('deprecated', true))
            ->andWhere(Criteria::expr()->eq('groupGis', true))
        ;

        $schematics = $this->schematics->matching($criteria);

        if (!$schematics->isEmpty())
            return $schematics->first();

        return null;
    }

    /**
     * Get gisSchematics
     * @return ArrayCollection
     */
    public function getGisSchematics()
    {
        if ($this->schematics->count() === 0)
            return null;

        $criteria = Criteria::create()
            ->where(Criteria::expr()->neq('filePath', null))
            ->andWhere(Criteria::expr()->neq('deprecated', true))
        ;

        $result = array();
        foreach ($this->schematics->matching($criteria) as $schematic)
            $result[$schematic->getId()] = $schematic->getDateString();

        return $result;
    }
}
