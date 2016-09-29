<?php

namespace Tisseo\EndivBundle\Entity;


/**
 * LineGroupGisContent
 */
class LineGroupGisContent
{
    /**
     * @var \Tisseo\EndivBundle\Entity\Line
     */
    private $line;

    /**
     * @var \Tisseo\EndivBundle\Entity\LineGroupGis
     */
    private $lineGroupGis;

    /**
     * Set line
     *
     * @param  \Tisseo\EndivBundle\Entity\Line $line
     * @return LineGroupGisContent
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

    /**
     * Set lineGroupGis
     *
     * @param  \Tisseo\EndivBundle\Entity\LineGroupGis $lineGroupGis
     * @return LineGroupGisContent
     */
    public function setLineGroupGis(\Tisseo\EndivBundle\Entity\LineGroupGis $lineGroupGis = null)
    {
        $this->lineGroupGis = $lineGroupGis;
        return $this;
    }

    /**
     * Get lineGroupGis
     *
     * @return \Tisseo\EndivBundle\Entity\LineGroupGis
     */
    public function getLineGroupGis()
    {
        return $this->lineGroupGis;
    }
}
