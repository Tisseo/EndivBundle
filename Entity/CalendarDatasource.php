<?php

namespace Tisseo\EndivBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CalendarDatasource
 */
class CalendarDatasource
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
     * @var \Tisseo\EndivBundle\Entity\Calendar
     */
    private $calendar;

    /**
     * @var \Tisseo\EndivBundle\Entity\Datasource
     */
    private $datasource;


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
     * @return CalendarDatasource
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
     * Set calendar
     *
     * @param  \Tisseo\EndivBundle\Entity\Calendar $calendar
     * @return CalendarDatasource
     */
    public function setCalendar(\Tisseo\EndivBundle\Entity\Calendar $calendar = null)
    {
        $this->calendar = $calendar;

        return $this;
    }

    /**
     * Get calendar
     *
     * @return \Tisseo\EndivBundle\Entity\Calendar
     */
    public function getCalendar()
    {
        return $this->calendar;
    }

    /**
     * Set datasource
     *
     * @param  \Tisseo\EndivBundle\Entity\Datasource $datasource
     * @return CalendarDatasource
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
}
