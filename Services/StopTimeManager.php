<?php

namespace Tisseo\EndivBundle\Services;

class StopTimeManager extends AbstractManager
{
    /**
     * Find times and RouteStop id using a Trip id
     *
     * @param  integer $tripId
     * @return Doctrine\Common\Collections\Collection
     */
    public function findTimesAndRouteStopByTrip($tripId)
    {
        $query = $this->getRepository()->createQueryBuilder('st')
            ->select('st.departureTime, st.arrivalTime, IDENTITY(st.routeStop) as routestop')
            ->where('st.trip = :trip')
            ->setParameter("trip", $tripId)
            ->getQuery();

        return $query->getResult();
    }
}
