<?php

namespace App\Command;

use App\Entity\Enterprise\Enterprise;
use App\Entity\Setting\User;
use Exception;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class LinkUserEnterpriseCommand.
 */
class LinkUserEnterpriseCommand extends AbstractBaseCommand
{
    /**
     * Configure.
     */
    protected function configure()
    {
        $this->setName('app:link:user:enterprise');
        $this->setDescription('Link user and enterprise');
    }

    /**
     * Execute.
     *
     * @param InputInterface  $input
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
            $users = $this->rm->getUserRepository()->getEnabledSortedByName();
            /** @var User $user */
            foreach ($users as $user) {
                $output->writeln($user->getId().' · '.$user->getFullname());
                $user->setDefaultEnterprise($enterprise);
                $enterprise->addUser($user);
            }

            $this->em->flush();
        }
    }
}
