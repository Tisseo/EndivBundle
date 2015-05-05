<?php

namespace Tisseo\EndivBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LineGroupContent
 */
class LineGroupContent
{
    private $id;

    /**
     * @var boolean
     */
    private $isParent;

    /**
     * @var \Tisseo\EndivBundle\Entity\LineVersion
     */
    private $lineVersion;

    /**
     * @var \Tisseo\EndivBundle\Entity\LineGroup
     */
    private $lineGroup;


    /**
     * Set isParent
     *
     * @param boolean $isParent
     * @return LineGroupContent
     */
    public function setIsParent($isParent)
    {
        $this->isParent = $isParent;

        return $this;
    }

    /**
     * Get isParent
     *
     * @return boolean 
     */
    public function getIsParent()
    {
        return $this->isParent;
    }

    /**
     * Set lineVersion
     *
     * @param \Tisseo\EndivBundle\Entity\LineVersion $lineVersion
     * @return LineGroupContent
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

    /**
     * Set lineGroup
     *
     * @param \Tisseo\EndivBundle\Entity\LineGroup $lineGroup
     * @return LineGroupContent
     */
    public function setLineGroup(\Tisseo\EndivBundle\Entity\LineGroup $lineGroup = null)
    {
        $this->lineGroup = $lineGroup;

        return $this;
    }

    /**
     * Get lineGroup
     *
     * @return \Tisseo\EndivBundle\Entity\LineGroup 
     */
    public function getLineGroup()
    {
        return $this->lineGroup;
    }
}
