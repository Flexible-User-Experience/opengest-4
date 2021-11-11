<?php

namespace App\Command\Vehicle;

use App\Command\AbstractBaseCommand;
use App\Entity\Vehicle\Vehicle;
use App\Entity\Vehicle\VehicleMaintenanceTask;
use DateTimeImmutable;
use Exception;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ImportVehicleMaintenanceTaskCsvCommand.
 */
class ImportVehicleMaintenanceTaskCsvCommand extends AbstractBaseCommand
{
    /**
     * Configure.
     */
    protected function configure()
    {
        $this->setName('app:import:vehicle_maintenance_task');
        $this->setDescription('Import vehicle maintenance task from CSV file');
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
            /** @var VehicleMaintenanceTask $vehicleMaintenanceTask */
            $vehicleMaintenanceTask = $this->rm->getVehicleMaintenanceTaskRepository()->findOneBy(['name' => $this->readColumn(2, $row)]);
            // new vehicle
            if (!$vehicleMaintenanceTask) {
                $vehicleMaintenanceTask = new VehicleMaintenanceTask();
                ++$newRecords;
            }
            // update vehicle
            $vehicleMaintenanceTask
                ->setName($this->readColumn(2, $row))
                ->setHours($this->readColumn(3, $row))
            ;

            $this->em->persist($vehicleMaintenanceTask);
            ++$rowsRead;
        }

        $this->em->flush();
        $endTimestamp = new DateTimeImmutable();
        // Print totals
        $this->printTotals($output, $rowsRead, $newRecords, $beginTimestamp, $endTimestamp);
    }
}
