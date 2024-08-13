<?php

namespace App\Command\Vehicle;

use App\Command\AbstractBaseCommand;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\Console\Command\Command;
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
    protected function configure(): void
    {
        $this->setName('app:vehicle:check_maintenance');
        $this->setDescription('Check vehicle maintenances');
    }

    /**
     * Execute.
     *
     * @throws NonUniqueResultException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Welcome
        $output->writeln('<info>Welcome to "'.$this->getDescription().'" command.</info>');

        // Initializations
        $this->init();
        $numberOfMaintenances = $this->vmm->checkVehicleMaintenance();
        $output->writeln('<info>'.$numberOfMaintenances.' vehicles need new maintenance.</info>');

        return Command::SUCCESS;
    }
}
