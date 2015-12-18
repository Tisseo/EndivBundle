<?php

namespace Tisseo\EndivBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;

/**
 * Route
 */
class Route
{
    const WAY_FORWARD = 'Aller';
    const WAY_BACKWARD = 'Retour';
    const WAY_LOOP = 'Boucle';
    const WAY_AREA = 'Zonal';

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $way;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $direction;

    /**
     * @var Comment
     */
    private $comment;

    /**
     * @var LineVersion
     */
    private $lineVersion;

    /**
     * @var Collection
     */
    private $routeDatasources;

    /**
     * @var Collection
     */
    private $trips;

    /**
     * @var Collection
     */
    private $routeStops;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->trips = new ArrayCollection();
        $this->routeDatasources = new ArrayCollection();
        $this->routeStops = new ArrayCollection();
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
     * Set way
     *
     * @param string $way
     * @return Route
     */
    public function setWay($way)
    {
        $this->way = $way;

        return $this;
    }

    /**
     * Get way
     *
     * @return string
     */
    public function getWay()
    {
        return $this->way;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Route
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
     * Set direction
     *
     * @param string $direction
     * @return Route
     */
    public function setDirection($direction)
    {
        $this->direction = $direction;

        return $this;
    }

    /**
     * Get direction
     *
     * @return string
     */
    public function getDirection()
    {
        return $this->direction;
    }

    /**
     * Set comment
     *
     * @param Comment $comment
     * @return Route
     */
    public function setComment(Comment $comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return Comment
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set lineVersion
     *
     * @param LineVersion $lineVersion
     * @return Route
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
     * Set trips
     *
     * @param Collection $trips
     * @return Route
     */
    public function setTrips(Collection $trips)
    {
        $this->trips = $trips;
        foreach ($this->trips as $trip) {
            $trip->setRoute($this);
        }
        return $this;
    }

    /**
     * Get trips
     *
     * @return Collection
     */
    public function getTrips()
    {
        return $this->trips;
    }

    /**
     * Add trips
     *
     * @param Trip $trip
     * @return Route
     */
    public function addTrip(Trip $trip)
    {
        $this->trips[] = $trip;
        $trip->setRoute($this);

        return $this;
    }

    /**
     * Remove trip
     *
     * @param Trip $trip
     */
    public function removeTrip(Trip $trip)
    {
        $this->trips->removeElement($trip);
    }

    /** Remove trips
     *
     * @param Collection $trips
     */
    public function removeTrips(Collection $trips)
    {
        foreach($trips as $trip)
            $this->trips->removeElement($trip);
    }

    /**
     * Clear trips
     *
     * @return Route
     */
    public function clearTrips()
    {
        $this->trips->clear();
    }

    /**
     * Set routeStops
     *
     * @param Collection $routeStops
     * @return Route
     */
    public function setRouteStops(Collection $routeStops)
    {
        $this->routeStops = $routeStops;
        foreach ($this->routeStops as $routeStop) {
            $routeStop->setRoute($this);
        }
        return $this;
    }

    /**
     * Get routeStops
     *
     * @return Collection
     */
    public function getRouteStops()
    {
        return $this->routeStops;
    }

    /**
     * Add routeStops
     *
     * @param RouteStop $routeStop
     * @return Route
     */
    public function addRouteStops(RouteStop $routeStop)
    {
        $this->routeStops[] = $routeStop;
        $routeStop->setRoute($this);

        return $this;
    }

    /**
     * Remove routeStops
     *
     * @param RouteStop $routeStop
     */
    public function removeRouteStops(RouteStop $routeStop)
    {
        $this->routeStops->removeElement($routeStop);
    }

    /**
     * Clear routeStops
     *
     * @return Route
     */
    public function clearRouteStops()
    {
        $this->routeStops->clear();
    }

    /**
     * Set routeDatasources
     *
     * @param Collection $routeDatasources
     * @return Route
     */
    public function setRouteDatasources(Collection $routeDatasources)
    {
        $this->routeDatasources = $routeDatasources;
        foreach ($this->routeDatasources as $routeDatasource) {
            $routeDatasource->setRoute($this);
        }
        return $this;
    }

    /**
     * Get routeDatasources
     *
     * @return Collection
     */
    public function getRouteDatasources()
    {
        return $this->routeDatasources;
    }

    /**
     * Add routeDatasources
     *
     * @param RouteDatasource $routeDatasource
     * @return Route
     */
    public function addRouteDatasource(RouteDatasource $routeDatasource)
    {
        $this->routeDatasources[] = $routeDatasource;
        $routeDatasource->setRoute($this);

        return $this;
    }

    /**
     * Remove routeDatasources
     *
     * @param RouteDatasource $routeDatasource
     */
    public function removeRouteDatasource(RouteDatasource $routeDatasource)
    {
        $this->routeDatasources->removeElement($routeDatasource);
    }

    /**
     * Clear routeDatasources
     *
     * @return Route
     */
    public function clearRouteDatasources()
    {
        $this->routeDatasources->clear();
    }

    /**
     * Add routeStops
     *
     * @param \Tisseo\EndivBundle\Entity\RouteStop $routeStops
     * @return Route
     */
    public function addRouteStop(\Tisseo\EndivBundle\Entity\RouteStop $routeStops)
    {
        $this->routeStops[] = $routeStops;

        return $this;
    }

    /**
     * Remove routeStops
     *
     * @param \Tisseo\EndivBundle\Entity\RouteStop $routeStops
     */
    public function removeRouteStop(\Tisseo\EndivBundle\Entity\RouteStop $routeStops)
    {
        $this->routeStops->removeElement($routeStops);
    }

    /** Criteria functions **/

    /**
     * Getting all Trips pattern from the collection
     */
    public function getTripsPattern()
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('isPattern', true))
        ;

        return $this->trips->matching($criteria);
    }

    /**
     * Getting all Trips not pattern from the collection
     */
    public function getTripsNotPattern()
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->neq('isPattern', true))
        ;

        return $this->trips->matching($criteria);
    }

    /**
     * Getting all Trips with calendars which aren't pattern
     */
    public function getTripsNotPatternWithCalendars()
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->neq('isPattern', true))
            ->andWhere(Criteria::expr()->neq('dayCalendar', null))
            ->andWhere(Criteria::expr()->neq('periodCalendar', null))
        ;

        return $this->trips->matching($criteria);
    }

    /**
     * Checking the Route has Trips
     */
    public function hasTrips()
    {
        return ($this->trips->count() > 0);
    }

    /**
     * Checking the Route has Trips pattern
     */
    public function hasTripsNotPattern()
    {
        return ($this->getTripsNotPattern()->count() > 0);
    }

    /**
     * Getting the last RouteStop (using rank) of the Route
     */
    public function getLastRouteStop()
    {
        if ($this->routeStops->count() === 0)
            return null;

        $criteria = Criteria::create()
            ->orderBy(array('rank' => Criteria::DESC))
            ->setMaxResults(1)
        ;

        return $this->routeStops->matching($criteria)->first();
    }

    public function getFirstRouteStop()
    {
        return $this->routeStops->first();
    }

    /**
     * Getting a specific Trip from the Route
     * @param integer $tripId
     */
    public function getTrip($tripId)
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('id', $tripId))
            ->setMaxResults(1)
        ;

        return $this->trips->matching($criteria)->first();
    }

    /**
     * Checking a Trip pattern is used by Trips or not
     * @param Trip $pattern
     */
    public function IsPatternLocked(Trip $pattern)
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('pattern', $pattern))
        ;

        return $this->trips->matching($criteria)->count() > 0;
    }

    /**
     * Getting all Trips having a Trip parent from the collection
     */
    public function getTripsHavingParent()
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->neq('parent', null))
        ;

        return $this->trips->matching($criteria);
    }

    /**
     * Getting all Trips having a Trip parent from the collection
     */
    public function getTripsHavingPattern()
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->neq('pattern', null))
        ;

        return $this->trips->matching($criteria);
    }

    /**
     * Getting a Trip having a TripCalendar and the same Calendars
     * from the Trip passed as parameter
     * @param Trip $trip
     * @return TripCalendar
     */
    public function getAvailableTripCalendar(Trip $trip)
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->neq('tripCalendar', null))
            ->andWhere(Criteria::expr()->eq('dayCalendar', $trip->getDayCalendar()))
            ->andWhere(Criteria::expr()->eq('periodCalendar', $trip->getPeriodCalendar()))
            ->setMaxResults(1)
        ;

        $trips = $this->trips->matching($criteria);

        if ($trips->isEmpty())
            return null;

        return $trips->first()->getTripCalendar();
    }
}
