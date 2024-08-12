<?php

namespace App\Command\Vehicle;

use App\Command\AbstractBaseCommand;
use App\Entity\Vehicle\VehicleCategory;
use DateTimeImmutable;
use Exception;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ImportVehicleCategoryCsvCommand.
 */
class ImportVehicleCategoryCsvCommand extends AbstractBaseCommand
{
    /**
     * Configure.
     */
    protected function configure()
    {
        $this->setName('app:import:vehicle:category');
        $this->setDescription('Import vehicle category from CSV file');
        $this->addArgument('filename', InputArgument::REQUIRED, 'CSV file to import');
    }

    /**
     * Execute.
     *
     * @return int|void|null
     *
     * @throws InvalidArgumentException
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int|null
    {
        // Welcome & Initialization & File validations
        $fr = $this->initialValidation($input, $output);

        // Import CSV rows
        $beginTimestamp = new DateTimeImmutable();
        $rowsRead = 0;
        $newRecords = 0;
        while (false !== ($row = $this->readRow($fr))) {
            /** @var VehicleCategory $vehicleCategory */
            $vehicleCategory = $this->rm->getVehicleCategoryRepository()->findOneBy(['name' => $this->readColumn(4, $row)]);
            // new vehicle category
            if (!$vehicleCategory) {
                $vehicleCategory = new VehicleCategory();
                ++$newRecords;
            }
            // update vehicle category
            $vehicleCategory->setName($this->readColumn(4, $row));
            $this->em->persist($vehicleCategory);
            ++$rowsRead;
        }

        $this->em->flush();
        $endTimestamp = new DateTimeImmutable();
        // Print totals
        return $this->printTotals($output, $rowsRead, $newRecords, $beginTimestamp, $endTimestamp);
    }
}
