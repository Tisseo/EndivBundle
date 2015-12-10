<?php

namespace Tisseo\EndivBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;

/**
 * Stop
 */
class Stop
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Tisseo\EndivBundle\Entity\Stop
     */
    private $masterStop;

    /**
     * @var \Tisseo\EndivBundle\Entity\StopArea
     */
    private $stopArea;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $stopDatasources;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $stopHistories;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $stopAccessibilities;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $phantoms;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $odtStops;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->stopDatasources = new ArrayCollection();
        $this->stopHistories = new ArrayCollection();
        $this->stopAccessibilities = new ArrayCollection();
        $this->phantoms = new ArrayCollection();
        $this->odtStops = new ArrayCollection();
    }

    /**
     * Set id
     *
     * @param integer $id
     * @return Stop
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Set OdtStop
     *
     * @param \Tisseo\EndivBundle\Entity\OdtStop
     * @return Stop
     */
    public function setOdtStop($odtstop)
    {
        $this->odtStop = $odtstop;

        return $this;
    }


    /**
     * Get OdtStop
     *
     * @return \Tisseo\EndivBundle\Entity\OdtStop
     */
    public function getOdtStop()
    {
        return $this->odtStop;
    }

    /**
     * Get id
     *
     * @return \Tisseo\EndivBundle\Entity\Waypoint
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set masterStop
     *
     * @param \Tisseo\EndivBundle\Entity\Stop $masterStop
     * @return Stop
     */
    public function setMasterStop(\Tisseo\EndivBundle\Entity\Stop $masterStop = null)
    {
        $this->masterStop = $masterStop;

        return $this;
    }

    /**
     * Get masterStop
     *
     * @return \Tisseo\EndivBundle\Entity\Stop
     */
    public function getMasterStop()
    {
        return $this->masterStop;
    }

    /**
     * Set stopArea
     *
     * @param \Tisseo\EndivBundle\Entity\StopArea $stopArea
     * @return Stop
     */
    public function setStopArea(\Tisseo\EndivBundle\Entity\StopArea $stopArea = null)
    {
        $this->stopArea = $stopArea;

        return $this;
    }

    /**
     * Get stopArea
     *
     * @return \Tisseo\EndivBundle\Entity\StopArea
     */
    public function getStopArea()
    {
        return $this->stopArea;
    }
    /**
     * @var \Tisseo\EndivBundle\Entity\Waypoint
     */
    private $waypoint;


    /**
     * Set waypoint
     *
     * @param \Tisseo\EndivBundle\Entity\Waypoint $waypoint
     * @return Stop
     */
    public function setWaypoint(\Tisseo\EndivBundle\Entity\Waypoint $waypoint = null)
    {
        $this->waypoint = $waypoint;

        return $this;
    }

    /**
     * Get waypoint
     *
     * @return \Tisseo\EndivBundle\Entity\Waypoint
     */
    public function getWaypoint()
    {
        return $this->waypoint;
    }

    /**
     * Get getOdtStops
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOdtStops()
    {
        return $this->odtStops;
    }

    public function getOpenedOdtStops()
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->orX(
                    Criteria::expr()->isNull('endDate'),
                    Criteria::expr()->gte('endDate', new \DateTime())
                ));
        return $this->odtStops->matching($criteria);
    }

    /**
     * Set odtStops
     *
     * @param \Doctrine\Common\Collections\Collection $odtStops
     * @return OdtArea
     */
    public function setOdtStops(Collection $odtStops)
    {
        $this->odtStops = $odtStops;
        foreach ($this->odtStops as $odtStop) {
            $odtStop->setStop($this);
        }
        return $this;
    }

    /**
     * Add odtStop
     *
     * @param OdtStop $odtStop
     * @return OdtArea
     */
    public function addOdtStop(odtStop $odtStop)
    {
        $this->odtStops[] = $odtStop;
        $odtStop->setStop($this);
        return $this;
    }

    /**
     * Remove odtStop
     *
     * @param OdtStop $odtStop
     */
    public function removeOdtStop(odtStop $odtStop)
    {
        $this->odtStops->removeElement($odtStop);
    }

    /**
     * Get stopDatasources
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getStopDatasources()
    {
        return $this->stopDatasources;
    }

    /**
     * Set stopDatasources
     *
     * @param \Doctrine\Common\Collections\Collection $stopDatasources
     * @return Line
     */
    public function setStopDatasources(Collection $stopDatasources)
    {
        $this->stopDatasources = $stopDatasources;
        foreach ($this->stopDatasources as $stopDatasource) {
            $stopDatasource->setStop($this);
        }
        return $this;
    }

    /**
     * Add stopDatasource
     *
     * @param StopDatasource $stopDatasources
     * @return Line
     */
    public function addStopDatasources(StopDatasource $stopDatasource)
    {
        $this->stopDatasources[] = $stopDatasource;
        $stopDatasource->setStop($this);
        return $this;
    }

    /**
     * Remove stopDatasources
     *
     * @param StopDatasource $stopDatasources
     */
    public function removeStopDatasources(StopDatasource $stopDatasources)
    {
        $this->stopDatasources->removeElement($stopDatasources);
    }

    /**
     * Get getStopHistories
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getStopHistories()
    {
        return $this->stopHistories;
    }

    /**
     * Set StopHistories
     *
     * @param \Doctrine\Common\Collections\Collection $stopHistories
     * @return Line
     */
    public function setStopHistories(Collection $stopHistories)
    {
        $this->stopHistories = $stopHistories;
        foreach ($this->stopHistories as $stopHistory) {
            $stopHistory->setStop($this);
        }
        return $this;
    }

    /**
     * Add stopHistory
     *
     * @param stopHistory $stopHistory
     * @return Line
     */
    public function addStopHistory(stopHistory $stopHistory)
    {
        $this->stopHistories[] = $stopHistory;
        $stopHistory->setStop($this);
        return $this;
    }

    /**
     * Remove stopHistory
     *
     * @param stopHistory $stopHistory
     */
    public function removeStopHistory(stopHistory $stopHistory)
    {
        $this->stopHistories->removeElement($stopHistory);
    }

    /**
     * Get getStopAccessibilities
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getStopAccessibilities()
    {
        return $this->stopAccessibilities;
    }

    /**
     * Set StopAccessibilities
     *
     * @param \Doctrine\Common\Collections\Collection $stopAccessibilities
     * @return Line
     */
    public function setStopAccessibilities(Collection $stopAccessibilities)
    {
        $this->stopAccessibilities = $stopAccessibilities;
        foreach ($this->stopAccessibilities as $stopAccessibility) {
            $stopAccessibility->setStop($this);
        }
        return $this;
    }

    /**
     * Add stopAccessibility
     *
     * @param stopAccessibility $stopAccessibility
     * @return Line
     */
    public function addStopAccessibility(stopAccessibility $stopAccessibility)
    {
        $this->stopAccessibilities[] = $stopAccessibility;
        $stopAccessibility->setStop($this);
        return $this;
    }

    /**
     * Remove stopAccessibility
     *
     * @param stopAccessibility $stopAccessibility
     */
    public function removeStopAccessibility(stopAccessibility $stopAccessibility)
    {
        $this->stopAccessibilities->removeElement($stopAccessibility);
    }

    /**
     * Find stopAccessibility
     *
     * @param integer $stopAccessibilityId
     */
    public function findStopAccessibility($stopAccessibilityId)
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->in('id', $stopAccessibilityId))
        ;

        return $this->stopAccessibilities()->matching($criteria);
    }

    /**
     * Get phantoms
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPhantoms()
    {
        return $this->phantoms;
    }

    /**
     * Set phantoms
     *
     * @param \Doctrine\Common\Collections\Collection $stopDatasources
     * @return Line
     */
    public function setPhantoms(Collection $phantoms)
    {
        $this->phantoms = $phantoms;
        foreach ($this->phantoms as $phantom) {
            $phantom->setMasterStop($this);
        }
        return $this;
    }

    /**
     * Add phantom
     *
     * @param Stop $Phantom
     * @return Line
     */
    public function addPhantom(Stop $phantom)
    {
        $this->phantoms[] = $phantom;
        $phantom->setMasterStop($this);
        return $this;
    }

    /**
     * Remove Phantom
     *
     * @param Stop $Phantom
     */
    public function removePhantom(Stop $phantom)
    {
        $this->phantoms->removeElement($phantom);
    }

    /**
     * CUSTOM FUNCTIONS MOST USING CRITERIA FOR SIMPLE DB REQUESTS
     */

    /**
     * Latest StopHistory
     */
    public function getLatestStopHistory()
    {
        $criteria = Criteria::create()
            ->orderBy(array('startDate' => Criteria::DESC))
            ->setMaxResults(1)
        ;

        return $this->stopHistories->matching($criteria)->first();
    }

    public function getYoungerStopHistories(\Datetime $date)
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->gte('startDate', $date))
            ->orderBy(array('startDate' => Criteria::DESC))
        ;

        return $this->stopHistories->matching($criteria);
    }

    /**
     * Current StopHistory
     *
     * @param \Datetime $date
     */
    public function getCurrentStopHistory($date)
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->lte('startDate', $date))
            ->andWhere(
                Criteria::expr()->orX(
                    Criteria::expr()->isNull('endDate'),
                    Criteria::expr()->gt('endDate', $date)
                )
            )
            ->setMaxResults(1)
        ;

        return $this->stopHistories->matching($criteria)->first();
    }

    /**
     * Current or Latest StopHistory
     *
     * @param \Datetime $date
     */
    public function getCurrentOrLatestStopHistory($date)
    {
        $stopHistory = $this->getCurrentStopHistory($date);

        return (empty($stopHistory) ? $this->getLatestStopHistory() : $stopHistory);
    }

    /**
     * Stop label
     *
     * Custom function to request a Stop "name" looking at its StopHistories and
     * StopDatasources.
     * {StopHistory.shortName} - {stopDatasource.agency.name} ({stopDatasource.code})
     * TODO: The Datetime is instanciated each time this function is called
     * Investigate in possibility to pass $date as a parameter in a Symfony Form entity
     * field in the option 'property' (cf. Tisseo/BoaBundle/Form/Type/StopEditType.php)
     */
    public function getStopLabel()
    {
        $stopHistory = $this->getCurrentOrLatestStopHistory(new \Datetime());
        if (empty($stopHistory))
            return "";

        $result = $stopHistory->getShortName();
        foreach ($this->stopDatasources as $stopDatasource)
            $result .= " - ".$stopDatasource->getDatasource()->getAgency()->getName()." (".$stopDatasource->getCode().")";

        return $result;
    }

    /**
     * Stop display label
     *
     * Custom function to request a Stop name and city name looking at its StopHistories and
     * StopDatasources.
     * {StopHistory.shortName} - {stopArea.city.name} ({stopDatasource.code})
    */
    public function getStopDisplayLabel()
    {
        $now = new \Datetime();
        if (empty($this->masterStop))
        {
            $stopHistory = $this->getCurrentOrLatestStopHistory($now);
        }
        else
        {
            $stopHistory = $this->masterStop->getCurrentOrLatestStopHistory($now);
        }

        if (empty($stopHistory))
        {
            return "";
        }
        $result = $stopHistory->getShortName();
        $result .= " ".$this->getStopArea()->getCity()->getName();
        foreach ($this->stopDatasources as $stopDatasource)
            $result .= " (".$stopDatasource->getCode().")";

        return $result;
    }

    /**
     * Closable
     *
     * Checking an older StopHistory exists and its endDate is null.
     */
    public function closable()
    {
        $stopHistory = $this->getLatestStopHistory();

        if (empty($stopHistory) || $stopHistory->getEndDate() !== null)
            return false;

        return true;
    }
}
