<?php

namespace Tisseo\EndivBundle\Services;

use Tisseo\EndivBundle\Entity\StopHistory;

class StopHistoryManager extends AbstractManager
{
    /**
     * Save a new StopHistory and close previous one
     *
     * @param StopHistory $stopHistory
     * @param StopHistory $latestStopHistory
     */
    public function createAndClose(
        StopHistory $stopHistory,
        StopHistory $latestStopHistory
    ) {
        // the new startDate is before some StopHistories startDate, delete them
        if ($latestStopHistory->getStartDate() >= $stopHistory->getStartDate()) {
            $youngerStopHistories = $stopHistory->getStop()->getYoungerStopHistories($stopHistory->getStartDate());
            foreach ($youngerStopHistories as $youngerStopHistory) {
                $objectManager->remove($youngerStopHistory);
            }

            $objectManager->flush();

            // updating end date
            $latestStopHistory = $stopHistory->getStop()->getLatestStopHistory();
            if ($latestStopHistory && $latestStopHistory->getEndDate() >= $stopHistory->getStartDate()) {
                $latestStopHistory->closeDate($stopHistory->getStartDate());
                $objectManager->persist($latestStopHistory);
            }
        } elseif ($latestStopHistory->getEndDate() === null) {
            $latestStopHistory->closeDate($stopHistory->getStartDate());
            $objectManager->persist($latestStopHistory);
        }

        $objectManager->persist($stopHistory);
        $objectManager->persist($stopHistory->getStop());

        $objectManager->flush();
    }
}
