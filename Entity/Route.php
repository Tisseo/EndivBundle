<?php

namespace Tisseo\EndivBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Route
 */
class Route
{
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

    public function getLineVersionId()
    {
        return $this->lineVersion->getId();
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
    public function addRouteDatasources(RouteDatasource $routeDatasource)
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
    public function removeRouteDatasources(RouteDatasource $routeDatasource)
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
     * Add routeDatasources
     *
     * @param \Tisseo\EndivBundle\Entity\RouteDatasource $routeDatasources
     * @return Route
     */
    public function addRouteDatasource(\Tisseo\EndivBundle\Entity\RouteDatasource $routeDatasources)
    {
        $this->routeDatasources[] = $routeDatasources;

        return $this;
    }

    /**
     * Remove routeDatasources
     *
     * @param \Tisseo\EndivBundle\Entity\RouteDatasource $routeDatasources
     */
    public function removeRouteDatasource(\Tisseo\EndivBundle\Entity\RouteDatasource $routeDatasources)
    {
        $this->routeDatasources->removeElement($routeDatasources);
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
}
