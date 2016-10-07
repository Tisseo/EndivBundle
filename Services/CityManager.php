<?php

namespace Tisseo\EndivBundle\Services;

class CityManager extends AbstractManager
{
    /**
     * Find a city usinglike function on its name
     *
     * @param  $term
     * @return array
     */
    public function findCityLike($term)
    {
        // cleaning special characters
        $term = str_replace(array("-", " ", "'"), "_", $term);

        $query = $this->createLikeQueryBuilder(array('name', 'insee'), $term);
        $query->orderBy('o.name');

        $shs = $query->getQuery()->getResult();
        $array = array();
        foreach ($shs as $sh) {
            $label = $sh["name"]." (".$sh["insee"].")";
            $array[] = array("name"=>$label, "id"=>$sh["id"]);
        }

        return $array;
    }
}
