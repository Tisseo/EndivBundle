<?php

namespace Tisseo\EndivBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LineVersionDatasource
 */
class LineVersionDatasource
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
     * @var \Tisseo\EndivBundle\Entity\LineVersion
     */
    private $lineVersion;


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
     * @return LineVersionDatasource
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
     * @return LineVersionDatasource
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
     * Set lineVersion
     *
     * @param \Tisseo\EndivBundle\Entity\LineVersion $lineVersion
     * @return LineVersionDatasource
     */
    public function setLineVersion(\Tisseo\EndivBundle\Entity\LineVersion $lineVersion = null)
    {
        $this->lineVersion = $lineVersion;

        return $this;
    }

    /**
     * Get lineVersion
     *
     * @return \Tisseo\EndivBundle\Entity\LineVersion 
     */
    public function getLineVersion()
    {
        return $this->lineVersion;
    }
}
