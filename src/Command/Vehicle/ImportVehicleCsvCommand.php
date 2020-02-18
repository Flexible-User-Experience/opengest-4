<?php

namespace App\Command\Vehicle;

use App\Command\AbstractBaseCommand;
use App\Entity\Vehicle\Vehicle;
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
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     *
     * @throws InvalidArgumentException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Welcome & Initialization & File validations
        $fr = $this->initialValidation($input, $output);

        // Import CSV rows
        $beginTimestamp = new \DateTime();
        $rowsRead = 0;
        $newRecords = 0;
        while (false !== ($row = $this->readRow($fr))) {
            $vehicle = $this->em->getRepository('App:Vehicle\Vehicle')->findOneBy(['name' => $this->readColumn(9, $row)]);
            // new vehicle
            if (!$vehicle) {
                $vehicle = new Vehicle();
                ++$newRecords;
            }
            // update vehicle
            $vehicle
                ->setName($this->readColumn(9, $row))
                ->setDescription($this->readColumn(11, $row))
                ->setShortDescription($this->readColumn(10, $row))
            ;
            $vehicleCategory = $this->em->getRepository('App:Vehicle\VehicleCategory')->findOneBy(['name' => $this->readColumn(26, $row)]);
            if ($vehicleCategory) {
                $vehicle->setCategory($vehicleCategory);
            }
            $link = $this->readColumn(18, $row);
            if (strlen($link) > 0) {
                $vehicle->setLink($link);
            }
            $attatchmentPDF = $this->readColumn(14, $row);
            if (strlen($attatchmentPDF) > 0) {
                $vehicle->setAttatchmentPDF($attatchmentPDF);
            }
            $image = $this->readColumn(3, $row);
            if (strlen($image) > 0) {
                $vehicle->setMainImage($image);
            } else {
                $vehicle->setMainImage('1.jpg');
            }

            $this->em->persist($vehicle);
            ++$rowsRead;
        }

        $this->em->flush();
        $endTimestamp = new \DateTime();
        // Print totals
        $this->printTotals($output, $rowsRead, $newRecords, $beginTimestamp, $endTimestamp);
    }
}
