<?php

namespace Tisseo\EndivBundle\Entity;


/**
 * Datasource
 */
class Datasource
{
    const HASTUS_SRC = 'HASTUS';
    const DATA_SRC = 'Service Données';
    const IV_SRC = 'Information Voyageurs';
    const TIGRE_SRC = 'TIGRE';

    public static $datasources = array(
        self::HASTUS_SRC,
        self::DATA_SRC,
        self::IV_SRC,
        self::TIGRE_SRC
    );

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var \Tisseo\EndivBundle\Entity\Agency
     */
    private $agency;


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
     * Set name
     *
     * @param  string $name
     * @return Datasource
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
     * Set agency
     *
     * @param  \Tisseo\EndivBundle\Entity\Agency $agency
     * @return Datasource
     */
    public function setAgency(\Tisseo\EndivBundle\Entity\Agency $agency = null)
    {
        $this->agency = $agency;

        return $this;
    }

    /**
     * Get agency
     *
     * @return \Tisseo\EndivBundle\Entity\Agency
     */
    public function getAgency()
    {
        return $this->agency;
    }
}
