<?php

namespace Tisseo\EndivBundle\Services\Ogive;

class MessageManager extends OgiveManager
{
    /**
     * Retrieve Messages
     *
     * @param bool $prehome
     *
     * @return array
     */
    public function findNetworkPublications($filterPrehome = false, $prehome = false)
    {
        $queryBuilder = $this->getRepository()->createQueryBuilder('m');
        $expr = $queryBuilder->expr();

        $queryBuilder
            ->select('m, e, o')
            ->join('m.event', 'e')
            ->join('e.objects', 'o')
            ->where($expr->lte('m.startDatetime', 'current_timestamp()'))
            ->andWhere($expr->gt('m.endDatetime', 'current_timestamp()'))
        ;

        if ($filterPrehome) {
            $queryBuilder
                ->andWhere($expr->eq('m.prehome', ':prehome'))
                ->setParameter('prehome', $prehome)
            ;
        }

        if ($filterPrehome) {
            return $queryBuilder->getQuery()->getOneOrNullResult();
        }

        return $queryBuilder->getQuery()->getResult();
    }

    public function findOldPrehome()
    {
        $queryBuilder = $this->getRepository()->createQueryBuilder('m');
        $expr = $queryBuilder->expr();

        $queryBuilder
            ->select('m', 'e')
            ->join('m.event', 'e')
            ->where($expr->lt('m.startDatetime', 'current_timestamp()'))
            ->andWhere($expr->eq('m.prehome', ':prehome'))
            ->setParameter('prehome', true)
        ;

        return $queryBuilder->getQuery()->getResult();
    }

    public function remove($message)
    {
        $message = $this->getRepository()->find($message);

        if (empty($message)) {
            throw new \Exception('The message cannot be deleted as it was not found');
        }

        $event = $message->getEvent();
        $event->setMessage();
        $this->objectManager->persist($event);
        $this->objectManager->remove($message);
        $this->objectManager->flush();
    }
}
