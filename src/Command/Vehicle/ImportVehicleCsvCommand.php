<?php

namespace App\Command\Vehicle;

use App\Command\AbstractBaseCommand;
use App\Entity\Enterprise\Enterprise;
use App\Entity\Vehicle\Vehicle;
use App\Entity\Vehicle\VehicleCategory;
use DateTimeImmutable;
use Exception;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ImportVehicleCsvCommand.
 */
class ImportVehicleCsvCommand extends AbstractBaseCommand
{
    /**
     * Configure.
     */
    protected function configure()
    {
        $this->setName('app:import:vehicle');
        $this->setDescription('Import vehicle from CSV file');
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
        // Create default vehicle category if it does not exist
        /** @var VehicleCategory $vehicleCategory */
        $vehicleCategory = $this->rm->getVehicleCategoryRepository()->findOneBy(['name' => 'Default']);
        // new vehicle category
        if (!$vehicleCategory) {
            $vehicleCategory = new VehicleCategory();
            $vehicleCategory->setName('Default');
            $this->em->persist($vehicleCategory);
        }
        while (false !== ($row = $this->readRow($fr))) {
            /** @var Vehicle $vehicle */
            $vehicle = $this->rm->getVehicleRepository()->findOneBy(['name' => $this->readColumn(4, $row)]);
            // new vehicle
            if (!$vehicle) {
                $vehicle = new Vehicle();
                ++$newRecords;
            }
            // update vehicle
            $vehicle
                ->setName($this->readColumn(4, $row))
                ->setVehicleRegistrationNumber($this->readColumn(3, $row))
                ->setChassisBrand($this->readColumn(7, $row))
                ->setChassisNumber($this->readColumn(9, $row))
                ->setVehicleBrand($this->readColumn(6, $row))
                ->setVehicleModel($this->readColumn(8, $row))
                ->setSerialNumber($this->readColumn(10, $row))
            ;
            /** @var Enterprise $enterprise */
            $enterprise = $this->rm->getEnterpriseRepository()->findOneBy(['id' => $this->readColumn(1, $row)]);
            if ($enterprise) {
                $vehicle->setEnterprise($enterprise);
            }
            $vehicle->setCategory($vehicleCategory);

            $image = $this->readColumn(15, $row);
            if (strlen($image) > 0) {
                $image = str_replace('vehiculos/', '', $image);
                $image = str_replace('imagen_grua/', '', $image);
                $vehicle->setMainImage($image);
            } else {
                $vehicle->setMainImage(null);
            }

            $this->em->persist($vehicle);
            ++$rowsRead;
        }

        $this->em->flush();
        $endTimestamp = new DateTimeImmutable();
        // Print totals
        $this->printTotals($output, $rowsRead, $newRecords, $beginTimestamp, $endTimestamp);
    }
}
