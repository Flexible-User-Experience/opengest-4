<?php

namespace App\Command\Vehicle;

use App\Command\AbstractBaseCommand;
use App\Entity\Enterprise\Enterprise;
use App\Entity\Vehicle\Vehicle;
use DateTimeImmutable;
use Exception;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class LinkVehicleEnterpriseCommand.
 *
 * @category Command
 *
 * @author   Wils Iglesias <wiglesias83@gmail.com>
 */
class LinkVehicleEnterpriseCommand extends AbstractBaseCommand
{
    /**
     * Configure.
     */
    protected function configure()
    {
        $this->setName('app:link:vehicle:enterprise');
        $this->setDescription('Link vehicle and enterprise');
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
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Welcome
        $output->writeln('<info>Welcome to "'.$this->getDescription().'" command.</info>');

        // Initializations
        $this->init();
        /** @var Enterprise $enterprise */
        $enterprise = $this->rm->getEnterpriseRepository()->findOneBy(['taxIdentificationNumber' => Enterprise::GRUAS_ROMANI_TIN]);
        if (!$enterprise) {
            $output->writeln('<error>No enterprise found</error>');
        } else {
            $vehicles = $this->rm->getVehicleRepository()->findEnabledSortedByName();
            /** @var Vehicle $vehicle */
            foreach ($vehicles as $vehicle) {
                $output->writeln($vehicle->getId().' · '.$vehicle->getName());
                $vehicle->setEnterprise($enterprise);
            }

            $this->em->flush();
        }
    }
}
