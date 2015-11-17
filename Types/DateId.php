<?php

namespace Tisseo\EndivBundle\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;

class DateId extends \DateTime 
{
    public function __toString()
    {
        return $this->format('Y-m-d');
    }
}
