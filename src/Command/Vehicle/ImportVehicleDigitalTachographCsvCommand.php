<?php

namespace App\Command\Vehicle;

use App\Command\AbstractBaseCommand;
use App\Entity\Vehicle\Vehicle;
use App\Entity\Vehicle\VehicleDigitalTachograph;
use DateTime;
use DateTimeImmutable;
use Exception;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ImportVehicleDigitalTachographCsvCommand.
 */
class ImportVehicleDigitalTachographCsvCommand extends AbstractBaseCommand
{
    /**
     * Configure.
     */
    protected function configure(): void
    {
        $this->setName('app:import:vehicle_digital_tachograph');
        $this->setDescription('Import vehicle digital tachograph from CSV file');
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
            /** @var Vehicle $vehicle */
            $vehicle = $this->rm->getVehicleRepository()->findOneBy(['vehicleRegistrationNumber' => $this->readColumn(4, $row)]);
            $date = DateTime::createFromFormat('Y-m-d H:i:s', $this->readColumn(2, $row));
            /** @var VehicleDigitalTachograph $vehicleDigitalTachograph */
            $vehicleDigitalTachograph = $this->rm->getVehicleDigitalTachographRepository()->findOneBy(
                [
                    'vehicle' => $vehicle,
                    'createdAt' => $date,
                ]
            );
            // new vehicle digital tachograph
            if (!$vehicleDigitalTachograph) {
                $vehicleDigitalTachograph = new VehicleDigitalTachograph();
                $vehicleDigitalTachograph
                    ->setVehicle($vehicle)
                    ->setCreatedAt($date)
                ;
                ++$newRecords;
            }
            // update vehicle special permit
            $vehicleDigitalTachograph
                ->setUploadedFileName($this->readColumn(3, $row))
            ;

            $this->em->persist($vehicleDigitalTachograph);
            $this->em->flush();
            ++$rowsRead;
        }
        $endTimestamp = new DateTimeImmutable();
        // Print totals
        return $this->printTotals($output, $rowsRead, $newRecords, $beginTimestamp, $endTimestamp);
    }
}
