<?php

namespace App\Command\Vehicle;

use App\Command\AbstractBaseCommand;
use App\Entity\Vehicle\Vehicle;
use App\Entity\Vehicle\VehicleCheckingType;
use DateTimeImmutable;
use Exception;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ImportVehicleCheckingTypeCsvCommand.
 */
class ImportVehicleCheckingTypeCsvCommand extends AbstractBaseCommand
{
    /**
     * Configure.
     */
    protected function configure()
    {
        $this->setName('app:import:vehicle_checking:type');
        $this->setDescription('Import vehicle checking types from CSV file');
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
            /** @var VehicleCheckingType $vehicleCheckingType */
            $vehicleCheckingType = $this->rm->getVehicleCheckingTypeRepository()->findOneBy(
                ['name' => $this->readColumn(1, $row)]
            );
            // new vehicle checking type
            if (!$vehicleCheckingType) {
                $vehicleCheckingType = new VehicleCheckingType();
                $vehicleCheckingType
                    ->setName($this->readColumn(1, $row))
                ;
                ++$newRecords;
            }

            $this->em->persist($vehicleCheckingType);
            $this->em->flush();
            ++$rowsRead;
        }
        $endTimestamp = new DateTimeImmutable();
        // Print totals
        return $this->printTotals($output, $rowsRead, $newRecords, $beginTimestamp, $endTimestamp);
    }
}
