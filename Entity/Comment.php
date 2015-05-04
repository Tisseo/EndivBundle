<?php

namespace Tisseo\EndivBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Comment
 */
class Comment
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $label;

    /**
     * @var string
     */
    private $commentText;

    /**
     * @var Collection
     */
    private $trips;

    /**
     * @var Collection
     */
    private $routes;

    public function __construct($label = null, $commentText = null)
    {
        $this->trips = new ArrayCollection();
        $this->routes = new ArrayCollection();

        if ($label !== null)
            $this->label = $label;

        if ($commentText !== null)
            $this->commentText = $commentText;
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
     * Set label
     *
     * @param string $label
     * @return Comment
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
     * Set commentText
     *
     * @param string $commentText
     * @return Comment
     */
    public function setCommentText($commentText)
    {
        $this->commentText = $commentText;

        return $this;
    }

    /**
     * Get commentText
     *
     * @return string 
     */
    public function getCommentText()
    {
        return $this->commentText;
    }

    /**
     * Set trips
     *
     * @param Collection $trips
     * @return Trip
     */
    public function setTrips(Collection $trips)
    {
        $this->trips = $trips;
        foreach ($this->trips as $trip) {
            $trip->setComment($this);
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
     * Add trip
     *
     * @param Trip $trip
     * @return Trip
     */
    public function addTrip(Trip $trip)
    {
        $this->trips[] = $trip;
        $trip->setComment($this);

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

    /**
     * Set routes
     *
     * @param Collection $routes
     * @return Trip
     */
    public function setRoutes(Collection $routes)
    {
        $this->routes = $routes;
        foreach ($this->routes as $route) {
            $route->setComment($this);
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
     * @return Trip
     */
    public function addRoute(Route $route)
    {
        $this->routes[] = $route;

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
}
