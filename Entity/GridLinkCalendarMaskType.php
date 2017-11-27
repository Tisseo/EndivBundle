<?php

namespace Tisseo\EndivBundle\Entity;

/**
 * GridLinkCalendarMaskType
 */
class GridLinkCalendarMaskType
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var bool
     */
    private $active;

    /**
     * @var \Tisseo\EndivBundle\Entity\GridCalendar
     */
    private $gridCalendar;

    /**
     * @var \Tisseo\EndivBundle\Entity\GridMaskType
     */
    private $gridMaskType;

    /**
     * Constructor
     *
     * @param GridCalendar $gridCalendar
     * @param GridMaskType $gridMaskType
     * @param bool active
     *
     * Create a new GridLinkCalendarMaskType.
     */
    public function __construct(GridCalendar $gridCalendar = null, GridMaskType $gridMaskType = null, $active = null)
    {
        if ($gridCalendar !== null) {
            $this->gridCalendar = $gridCalendar;
        }
        if ($gridMaskType !== null) {
            $this->gridMaskType = $gridMaskType;
        }
        if ($active !== null) {
            $this->active = $active;
        }
    }

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
     * Set active
     *
     * @param bool $active
     *
     * @return GridLinkCalendarMaskType
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return bool
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set gridCalendar
     *
     * @param \Tisseo\EndivBundle\Entity\GridCalendar $gridCalendar
     *
     * @return GridLinkCalendarMaskType
     */
    public function setGridCalendar(GridCalendar $gridCalendar = null)
    {
        $this->gridCalendar = $gridCalendar;

        return $this;
    }

    /**
     * Get gridCalendar
     *
     * @return \Tisseo\EndivBundle\Entity\GridCalendar
     */
    public function getGridCalendar()
    {
        return $this->gridCalendar;
    }

    /**
     * Set gridMaskType
     *
     * @param \Tisseo\EndivBundle\Entity\GridMaskType $gridMaskType
     *
     * @return GridLinkCalendarMaskType
     */
    public function setGridMaskType(GridMaskType $gridMaskType = null)
    {
        $this->gridMaskType = $gridMaskType;

        return $this;
    }

    /**
     * Get gridMaskType
     *
     * @return \Tisseo\EndivBundle\Entity\GridMaskType
     */
    public function getGridMaskType()
    {
        return $this->gridMaskType;
    }
}
