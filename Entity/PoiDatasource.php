<?php

namespace Tisseo\EndivBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PoiDatasource
 */
class PoiDatasource
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
     * @var \Tisseo\EndivBundle\Entity\Poi
     */
    private $poi;


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
     * @return PoiDatasource
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
     * @return PoiDatasource
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
     * Set poi
     *
     * @param  \Tisseo\EndivBundle\Entity\Poi $poi
     * @return PoiDatasource
     */
    public function setPoi(\Tisseo\EndivBundle\Entity\Poi $poi = null)
    {
        $this->poi = $poi;

        return $this;
    }

    /**
     * Get poi
     *
     * @return \Tisseo\EndivBundle\Entity\Poi
     */
    public function getPoi()
    {
        return $this->poi;
    }
}
