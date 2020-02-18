<?php

namespace App\Command\Vehicle;

use App\Command\AbstractBaseCommand;
use App\Entity\Vehicle\VehicleCategory;
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
            $vehicleCategory = $this->em->getRepository('App:Vehicle\VehicleCategory')->findOneBy(['name' => $this->readColumn(4, $row)]);
            // new vehicle category
            if (!$vehicleCategory) {
                $vehicleCategory = new VehicleCategory();
                ++$newRecords;
            }
            // update vehicle category
            $vehicleCategory
                ->setName($this->readColumn(4, $row))
            ;
            $this->em->persist($vehicleCategory);
            ++$rowsRead;
        }

        $this->em->flush();
        $endTimestamp = new \DateTime();
        // Print totals
        $this->printTotals($output, $rowsRead, $newRecords, $beginTimestamp, $endTimestamp);
    }
}
