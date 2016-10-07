<?php

namespace Tisseo\EndivBundle\Services;

class NonConcurrencyManager extends AbstractManager
{
    /**
     * {inheritdoc}
     */
    public function find($nonConcurrencyId)
    {
        if ($nonConcurrencyId === null) {
            return null;
        }

        $idArray = explode("/", $nonConcurrencyId);

        return $this->findByLines($idArray[0], $idArray[1]);
    }


    /**
     * Find NonConcurrency rule by lines
     *
     * @param  integer $priorityLineId
     * @param  integer $nonPriorityLineId
     * @return array
     */
    protected function findByLines($priorityLineId, $nonPriorityLineId)
    {
        return $this->getRepository()->findOneBy(
            array(
            'priorityLine' => $priorityLineId,
            'nonPriorityLine' => $nonPriorityLineId,
            )
        );
    }
}
