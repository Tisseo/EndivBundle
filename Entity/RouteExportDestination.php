<?php

namespace Tisseo\EndivBundle\Entity;

/**
 * RouteExportDestination
 */
class RouteExportDestination
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var \Tisseo\EndivBundle\Entity\ExportDestination
     */
    private $exportDestination;

    /**
     * @var \Tisseo\EndivBundle\Entity\Route
     */
    private $route;

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
     * Set exportDestination
     *
     * @param \Tisseo\EndivBundle\Entity\ExportDestination $exportDestination
     *
     * @return RouteExportDestination
     */
    public function setExportDestination(\Tisseo\EndivBundle\Entity\ExportDestination $exportDestination = null)
    {
        $this->exportDestination = $exportDestination;

        return $this;
    }

    /**
     * Get exportDestination
     *
     * @return \Tisseo\EndivBundle\Entity\ExportDestination
     */
    public function getExportDestination()
    {
        return $this->exportDestination;
    }

    /**
     * Set route
     *
     * @param \Tisseo\EndivBundle\Entity\Route $route
     *
     * @return RouteExportDestination
     */
    public function setRoute(\Tisseo\EndivBundle\Entity\Route $route = null)
    {
        $this->route = $route;

        return $this;
    }

    /**
     * Get route
     *
     * @return \Tisseo\EndivBundle\Entity\Route
     */
    public function getRoute()
    {
        return $this->route;
    }
}
