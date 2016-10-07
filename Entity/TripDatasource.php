<?php

namespace Tisseo\EndivBundle\Entity;

/**
 * TripDatasource
 */
class TripDatasource
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $code;

    /**
     * @var \Tisseo\EndivBundle\Entity\Datasource
     */
    private $datasource;

    /**
     * @var \Tisseo\EndivBundle\Entity\Trip
     */
    private $trip;


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
     * Set code
     *
     * @param  string $code
     * @return TripDatasource
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set datasource
     *
     * @param  \Tisseo\EndivBundle\Entity\Datasource $datasource
     * @return TripDatasource
     */
    public function setDatasource(\Tisseo\EndivBundle\Entity\Datasource $datasource = null)
    {
        $this->datasource = $datasource;

        return $this;
    }

    /**
     * Get datasource
     *
     * @return \Tisseo\EndivBundle\Entity\Datasource
     */
    public function getDatasource()
    {
        return $this->datasource;
    }

    /**
     * Set trip
     *
     * @param  \Tisseo\EndivBundle\Entity\Trip $trip
     * @return TripDatasource
     */
    public function setTrip(\Tisseo\EndivBundle\Entity\Trip $trip = null)
    {
        $this->trip = $trip;

        return $this;
    }

    /**
     * Get trip
     *
     * @return \Tisseo\EndivBundle\Entity\Trip
     */
    public function getTrip()
    {
        return $this->trip;
    }
}
