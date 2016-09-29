<?php

namespace Tisseo\EndivBundle\Types;

class DateId extends \DateTime
{
    public function __toString()
    {
        return $this->format('Y-m-d');
    }
}
