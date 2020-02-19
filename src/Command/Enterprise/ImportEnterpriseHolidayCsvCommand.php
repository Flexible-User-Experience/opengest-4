<?php

namespace App\Command\Enterprise;

use App\Command\AbstractBaseCommand;
use App\Entity\Enterprise\Enterprise;
use App\Entity\Enterprise\EnterpriseHolidays;
use DateTime;
use DateTimeImmutable;
use Exception;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ImportEnterpriseHolidayCsvCommand.
 *
 * @category Command
 *
 * @author   David Romaní <david@flux.cat>
 */
class ImportEnterpriseHolidayCsvCommand extends AbstractBaseCommand
{
    /**
     * Configure.
     */
    protected function configure()
    {
        $this->setName('app:import:enterprise:holiday');
        $this->setDescription('Import enterprise holidays from CSV file');
        $this->addArgument('filename', InputArgument::REQUIRED, 'CSV file to import');
        $this->addOption('dry-run', null, InputOption::VALUE_NONE, 'don\'t persist changes into database');
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
        // Welcome & Initialization & File validations
        $fr = $this->initialValidation($input, $output);

        // Set counters
        $beginTimestamp = new DateTimeImmutable();
        $rowsRead = 0;
        $newRecords = 0;
        $errors = 0;

        // Import CSV rows
        while (false != ($row = $this->readRow($fr))) {
            $output->writeln('#'.$rowsRead.' · ID_'.$this->readColumn(0, $row).' · '.$this->readColumn(2, $row));
            /** @var Enterprise $enterprise */
            $enterprise = $this->rm->getEnterpriseRepository()->findOneBy(['taxIdentificationNumber' => $this->readColumn(4, $row)]);
            $date = $this->readColumn(2, $row);
            if ($enterprise && '0000-00-00' != $date) {
                $date = explode('-', $date);
                $day = new DateTime();
                $day->setDate($date[0], $date[1], $date[2]);
                $day->setTime(0, 0, 0);
                /** @var EnterpriseHolidays $enterpriseHolidays */
                $enterpriseHolidays = $this->rm->getEnterpriseHolidaysRepository()->findOneBy(['day' => $day, 'enterprise' => $enterprise]);
                if (!$enterpriseHolidays) {
                    // new record
                    $enterpriseHolidays = new EnterpriseHolidays();
                    ++$newRecords;
                }
                $enterpriseHolidays
                    ->setEnterprise($enterprise)
                    ->setDay($day)
                    ->setName($this->readColumn(3, $row))
                ;
                $this->em->persist($enterpriseHolidays);
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
        $this->printTotals($output, $rowsRead, $newRecords, $beginTimestamp, $endTimestamp, $errors, $input->getOption('dry-run'));
    }
}
