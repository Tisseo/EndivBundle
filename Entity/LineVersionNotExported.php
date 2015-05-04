<?php

namespace Tisseo\EndivBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LineVersionNotExported
 */
class LineVersionNotExported
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Tisseo\EndivBundle\Entity\ExportDestination
     */
    private $exportDestination;

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
     * Set exportDestination
     *
     * @param \Tisseo\EndivBundle\Entity\ExportDestination $exportDestination
     * @return LineVersionNotExported
     */
    public function setExportDestination(\Tisseo\EndivBundle\Entity\ExportDestination $exportDestination = null)
    {
        $this->exportDestination = $exportDestination;

        return $this;
    }

    /**
     * Get exportDestination
     *
     * @return \Tisseo\EndivBundle\Entity\ExportDestination 
     */
    public function getExportDestination()
    {
        return $this->exportDestination;
    }

    /**
     * Set lineVersion
     *
     * @param \Tisseo\EndivBundle\Entity\LineVersion $lineVersion
     * @return LineVersionNotExported
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
