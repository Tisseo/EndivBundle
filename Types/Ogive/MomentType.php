<?php

namespace Tisseo\EndivBundle\Types\Ogive;

class MomentType
{
    const BEFORE = 0;
    const NOW = 1;
    const AFTER = 2;

    public static $momentTypes = array(
        self::BEFORE => self::BEFORE,
        self::NOW => self::NOW,
        self::AFTER => self::AFTER
    );
}
