<?php

namespace App\Command\Partner;

use App\Command\AbstractBaseCommand;
use App\Entity\Partner\Partner;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class UpdatePartnerAccountingAccountCommand.
 *
 * @category Command
 *
 * @author   Jordi Sort <jordi.sort@mirmit.com>
 */
class UpdatePartnerAccountingAccountCommand extends AbstractBaseCommand
{
    /**
     * Configure.
     */
    protected function configure(): void
    {
        $this->setName('app:update:partner:accounting_account');
        $this->setDescription('Update partner accounting account to match 10 digits criteria.');
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
        $output->writeln('<info>Welcome to update partner accounting accounts command.</info>');

        // Set counters
        $numberPartners = 0;
        $accountingAccountsChanged = 0;

        /** @var Partner[] $partners */
        $partners = $this->rm->getPartnerRepository()->findAll();
        foreach ($partners as $partner) {
            if ($partner->getAccountingAccount()) {
                $accountingAccount = (string) $partner->getAccountingAccount();
                $missingZeros = 10 - strlen($accountingAccount);
                if ($missingZeros > 0) {
                    $newAccountingAccount = substr_replace($accountingAccount, str_repeat('0', $missingZeros), 2, 0);
                    $partner->setAccountingAccount($newAccountingAccount);
                    ++$accountingAccountsChanged;
                }
            }
            ++$numberPartners;
        }
        $this->em->flush();
        $output->writeln('<comment>'.$numberPartners.' partners found.</comment>');
        $output->writeln('<comment>'.$accountingAccountsChanged.' partner accounting accounts changed.</comment>');

        return Command::SUCCESS;
    }
}
