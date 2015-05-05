<?php

namespace Tisseo\EndivBundle\Services;

abstract class SortManager
{
    protected function sortLineVersionsByNumber($lineVersions)
    {
        usort($lineVersions, function($val1, $val2) {
            $line1 = $val1->getLine();
            $line2 = $val2->getLine();
            if ($line1->getPriority() == $line2->getPriority())
                return strnatcmp($line1->getNumber(), $line2->getNumber());
            if ($line1->getPriority() > $line2->getPriority())
                return 1;
            if ($line1->getPriority() < $line2->getPriority())
                return -1;
        });
        return $lineVersions;
    }

    protected function sortLinesByNumber($lines)
    {
        usort($lines, function($val1, $val2) {
            if ($val1->getPriority() == $val2->getPriority())
                return strnatcmp($val1->getNumber(), $val2->getNumber());
            if ($val1->getPriority() > $val2->getPriority())
                return 1;
            if ($val1->getPriority() < $val2->getPriority())
                return -1;
        });
        return $lines;
    }

    protected function splitByPhysicalMode($data, $physicalModes)
    {
        if (empty($data) || !(method_exists($data[0], 'getPhysicalModeName')))
            return null;

        $sortedResult = array();
        foreach($physicalModes as $physicalMode)
        {
            $sortedResult[$physicalMode['name']] = array();
        }

        foreach($data as $object)
            $sortedResult[$object->getPhysicalModeName()][] = $object;
        
        return $sortedResult;
    }
}
