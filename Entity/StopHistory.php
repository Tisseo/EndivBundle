<?php

namespace Tisseo\EndivBundle\Entity;

use Symfony\Component\Validator\ExecutionContextInterface;

/**
 * StopHistory
 */
class StopHistory
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $startDate;

    /**
     * @var \DateTime
     */
    private $endDate;

    /**
     * @var string
     */
    private $shortName;

    /**
     * @var string
     */
    private $longName;

    /**
     * @var point
     */
    private $theGeom;

    /**
     * @var string
     */
    private $ttsName;

    /**
     * @var \Tisseo\EndivBundle\Entity\Stop
     */
    private $stop;

    public function __construct(StopHistory $stopHistory = null)
    {
        $this->startDate = new \Datetime('now');
        $this->startDate->modify('+1 day');

        if ($stopHistory !== null) {
            $this->shortName = $stopHistory->getShortName();
            $this->longName = $stopHistory->getLongName();
            $this->theGeom = $stopHistory->getTheGeom();
            $this->stop = $stopHistory->getStop();
            $this->ttsName = $stopHistory->getTtsName();
        }
    }

    public function minStartDate(ExecutionContextInterface $context)
    {
        $now = new \Datetime();
        $currentStopHistory = $this->getStop()->getCurrentStopHistory($now);

        if (!empty($currentStopHistory) && $currentStopHistory->getEndDate() !== null) {
            if ($this->getStartDate() <= $currentStopHistory->getEndDate()) {
                $context->addViolationAt(
                    'startDate',
                    'stop_history.errors.min_date_end',
                    array('%date%' => $currentStopHistory->getEndDate()->format('d/m/Y')),
                    null
                );
            }
        } else {
            if ($this->getStartDate() <= $now) {
                $context->addViolationAt(
                    'startDate',
                    'stop_history.errors.min_date',
                    array('%date%' => $now->format('d/m/Y')),
                    null
                );
            }
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
     * Set startDate
     *
     * @param \DateTime $startDate
     *
     * @return StopHistory
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get startDate
     *
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set endDate
     *
     * @param \DateTime $endDate
     *
     * @return StopHistory
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get endDate
     *
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Set shortName
     *
     * @param string $shortName
     *
     * @return StopHistory
     */
    public function setShortName($shortName)
    {
        $this->shortName = $shortName;

        return $this;
    }

    /**
     * Get shortName
     *
     * @return string
     */
    public function getShortName()
    {
        return $this->shortName;
    }

    /**
     * Set longName
     *
     * @param string $longName
     *
     * @return StopHistory
     */
    public function setLongName($longName)
    {
        $this->longName = $longName;

        return $this;
    }

    /**
     * Get longName
     *
     * @return string
     */
    public function getLongName()
    {
        return $this->longName;
    }

    /**
     * Set theGeom
     *
     * @param geometry $theGeom
     *
     * @return StopHistory
     */
    public function setTheGeom($theGeom)
    {
        $this->theGeom = $theGeom;

        return $this;
    }

    /**
     * Get theGeom
     *
     * @return geometry
     */
    public function getTheGeom()
    {
        return $this->theGeom;
    }

    /**
     * Set ttsName
     *
     * @param string $ttsName
     *
     * @return StopHistory
     */
    public function setTtsName($ttsName)
    {
        $this->ttsName = $ttsName;

        return $this;
    }

    /**
     * Get ttsName
     *
     * @return string
     */
    public function getTtsName()
    {
        return $this->ttsName;
    }

    /**
     * Set stop
     *
     * @param \Tisseo\EndivBundle\Entity\Stop $stop
     *
     * @return StopHistory
     */
    public function setStop(\Tisseo\EndivBundle\Entity\Stop $stop = null)
    {
        $this->stop = $stop;

        return $this;
    }

    /**
     * Get stop
     *
     * @return \Tisseo\EndivBundle\Entity\Stop
     */
    public function getStop()
    {
        return $this->stop;
    }

    /**
     * Close Date
     *
     * @param Datetime $date
     *
     * Set the endDate with the date passed as parameter
     */
    public function closeDate(\Datetime $date)
    {
        $this->endDate = new \Datetime($date->format('Y-m-d'));
        $this->endDate->modify('-1 day');
    }
}
