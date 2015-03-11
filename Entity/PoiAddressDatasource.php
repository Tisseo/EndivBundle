<?php

namespace Tisseo\EndivBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PoiAddressDatasource
 */
class PoiAddressDatasource
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
     * @var \Tisseo\EndivBundle\Entity\PoiAddress
     */
    private $poiAddress;


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
     * @param string $code
     * @return PoiAddressDatasource
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
     * @param \Tisseo\EndivBundle\Entity\Datasource $datasource
     * @return PoiAddressDatasource
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
     * Set poiAddress
     *
     * @param \Tisseo\EndivBundle\Entity\PoiAddress $poiAddress
     * @return PoiAddressDatasource
     */
    public function setPoiAddress(\Tisseo\EndivBundle\Entity\PoiAddress $poiAddress = null)
    {
        $this->poiAddress = $poiAddress;

        return $this;
    }

    /**
     * Get poiAddress
     *
     * @return \Tisseo\EndivBundle\Entity\PoiAddress 
     */
    public function getPoiAddress()
    {
        return $this->poiAddress;
    }
}
