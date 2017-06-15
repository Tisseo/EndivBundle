<?php
namespace Tisseo\EndivBundle\Services\Ogive;

use Tisseo\EndivBundle\Entity\Ogive\Object as OgiveObject;

class MessageManager extends OgiveManager
{
    /**
     * Retrieve Messages
     *
     * @param  boolean $prehome
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

    public function remove($identifier)
    {
        $message = $this->getRepository()->find($identifier);

        if (empty($message)) {
            throw new Exception("The message {$identifier} was not found");
        }

        $event = $message->getEvent();
        $event->setMessage();
        $this->objectManager->persist($event);
        $this->objectManager->remove($message);
        $this->objectManager->flush();
    }
}
