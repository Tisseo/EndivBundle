<?php

namespace Tisseo\EndivBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Datasource
 */
class Datasource
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
     * @param string $name
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
     * @param \Tisseo\EndivBundle\Entity\Agency $agency
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
