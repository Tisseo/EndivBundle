<?php

namespace Tisseo\EndivBundle\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Tisseo\EndivBundle\Types\DateId;

class DateIdType extends \Doctrine\DBAL\Types\DateType
{
    public function getName()
    {
        return 'date_id';        
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        $date = parent::convertToPHPValue($value, $platform);

        if ( ! $date) {
            return $date;
        }

        return new DateId($date->format('Y-m-d'));
    }
}


