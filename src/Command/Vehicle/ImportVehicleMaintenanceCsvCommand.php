<?php

namespace App\Command\Vehicle;

use App\Command\AbstractBaseCommand;
use App\Entity\Vehicle\Vehicle;
use App\Entity\Vehicle\VehicleMaintenance;
use App\Entity\Vehicle\VehicleMaintenanceTask;
use DateTime;
use DateTimeImmutable;
use Exception;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ImportVehicleMaintenanceCsvCommand.
 */
class ImportVehicleMaintenanceCsvCommand extends AbstractBaseCommand
{
    /**
     * Configure.
     */
    protected function configure()
    {
        $this->setName('app:import:vehicle_maintenance');
        $this->setDescription('Import vehicle maintenances from CSV file');
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
            $vehicle = $this->rm->getVehicleRepository()->findOneBy(['vehicleRegistrationNumber' => $this->readColumn(7, $row)]);
            /** @var VehicleMaintenanceTask $vehicleMaintenanceTask */
            $vehicleMaintenanceTask = $this->rm->getVehicleMaintenanceTaskRepository()->findOneBy(['name' => $this->readColumn(8, $row)]);
            $date = DateTime::createFromFormat('Y-m-d', $this->readColumn(3, $row));
            /** @var VehicleMaintenance $vehicleMaintenance */
            $vehicleMaintenance = $this->rm->getVehicleMaintenanceRepository()->findOneBy(
                [
                    'vehicle' => $vehicle,
                    'vehicleMaintenanceTask' => $vehicleMaintenanceTask,
                    'date' => $date,
                ]
            );
            // new vehicle maintenance
            if (!$vehicleMaintenance) {
                $vehicleMaintenance = new VehicleMaintenance();
                $vehicleMaintenance
                    ->setVehicle($vehicle)
                    ->setVehicleMaintenanceTask($vehicleMaintenanceTask)
                    ->setDate($date)
                ;
                ++$newRecords;
            }
            // update vehicle maintenance
            $vehicleMaintenance
                ->setDescription($this->readColumn(4, $row))
            ;

            $this->em->persist($vehicleMaintenance);
            $this->em->flush();
            ++$rowsRead;
        }
        $endTimestamp = new DateTimeImmutable();
        // Print totals
        $this->printTotals($output, $rowsRead, $newRecords, $beginTimestamp, $endTimestamp);
    }
}
