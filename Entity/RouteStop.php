<?php

namespace Tisseo\EndivBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;

/**
 * RouteStop
 */
class RouteStop
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
     * @var boolean
     */
    private $scheduledStop;

    /**
     * @var boolean
     */
    private $pickup;

    /**
     * @var boolean
     */
    private $dropOff;

    /**
     * @var boolean
     */
    private $reservationRequired;

    /**
     * @var boolean
     */
    private $internalService;

    /**
     * @var Route
     */
    private $route;

    /**
     * @var RouteSection
     */
    private $routeSection;

    /**
     * @var Waypoint
     */
    private $waypoint;

    /**
     * @var Collection
     */
    private $stopTimes;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->stopTimes = new ArrayCollection();
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
     * Set id
     *
     * @param integer $id
     * @return RouteStop
     */

    public function setId($id){

        $this->id = $id;

        return $this;
    }

    /**
     * Set rank
     *
     * @param integer $rank
     * @return RouteStop
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
     * Set scheduledStop
     *
     * @param boolean $scheduledStop
     * @return RouteStop
     */
    public function setScheduledStop($scheduledStop)
    {
        $this->scheduledStop = $scheduledStop;

        return $this;
    }

    /**
     * Get scheduledStop
     *
     * @return boolean
     */
    public function getScheduledStop()
    {
        return $this->scheduledStop;
    }

    /**
     * Set pickup
     *
     * @param boolean $pickup
     * @return RouteStop
     */
    public function setPickup($pickup)
    {
        $this->pickup = $pickup;

        return $this;
    }

    /**
     * Get pickup
     *
     * @return boolean
     */
    public function getPickup()
    {
        return $this->pickup;
    }

    /**
     * Set dropOff
     *
     * @param boolean $dropOff
     * @return RouteStop
     */
    public function setDropOff($dropOff)
    {
        $this->dropOff = $dropOff;

        return $this;
    }

    /**
     * Get dropOff
     *
     * @return boolean
     */
    public function getDropOff()
    {
        return $this->dropOff;
    }

    /**
     * Set reservationRequired
     *
     * @param boolean $reservationRequired
     * @return RouteStop
     */
    public function setReservationRequired($reservationRequired)
    {
        $this->reservationRequired = $reservationRequired;

        return $this;
    }

    /**
     * Get reservationRequired
     *
     * @return boolean
     */
    public function getReservationRequired()
    {
        return $this->reservationRequired;
    }

    /**
     * Set route
     *
     * @param Route $route
     * @return RouteStop
     */
    public function setRoute(Route $route = null)
    {
        $this->route = $route;

        return $this;
    }

    /**
     * Get route
     *
     * @return Route
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * Set routeSection
     *
     * @param RouteSection $routeSection
     * @return RouteStop
     */
    public function setRouteSection(RouteSection $routeSection = null)
    {
        $this->routeSection = $routeSection;

        return $this;
    }

    /**
     * Get routeSection
     *
     * @return RouteSection
     */
    public function getRouteSection()
    {
        return $this->routeSection;
    }

    /**
     * Set waypoint
     *
     * @param Waypoint $waypoint
     * @return RouteStop
     */
    public function setWaypoint(Waypoint $waypoint = null)
    {
        $this->waypoint = $waypoint;

        return $this;
    }

    /**
     * Get waypoint
     *
     * @return Waypoint
     */
    public function getWaypoint()
    {
        return $this->waypoint;
    }

    /**
     * Set internalService
     *
     * @param boolean $internalService
     * @return RouteStop
     */
    public function setInternalService($internalService)
    {
        $this->internalService = $internalService;

        return $this;
    }

    /**
     * Get internalService
     *
     * @return boolean
     */
    public function getInternalService()
    {
        return $this->internalService;
    }

    /**
     * Set stopTimes
     *
     * @param Collection $stopTimes
     * @return RouteStop
     */
    public function setStopTimes(Collection $stopTimes = null)
    {
        $this->stopTimes = $stopTimes;

        return $this;
    }

    /**
     * Get stopTimes
     *
     * @return Collection
     */
    public function getStopTimes()
    {
        return $this->stopTimes;
    }

    /**
     * Add stopTimes
     *
     * @param StopTime $stopTime
     * @return Route
     */
    public function addStopTimes(StopTime $stopTime)
    {
        $this->stopTimes[] = $stopTime;
        $stopTime->setRoute($this);

        return $this;
    }

    /**
     * Remove stopTimes
     *
     * @param StopTime $stopTime
     */
    public function removeStopTimes(StopTime $stopTime)
    {
        $this->stopTimes->removeElement($stopTime);
    }

    /**
     * Add stopTimes
     *
     * @param \Tisseo\EndivBundle\Entity\StopTime $stopTimes
     * @return RouteStop
     */
    public function addStopTime(\Tisseo\EndivBundle\Entity\StopTime $stopTimes)
    {
        $this->stopTimes[] = $stopTimes;

        return $this;
    }

    /**
     * Remove stopTimes
     *
     * @param \Tisseo\EndivBundle\Entity\StopTime $stopTimes
     */
    public function removeStopTime(\Tisseo\EndivBundle\Entity\StopTime $stopTimes)
    {
        $this->stopTimes->removeElement($stopTimes);
    }

    public function isFirst()
    {
        return $this->rank === 1;
    }

    public function isLast()
    {
        return $this !== $this->route->getLastRouteStop();
    }

    public function isOdtAreaRouteStop()
    {
        $isOdtArea = (is_null($this->waypoint->getStop()) && !is_null($this->waypoint->getOdtArea()));
        return $isOdtArea;
    }

    public function getStopLabel()
    {
        if ($this->isOdtAreaRouteStop()) {
            $label = $this->waypoint->getOdtArea()->getName() . ' (zone)';
        } else {
            $label = $this->waypoint->getStop()->getStopArea()->getShortName();
        }
        return $label;
    }

    public function getStopTime($stopTimeId)
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('id',$stopTimeId))
            ->setMaxResults(1)
        ;

        return $this->stopTimes->matching($criteria)->first();
    }
}
