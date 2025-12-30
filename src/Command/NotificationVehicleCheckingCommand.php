<?php

namespace App\Command;

use App\Entity\Vehicle\VehicleChecking;
use Exception;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

/**
 * Class NotificationVehicleCheckingCommand.
 *
 * @category Command
 *
 * @author   Wils Iglesias <wiglesias83@gmail.com>
 */
class NotificationVehicleCheckingCommand extends AbstractBaseCommand
{
    /**
     * Configure.
     */
    protected function configure(): void
    {
        $this->setName('app:notification:vehicle-checking');
        $this->setDescription('Send vehicle checking notification before to be invalid');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     *
     * @throws TransportExceptionInterface;
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int|null
    {
        // Welcome
        $output->writeln('<info>Welcome to "'.$this->getName().'" command.</info>');

        // Get invailid entities
        $vcr = $this->rm->getVehicleCheckingRepository();
        $entities = $vcr->getItemsInvalidByEnabledVehicle();
        $output->writeln('<comment>Invalid entities</comment>');
        /** @var VehicleChecking $entity */
        foreach ($entities as $entity) {
            $output->writeln($entity->getId().' '.$entity->getVehicle()->getName().' '.$entity->getEnd()->format('d-m-Y'));
        }
        if (count($entities) > 0) {
            $this->ns->sendVehicleCheckingInvalidNotification($entities);
        }

        // Get before to be invalid entities
        $entities = $vcr->getItemsBeforeToBeInvalidByEnabledVehicle();
        $output->writeln('<comment>Before to be invalid entities</comment>');
        /** @var VehicleChecking $entity */
        foreach ($entities as $entity) {
            $output->writeln($entity->getId().' '.$entity->getVehicle()->getName().' '.$entity->getEnd()->format('d-m-Y'));
        }
        if (count($entities) > 0) {
            $this->ns->sendVehicleCheckingBeforeToBeInvalidNotification($entities);
        }
    }
}
