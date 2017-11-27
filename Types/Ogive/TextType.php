<?php

namespace Tisseo\EndivBundle\Types\Ogive;

class TextType
{
    const OBJECT = 1;
    const CONTENT = 2;
    const ATTACHEMENT = 3;

    /**
     * @var static array
     *             available types for text objects
     */
    public static $textTypes = array(
        self::OBJECT,
        self::CONTENT,
        self::ATTACHEMENT
    );
}
