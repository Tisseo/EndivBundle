<?php

namespace Tisseo\EndivBundle\Services\Ogive;

class TextManager extends OgiveManager
{
    /**
     * Customized findAll
     *
     * @return array
     */
    public function findAll()
    {
        return $this->getRepository()->findBy(array(), array('label' => 'ASC'));
    }
}
