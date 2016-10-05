<?php

namespace Tisseo\EndivBundle\Types\Ogive;

class ConnectorParamType
{
    const COPY = 0;
    const RECIPIENT = 1;

    /** 
     * @var static array
     * available types for connector parameters objects
     */
    public static $connectorParamTypes = array(
        self::COPY,
        self::RECIPIENT
    );
}
