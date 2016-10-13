<?php

namespace Tisseo\EndivBundle\Services;

class SchematicManager extends AbstractManager implements CsvExportInterface
{
    private $date;

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

    public function configureForCsvExport(array $parameters = array())
    {
        if (!empty($parameters['date'])) {
            $this->date = $parameters['date'];
        }
    }

    public function getCsvExport()
    {
        if (empty($this->date)) {
            throw new \Exception('You must configure the service reference date before using export function');
        }

        $startDate = \DateTime::createFromFormat('d-m-Y', $this->date);
        $now = new \Datetime();

        $query = $this->getRepository()
            ->createQueryBuilder('s')
            ->select('s.comment as schematic_comment, s.date as schematic_date, l.number as line_number')
            ->join('s.line', 'l')
            ->where('s.date >= :startDate')
            ->orderBy('l.priority, l.number')
            ->setParameter('startDate', $startDate)
            ->getQuery();

        $content = $query->getArrayResult();
        $filename = 'schematic_'.$startDate->format('Y-m-d').'_to_'.$now->format('Y-m-d');

        return array($content, $filename);
    }
}
