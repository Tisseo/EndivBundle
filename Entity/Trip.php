<?php

namespace Tisseo\EndivBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;

/**
 * Trip
 */
class Trip extends ObjectDatasource
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
    private $isPattern;

    /**
     * @var Trip
     */
    private $pattern;

    /**
     * @var Comment
     */
    private $comment;

    /**
     * @var Route
     */
    private $route;

    /**
     * @var TripCalendar
     */
    private $tripCalendar;

    /**
     * @var Calendar
     */
    private $periodCalendar;

    /**
     * @var Calendar
     */
    private $dayCalendar;

    /**
     * @var Collection
     */
    private $tripDatasources;

    /**
     * @var Collection
     */
    private $stopTimes;

    /**
     * @var integer
     */
    private $parent;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->tripDatasources = new ArrayCollection();
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
     * Get id
     *
     * @param  integer $id
     * @return Trip
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Set name
     *
     * @param  string $name
     * @return Trip
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
     * Get parent
     *
     * @return integer
     */

    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set parent
     *
     * @return Trip
     */

    public function setParent($parent)
    {
        $this->parent = $parent;

        return $this;
    }


    /**
     * Set isPattern
     *
     * @param  boolean $isPattern
     * @return Trip
     */
    public function setIsPattern($isPattern)
    {
        $this->isPattern = $isPattern;

        return $this;
    }

    /**
     * Get isPattern
     *
     * @return boolean
     */
    public function getIsPattern()
    {
        return $this->isPattern;
    }

    /**
     * Set pattern
     *
     * @param  Trip $pattern
     * @return Trip
     */
    public function setPattern($pattern)
    {
        $this->pattern = $pattern;

        return $this;
    }

    /**
     * Get pattern
     *
     * @return Trip
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * Set comment
     *
     * @param  Comment $comment
     * @return Trip
     */
    public function setComment(Comment $comment = null)
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
     * Set route
     *
     * @param  Route $route
     * @return Trip
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
     * Set tripCalendar
     *
     * @param  TripCalendar $tripCalendar
     * @return Trip
     */
    public function setTripCalendar(TripCalendar $tripCalendar = null)
    {
        $this->tripCalendar = $tripCalendar;

        return $this;
    }

    /**
     * Get tripCalendar
     *
     * @return TripCalendar
     */
    public function getTripCalendar()
    {
        return $this->tripCalendar;
    }

    /**
     * Set periodCalendar
     *
     * @param  Calendar $periodCalendar
     * @return Trip
     */
    public function setPeriodCalendar(Calendar $periodCalendar = null)
    {
        $this->periodCalendar = $periodCalendar;

        return $this;
    }

    /**
     * Get periodCalendar
     *
     * @return Calendar
     */
    public function getPeriodCalendar()
    {
        return $this->periodCalendar;
    }

    /**
     * Set dayCalendar
     *
     * @param  Calendar $dayCalendar
     * @return Trip
     */
    public function setDayCalendar(Calendar $dayCalendar = null)
    {
        $this->dayCalendar = $dayCalendar;

        return $this;
    }

    /**
     * Get dayCalendar
     *
     * @return Calendar
     */
    public function getDayCalendar()
    {
        return $this->dayCalendar;
    }

    /**
     * Set stopTimes
     *
     * @param  Collection $stopTimes
     * @return Trip
     */
    public function setStopTimes(Collection $stopTimes)
    {
        $this->stopTimes = $stopTimes;
        foreach ($this->stopTimes as $stopTime) {
            $stopTime->setTrip($this);
        }
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
     * Add stopTime
     *
     * @param  StopTime $stopTime
     * @return Trip
     */
    public function addStopTime(StopTime $stopTime)
    {
        $this->stopTimes[] = $stopTime;

        return $this;
    }

    /**
     * Remove stopTime
     *
     * @param StopTime $stopTime
     */
    public function removeStopTime(StopTime $stopTime)
    {
        $this->stopTimes->removeElement($stopTime);
    }

    /**
 * Clear stopTimes
     *
     * @return Trip
     */
    public function clearStopTimes()
    {
        $this->stopTimes->clear();
    }

    /**
     * Set tripDatasources
     *
     * @param  Collection $tripDatasources
     * @return Trip
     */
    public function setTripDatasources(Collection $tripDatasources)
    {
        $this->tripDatasources = $tripDatasources;
        foreach ($this->tripDatasources as $tripDatasource) {
            $tripDatasource->setTrip($this);
        }
        return $this;
    }

    /**
     * Get tripDatasources
     *
     * @return Collection
     */
    public function getTripDatasources()
    {
        return $this->tripDatasources;
    }

    /**
     * Add tripDatasource
     *
     * @param  \Tisseo\EndivBundle\Entity\TripDatasource $tripDatasources
     * @return Trip
     */
    public function addTripDatasource(\Tisseo\EndivBundle\Entity\TripDatasource $tripDatasource)
    {
        $this->tripDatasources->add($tripDatasource);
        $tripDatasource->setTrip($this);

        return $this;
    }

    /**
     * Remove tripDatasource
     *
     * @param \Tisseo\EndivBundle\Entity\TripDatasource $tripDatasources
     */
    public function removeTripDatasource(\Tisseo\EndivBundle\Entity\TripDatasource $tripDatasource)
    {
        $this->tripDatasources->removeElement($tripDatasource);
    }

    /**
 * Criteria functions
**/

    /**
     * Getting a specific StopTime using a RouteStop
     *
     * @param RouteStop $routeStop
     */
    public function getStopTime(RouteStop $routeStop)
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('routeStop', $routeStop))
            ->setMaxResults(1);

        return $this->stopTimes->matching($criteria)->first();
    }

    /**
     * Getting the first StopTime using arrivalTime
     */
    public function getFirstStopTime()
    {
        $criteria = Criteria::create()
            ->orderBy(array('arrivalTime' => Criteria::ASC))
            ->setMaxResults(1);

        return $this->stopTimes->matching($criteria)->first();
    }

    public function __toString()
    {
        return $this->name;
    }
}
