<?php

namespace Tisseo\EndivBundle\Services;

use Tisseo\EndivBundle\Entity\Line;
use Tisseo\EndivBundle\Entity\LineVersion;

abstract class SortManager
{
    const SORT_BY_NUMBER = 1;
    const SORT_BY_PRIORITY_NUMBER_AND_START_OFFER = 2;

    protected function sortLineVersionsByNumber($lineVersions)
    {
        usort($lineVersions, function ($val1, $val2) {
            $line1 = $val1->getLine();
            $line2 = $val2->getLine();
            if ($line1->getPriority() == $line2->getPriority()) {
                if ($line1->getNumber() == $line2->getNumber()) {
                    return strnatcmp($val1->getVersion(), $val2->getVersion());
                } else {
                    return strnatcmp($line1->getNumber(), $line2->getNumber());
                }
            }
            if ($line1->getPriority() > $line2->getPriority()) {
                return 1;
            }
            if ($line1->getPriority() < $line2->getPriority()) {
                return -1;
            }
        });

        return $lineVersions;
    }

    /**
     * @param LineVersion[] $lineVersions or Line[] $lines
     *
     * @return \Tisseo\EndivBundle\Entity\LineVersion[] Array of lineVersion
     */
    protected function sortByPriorityNumberStartOffer($lineVersions)
    {
        usort($lineVersions, function ($val1, $val2) {
            $line1 = $val1->getLine();
            $line2 = $val2->getLine();

            if ($line1->getPriority() == $line2->getPriority()) {
                if ($line1->getNumber() == $line2->getNumber()) {
                    if ($val1->getStartDate()->getTimestamp() > $val2->getStartDate()->getTimestamp()) {
                        return 1;
                    } elseif ($val1->getStartDate()->getTimestamp() <= $val2->getStartDate()->getTimestamp()) {
                        return -1;
                    }
                } else {
                    return strnatcmp($line1->getNumber(), $line2->getNumber());
                }
            }
            if ($line1->getPriority() > $line2->getPriority()) {
                return 1;
            }
            if ($line1->getPriority() < $line2->getPriority()) {
                return -1;
            }
        });

        return $lineVersions;
    }

    protected function sortLinesByNumber($lines)
    {
        usort($lines, function ($val1, $val2) {
            if ($val1->getPriority() == $val2->getPriority()) {
                return strnatcmp($val1->getNumber(), $val2->getNumber());
            }
            if ($val1->getPriority() > $val2->getPriority()) {
                return 1;
            }
            if ($val1->getPriority() < $val2->getPriority()) {
                return -1;
            }
        });

        return $lines;
    }

    public function sortLinesByStatus($lines)
    {
        usort($lines, function ($val1, $val2) {
            $status1 = ($val1->getCurrentStatus() == null) ? null : $val1->getCurrentStatus()->getStatus();
            $status2 = ($val2->getCurrentStatus() == null) ? null : $val2->getCurrentStatus()->getStatus();

            if ($status1 == $status2) {
                if ($val1->getPriority() == $val2->getPriority()) {
                    return strnatcmp($val1->getNumber(), $val2->getNumber());
                }
                if ($val1->getPriority() > $val2->getPriority()) {
                    return 1;
                }
                if ($val1->getPriority() < $val2->getPriority()) {
                    return -1;
                }
            }
            if ($status1 == null) {
                return 1;
            }
            if ($status2 == null) {
                return -1;
            }
            if ($status1 < $status2) {
                return -1;
            }
            if ($status1 > $status2) {
                return 1;
            }
        });

        return $lines;
    }

    protected function splitByPhysicalMode($data, $physicalModes)
    {
        if (empty($data) || !(method_exists($data[0], 'getPhysicalModeName'))) {
            return null;
        }

        $sortedResult = array();
        foreach ($physicalModes as $physicalMode) {
            $sortedResult[$physicalMode['name']] = array();
        }

        foreach ($data as $object) {
            $sortedResult[$object->getPhysicalModeName()][] = $object;
        }

        foreach ($sortedResult as $key => $value) {
            if (empty($value)) {
                unset($sortedResult[$key]);
            }
        }

        return $sortedResult;
    }
}
