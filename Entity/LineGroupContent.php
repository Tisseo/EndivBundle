<?php

namespace Tisseo\EndivBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * LineGroupContent
 */
class LineGroupContent
{
    private $id;

    /**
     * @var boolean
     */
    private $parent;

    /**
     * @var \Tisseo\EndivBundle\Entity\LineVersion
     */
    private $lineVersion;

    /**
     * @var \Tisseo\EndivBundle\Entity\LineGroup
     */
    private $lineGroup;

    /**
     * Get Id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get childLines
     *
     * @return Doctrine\Common\Collections\ArrayCollection
     */
    public function getChildLines()
    {
        if ($this->parent) {
            $childLines = new ArrayCollection();

            foreach ($this->lineGroup->getLineGroupContents() as $otherGroupContent) {
                if ($otherGroupContent !== $this) {
                    $childLines[] = $otherGroupContent->getLineVersion();
                }
            }

            return $childLines;
        }

        return null;
    }

    /**
     * Set parent
     *
     * @param  boolean $parent
     * @return LineGroupContent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Is parent
     *
     * @return boolean
     */
    public function isParent()
    {
        return $this->parent;
    }

    /**
     * Set lineVersion
     *
     * @param  \Tisseo\EndivBundle\Entity\LineVersion $lineVersion
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
     * @param  \Tisseo\EndivBundle\Entity\LineGroup $lineGroup
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
