<?php

namespace Tisseo\EndivBundle\Entity;

/**
 * LineDatasource
 */
class LineDatasource
{
    /**
     * @var int
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
     * @var \Tisseo\EndivBundle\Entity\Line
     */
    private $line;

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
     * Set code
     *
     * @param string $code
     *
     * @return LineDatasource
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
     *
     * @return LineDatasource
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
     * Set line
     *
     * @param \Tisseo\EndivBundle\Entity\Line $line
     *
     * @return LineDatasource
     */
    public function setLine(\Tisseo\EndivBundle\Entity\Line $line = null)
    {
        $this->line = $line;

        return $this;
    }

    /**
     * Get line
     *
     * @return \Tisseo\EndivBundle\Entity\Line
     */
    public function getLine()
    {
        return $this->line;
    }
}
