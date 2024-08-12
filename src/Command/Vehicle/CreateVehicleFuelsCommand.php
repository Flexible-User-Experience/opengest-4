<?php

namespace App\Command\Vehicle;

use App\Command\AbstractBaseCommand;
use App\Entity\Vehicle\VehicleFuel;
use DateTimeImmutable;
use Exception;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CreateVehicleFuelsCommand.
 *
 * @category Command
 *
 * @author   Jordi Sort <jordi.sort@mirmit.com>
 */
class CreateVehicleFuelsCommand extends AbstractBaseCommand
{
    /**
     * Configure.
     */
    protected function configure()
    {
        $this->setName('app:create:vehicle:fuels');
        $this->setDescription('Create default vehicle fuels');
        $this->addOption('dry-run', null, InputOption::VALUE_NONE, 'don\'t persist changes into database');
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
        // Set counters
        $beginTimestamp = new DateTimeImmutable();
        $rowsRead = 0;
        $newRecords = 0;
        $errors = 0;
        $newFuels = [
          ['Diésel', 1],
        ];
        foreach ($newFuels as $newFuel) {
            $vehicleFuel = $this->rm->getVehicleFuelRepository()->findOneBy([
               'name' => $newFuel[0],
            ]);
            if (!$vehicleFuel) {
                //new Record
                $vehicleFuel = new VehicleFuel();
                $vehicleFuel->setName($newFuel[0]);
                $this->em->persist($vehicleFuel);
                $this->em->flush();
            }
            ++$rowsRead;
            ++$newRecords;
        }
        if (!$input->getOption('dry-run')) {
            $this->em->flush();
        }

        // Print totals
        $endTimestamp = new DateTimeImmutable();

        return $this->printTotals($output, $rowsRead, $newRecords, $beginTimestamp, $endTimestamp, $errors, $input->getOption('dry-run'));
    }
}
