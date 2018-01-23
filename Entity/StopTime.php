<?php

namespace Tisseo\EndivBundle\Entity;

/**
 * StopTime
 */
class StopTime
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $arrivalTime;

    /**
     * @var int
     */
    private $departureTime;

    /**
     * @var Trip
     */
    private $trip;

    /**
     * @var RouteStop
     */
    private $routeStop;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get id
     *
     * @param int $id
     *
     * @return StopTime
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Set arrivalTime
     *
     * @param int $arrivalTime
     *
     * @return StopTime
     */
    public function setArrivalTime($arrivalTime)
    {
        $this->arrivalTime = $arrivalTime;

        return $this;
    }

    /**
     * Get arrivalTime
     *
     * @return int
     */
    public function getArrivalTime()
    {
        return $this->arrivalTime;
    }

    /**
     * Set departureTime
     *
     * @param int $departureTime
     *
     * @return StopTime
     */
    public function setDepartureTime($departureTime)
    {
        $this->departureTime = $departureTime;

        return $this;
    }

    /**
     * Get departureTime
     *
     * @return int
     */
    public function getDepartureTime()
    {
        return $this->departureTime;
    }

    /**
     * Set trip
     *
     * @param Trip $trip
     *
     * @return StopTime
     */
    public function setTrip(Trip $trip = null)
    {
        $this->trip = $trip;

        return $this;
    }

    /**
     * Get trip
     *
     * @return Trip
     */
    public function getTrip()
    {
        return $this->trip;
    }

    /**
     * Get trip
     *
     * @return StopTime
     */
    public function removeTrip()
    {
        $this->trip = null;

        return $this;
    }

    /**
     * Set routeStop
     *
     * @param RouteStop $routeStop
     *
     * @return StopTime
     */
    public function setRouteStop(RouteStop $routeStop = null)
    {
        $this->routeStop = $routeStop;

        return $this;
    }

    /**
     * Get routeStop
     *
     * @return RouteStop
     */
    public function getRouteStop()
    {
        return $this->routeStop;
    }
}
