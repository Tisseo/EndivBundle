<?php

namespace Tisseo\EndivBundle\Services;

class SchematicManager extends AbstractManager
{
    /**
     * Find multiple by Id
     *
     * @param array $schematicIds
     */
    public function findMultipleById(array $schematicIds)
    {
        return $this->getRepository->find($schematicIds);
    }

    /**
     * Update group gis
     *
     * @param array   $schematicIds
     * @param boolean $groupGis
     *
     * Updating groupGis attribute for specified Schematics.
     */
    public function updateGroupGis(array $schematics, $groupGis)
    {
        $objectManager = $this->getObjectManager();
        $schematicsCollection = $this->findMultipleById($schematics);

        $sync = false;
        foreach ($schematicsCollection as $schematic) {
            if ($schematic->getGroupGis() !== $groupGis) {
                $upSchematics = $this->getRepository->findBy(array('line' => $schematic->getLine()));
                foreach ($upSchematics as $upSchematic) {
                    $upSchematic->setGroupGis(!$groupGis);
                    $objectManager->persist($upSchematic);
                }

                $schematic->setGroupGis($groupGis);

                $objectManager->persist($schematic);
                $sync = true;
            }
        }

        if ($sync) {
            $objectManager->flush();
        }
    }

    public function getCsvExport($date)
    {
        $startDate = \DateTime::createFromFormat('d-m-Y', $date);
        $now = new \Datetime();
        $sql ="
            SELECT
                l.number as line_number,
                s.date as schematic_date,
                s.comment as schematic_comment
            FROM Tisseo\EndivBundle\Entity\Schematic s
            JOIN s.line l
            WHERE s.date >= :startDate
            ORDER BY l.priority, l.number
        ";

        $query = $this->getObjectManager()->createQuery($sql)
            ->setParameter('startDate', $startDate);

        $content = $query->getArrayResult();
        $filename = 'schematic_'.$startDate->format('Y-m-d').'_to_'.$now->format('Y-m-d');

        return array($content, $filename);
    }
}
