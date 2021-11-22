<?php

namespace App\Command\Vehicle;

use App\Command\AbstractBaseCommand;
use App\Entity\Vehicle\Vehicle;
use App\Entity\Vehicle\VehicleChecking;
use App\Entity\Vehicle\VehicleCheckingType;
use App\Entity\Vehicle\VehicleMaintenance;
use DateTime;
use DateTimeImmutable;
use Exception;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ImportVehicleCheckingCsvCommand.
 */
class ImportVehicleCheckingCsvCommand extends AbstractBaseCommand
{
    /**
     * Configure.
     */
    protected function configure()
    {
        $this->setName('app:import:vehicle_checking');
        $this->setDescription('Import vehicle checking from CSV file');
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
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Welcome & Initialization & File validations
        $fr = $this->initialValidation($input, $output);

        // Import CSV rows
        $beginTimestamp = new DateTimeImmutable();
        $rowsRead = 0;
        $newRecords = 0;
        while (false !== ($row = $this->readRow($fr))) {
            /** @var Vehicle $vehicle */
            $vehicle = $this->rm->getVehicleRepository()->findOneBy(['vehicleRegistrationNumber' => $this->readColumn(6, $row)]);
            /** @var VehicleCheckingType $vehicleCheckingType */
            $vehicleCheckingType = $this->rm->getVehicleCheckingTypeRepository()->findOneBy(['name' => $this->readColumn(5, $row)]);
            $begin = DateTime::createFromFormat('Y-m-d', $this->readColumn(3, $row));
            $end = DateTime::createFromFormat('Y-m-d', $this->readColumn(4, $row));
            /** @var VehicleMaintenance $vehicleMaintenance */
            $vehicleChecking = $this->rm->getVehicleCheckingRepository()->findOneBy(
                [
                    'vehicle' => $vehicle,
                    'type' => $vehicleCheckingType,
                    'begin' => $begin,
                    'end' => $end,
                ]
            );
            // new vehicle maintenance
            if (!$vehicleChecking) {
                $vehicleChecking = new VehicleChecking();
                $vehicleChecking
                    ->setVehicle($vehicle)
                    ->setType($vehicleCheckingType)
                    ->setBegin($begin)
                    ->setEnd($end)
                ;
                ++$newRecords;
            }

            $this->em->persist($vehicleChecking);
            $this->em->flush();
            ++$rowsRead;
        }
        $endTimestamp = new DateTimeImmutable();
        // Print totals
        $this->printTotals($output, $rowsRead, $newRecords, $beginTimestamp, $endTimestamp);
    }
}
