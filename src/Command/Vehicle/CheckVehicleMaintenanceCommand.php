<?php

namespace App\Command\Vehicle;

use App\Command\AbstractBaseCommand;
use App\Entity\Vehicle\VehicleMaintenance;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CheckVehicleMaintenanceCommand.
 *
 * @category Command
 *
 * @author   Jordi Sort <jordi.sort@mirmit.com>
 */
class CheckVehicleMaintenanceCommand extends AbstractBaseCommand
{
    /**
     * Configure.
     */
    protected function configure()
    {
        $this->setName('app:vehicle:check_maintenance');
        $this->setDescription('Check vehicle maintenances');
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
        // Welcome
        $output->writeln('<info>Welcome to "'.$this->getDescription().'" command.</info>');

        // Initializations
        $this->init();
        $needMainenance = 0;
        /** @var VehicleMaintenance[] $vehicleMaintenances */
        $vehicleMaintenances = $this->rm->getVehicleMaintenanceRepository()->findBy(
            ['enabled' => true,
            'needsCheck' => false, ]
        );
        foreach ($vehicleMaintenances as $vehicleMaintenance) {
            $needsCheck = $this->vmm->checkIfMaintenanceNeedsCheck($vehicleMaintenance);
            if ($needsCheck) {
                $vehicleMaintenance->setNeedsCheck(true);
                $this->em->persist($vehicleMaintenance);
                ++$needMainenance;
            }
        }
        $this->em->flush();
        $output->writeln('<info>'.$needMainenance.' vehicles need new maintenance.</info>');

        return Command::SUCCESS;
    }
}
