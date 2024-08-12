<?php

namespace App\Command\Enterprise;

use App\Command\AbstractBaseCommand;
use App\Entity\Enterprise\Enterprise;
use App\Entity\Enterprise\EnterpriseTransferAccount;
use DateTimeImmutable;
use Exception;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ImportEnterpriseTransferAccountCsvCommand.
 *
 * @category Command
 *
 * @author   David Romaní <david@flux.cat>
 */
class ImportEnterpriseTransferAccountCsvCommand extends AbstractBaseCommand
{
    /**
     * Configure.
     */
    protected function configure()
    {
        $this->setName('app:import:enterprise:transfer:account');
        $this->setDescription('Import enterprise transfer accounts from CSV file');
        $this->addArgument('filename', InputArgument::REQUIRED, 'CSV file to import');
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
        // Welcome & Initialization & File validations
        $fr = $this->initialValidation($input, $output);

        // Set counters
        $beginTimestamp = new DateTimeImmutable();
        $rowsRead = 0;
        $newRecords = 0;
        $errors = 0;

        // Import CSV rows
        while (false != ($row = $this->readRow($fr))) {
            $name = $this->readColumn(2, $row);
            $output->writeln('#'.$rowsRead.' · ID_'.$this->readColumn(0, $row).' · '.$name);
            /** @var Enterprise $enterprise */
            $enterprise = $this->rm->getEnterpriseRepository()->findOneBy(['taxIdentificationNumber' => $this->readColumn(9, $row)]);
            if ($enterprise) {
                /** @var EnterpriseTransferAccount $transferAccount */
                $transferAccount = $this->rm->getEnterpriseTransferAccountRepository()->findOneBy(['name' => $name, 'enterprise' => $enterprise]);
                if (!$transferAccount) {
                    // new record
                    $transferAccount = new EnterpriseTransferAccount();
                    ++$newRecords;
                }
                $transferAccount
                    ->setEnterprise($enterprise)
                    ->setName($name)
                    ->setIban($this->readColumn(3, $row))
                    ->setSwift($this->readColumn(4, $row))
                    ->setBankCode($this->readColumn(5, $row))
                    ->setOfficeNumber($this->readColumn(6, $row))
                    ->setControlDigit($this->readColumn(7, $row))
                    ->setAccountNumber($this->readColumn(8, $row))
                ;
                $this->em->persist($transferAccount);
                if (0 == $rowsRead % self::CSV_BATCH_WINDOW && !$input->getOption('dry-run')) {
                    $this->em->flush();
                }
            } else {
                $output->writeln('<error>Error at row number #'.$rowsRead.'</error>');
                ++$errors;
            }
            ++$rowsRead;
        }
        if (!$input->getOption('dry-run')) {
            $this->em->flush();
        }

        // Print totals
        $endTimestamp = new DateTimeImmutable();

        return $this->printTotals($output, $rowsRead, $newRecords, $beginTimestamp, $endTimestamp, $errors, $input->getOption('dry-run'));
    }
}
