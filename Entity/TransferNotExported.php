<?php

namespace Tisseo\EndivBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TransferNotExported
 */
class TransferNotExported
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
     * @var \Tisseo\EndivBundle\Entity\Transfer
     */
    private $transfer;


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
     * @return TransferNotExported
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
     * Set transfer
     *
     * @param \Tisseo\EndivBundle\Entity\Transfer $transfer
     * @return TransferNotExported
     */
    public function setTransfer(\Tisseo\EndivBundle\Entity\Transfer $transfer = null)
    {
        $this->transfer = $transfer;

        return $this;
    }

    /**
     * Get transfer
     *
     * @return \Tisseo\EndivBundle\Entity\Transfer 
     */
    public function getTransfer()
    {
        return $this->transfer;
    }
}
