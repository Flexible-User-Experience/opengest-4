<?php

namespace App\Command;

use App\Entity\Operator\OperatorChecking;
use Exception;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

/**
 * Class NotificationOperatorCheckingCommand.
 */
class NotificationOperatorCheckingCommand extends AbstractBaseCommand
{
    /**
     * Configure.
     */
    protected function configure(): void
    {
        $this->setName('app:notification:operator-checking');
        $this->setDescription('Send operator checking notification before to be invalid');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     *
     * @throws TransportExceptionInterface
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int|null
    {
        // Welcome
        $output->writeln('<info>Welcome to "'.$this->getName().'" command.</info>');

        // Get invalid entities
        $ocr = $this->rm->getOperatorCheckingRepository();
        $entities = $ocr->getItemsInvalidByEnabledOperator();
        $output->writeln('<comment>Invalid entities</comment>');
        /** @var OperatorChecking $entity */
        foreach ($entities as $entity) {
            $output->writeln($entity->getId().' '.$entity->getOperator()->getFullName().' '.$entity->getEnd()->format('d-m-Y'));
            if ($entity->getOperator()->getEmail()) {
                $this->ns->sendToOperatorInvalidCheckingNotification($entity);
            }
        }

        if (count($entities) > 0) {
            $this->ns->sendOperatorCheckingInvalidNotification($entities);
        }

        // Get before to be invalid entities
        $entities = $ocr->getItemsBeforeToBeInvalidByEnabledOperator();
        $output->writeln('<comment>Before to be invalid entities</comment>');
        /** @var OperatorChecking $entity */
        foreach ($entities as $entity) {
            $output->writeln($entity->getId().' '.$entity->getOperator()->getFullName().' '.$entity->getEnd()->format('d-m-Y'));
            if ($entity->getOperator()->getEmail()) {
                $this->ns->sendToOperatorBeforeToBeInvalidCheckingNotification($entity);
            }
        }

        if (count($entities) > 0) {
            $this->ns->sendOperatorCheckingBeforeToBeInvalidNotification($entities);
        }
    }
}
