<?php

namespace Tisseo\EndivBundle\Types\Ogive;

class ConnectorParamType
{
    // It seems that connector param types aren't needed anymore
    // I let it be for now but we could remove it in the future
    const MAIL = 0;

    /** 
     * @var static array
     * available types for connector parameters objects
     */
    public static $connectorParamTypes = array(
        self::MAIL
    );
}
