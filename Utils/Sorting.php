<?php

namespace Tisseo\EndivBundle\Utils;

use Tisseo\EndivBundle\Entity\LineVersion;
use Tisseo\EndivBundle\Entity\Line;

class Sorting
{
    const SPLIT_LINE = 'line';
    const SPLIT_LINE_VERSION = 'line_version';

    private static $splitModes = array(
        self::SPLIT_LINE,
        self::SPLIT_LINE_VERSION
    );

    public static function sortLineVersionsByNumber($lineVersions)
    {
        usort(
            $lineVersions, function(LineVersion $val1, LineVersion $val2) {
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
            }
        );

        return $lineVersions;
    }

    public static function sortLinesByNumber($lines)
    {
        usort(
            $lines, function($val1, $val2) {
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
        );

        return $lines;
    }

    public static function sortLinesByStatus($lines)
    {
        usort(
            $lines, function (Line $val1, Line $val2) {
                $status1 = ($val1->getCurrentStatus() === null) ? null : $val1->getCurrentStatus()->getStatus();
                $status2 = ($val2->getCurrentStatus() === null) ? null : $val2->getCurrentStatus()->getStatus();

                if ($status1 === $status2) {
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
                if ($status1 === null) {
                    return 1;
                }
                if ($status2 === null) {
                    return -1;
                }
                if ($status1 < $status2) {
                    return -1;
                }
                if ($status1 > $status2) {
                    return 1;
                }
            }
        );

        return $lines;
    }

    public static function splitByPhysicalMode($data, $type, $modes)
    {
        if (empty($data)) {
            return array();
        }

        if (!in_array($type, self::$splitModes)) {
            throw new \Exception(sprintf("Cannot split this type of data: %s", $type));
        }

        foreach ($data as $object) {
            if ($type === self::SPLIT_LINE_VERSION) {
                $modes[$object->getLine()->getPhysicalMode()->getName()][] = $object;
            } else if ($type === self::SPLIT_LINE) {
                $modes[$object->getPhysicalMode()->getName()][] = $object;
            }
        }

        return $modes;
    }
}
