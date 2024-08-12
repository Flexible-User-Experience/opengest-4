<?php

namespace App\Command\Vehicle;

use App\Command\AbstractBaseCommand;
use App\Entity\Vehicle\Vehicle;
use App\Entity\Vehicle\VehicleSpecialPermit;
use DateTime;
use DateTimeImmutable;
use Exception;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ImportVehicleSpecialPermitCsvCommand.
 */
class ImportVehicleSpecialPermitCsvCommand extends AbstractBaseCommand
{
    /**
     * Configure.
     */
    protected function configure()
    {
        $this->setName('app:import:vehicle_special_permit');
        $this->setDescription('Import vehicle special permit from CSV file');
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
            $vehicle = $this->rm->getVehicleRepository()->findOneBy(['vehicleRegistrationNumber' => $this->readColumn(16, $row)]);
            $expeditionDate = DateTime::createFromFormat('Y-m-d', $this->readColumn(4, $row));
            $expiryDate = DateTime::createFromFormat('Y-m-d', $this->readColumn(5, $row));
            $expedientNumber = $this->readColumn(11, $row);
            /** @var VehicleSpecialPermit $vehicleSpecialPermit */
            $vehicleSpecialPermit = $this->rm->getVehicleSpecialPermitRepository()->findOneBy(
                [
                    'vehicle' => $vehicle,
                    'expeditionDate' => $expeditionDate,
                    'expiryDate' => $expiryDate,
                    'expedientNumber' => $expedientNumber,
                ]
            );
            // new vehicle special permit
            if (!$vehicleSpecialPermit) {
                $vehicleSpecialPermit = new VehicleSpecialPermit();
                $vehicleSpecialPermit
                    ->setVehicle($vehicle)
                    ->setExpeditionDate($expeditionDate)
                    ->setExpiryDate($expiryDate)
                    ->setExpedientNumber($expedientNumber)
                ;
                ++$newRecords;
            }
            // update vehicle special permit
            $vehicleSpecialPermit
                ->setExpedientNumber($expedientNumber)
                ->setAdditionalVehicle($this->readColumn(2, $row))
                ->setAdditionalRegistrationNumber($this->readColumn(3, $row))
                ->setTotalLength($this->readColumn(6, $row))
                ->setTotalWidth($this->readColumn(7, $row))
                ->setMaximumWeight($this->readColumn(8, $row))
                ->setNumberOfAxes($this->readColumn(9, $row))
                ->setLoadContent($this->readColumn(10, $row))
                ->setNotes($this->readColumn(12, $row))
                ->setRoute($this->readColumn(13, $row))
                ->setTotalHeight($this->readColumn(15, $row))
            ;

            $this->em->persist($vehicleSpecialPermit);
            $this->em->flush();
            ++$rowsRead;
        }
        $endTimestamp = new DateTimeImmutable();
        // Print totals
        return $this->printTotals($output, $rowsRead, $newRecords, $beginTimestamp, $endTimestamp);
    }
}
