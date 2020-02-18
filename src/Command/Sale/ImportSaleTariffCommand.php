<?php

namespace App\Command\Sale;

use App\Command\AbstractBaseCommand;
use App\Entity\Sale\SaleTariff;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ImportSaleTariffCommand.
 *
 * @category Command
 *
 * @author   Rubèn Hierro <info@rubenhierro.com>
 */
class ImportSaleTariffCommand extends AbstractBaseCommand
{
    /**
     * Configure.
     */
    protected function configure()
    {
        $this->setName('app:import:sale:tariff');
        $this->setDescription('Import sale tariff from CSV file');
        $this->addArgument('filename', InputArgument::REQUIRED, 'CSV file to import');
        $this->addOption('dry-run', null, InputOption::VALUE_NONE, 'don\'t persist changes into database');
    }

    /**
     * Execute.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|void|null
     *
     * @throws InvalidArgumentException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Welcome & Initialization & File validations
        $fr = $this->initialValidation($input, $output);

        // Set counters
        $beginTimestamp = new \DateTime();
        $rowsRead = 0;
        $newRecords = 0;
        $errors = 0;

        // Import CSV rows
        while (false != ($row = $this->readRow($fr))) {
            $year = $this->readColumn(2, $row);
            $tonnage = $this->readColumn(3, $row);
            $priceHour = $this->readColumn(4, $row);
            $miniumHours = $this->readColumn(5, $row);
            $miniumHolidayHours = $this->readColumn(6, $row);
            $displacement = $this->readColumn(7, $row);
            $increaseForHolidays = $this->readColumn(8, $row);
            $enterpriseTaxIdentificationNumber = $this->readColumn(9, $row);
            $enterprise = $this->em->getRepository('App:Enterprise\Enterprise')->findOneBy(['taxIdentificationNumber' => $enterpriseTaxIdentificationNumber]);
            $output->writeln('#'.$rowsRead.' · ID_'.$this->readColumn(0, $row).' · '.$year.' · '.$tonnage.' · '.$priceHour.' · '.$miniumHours.' · '.$miniumHolidayHours.' · '.$displacement.' · '.$increaseForHolidays.' · '.$enterpriseTaxIdentificationNumber);

            if ($year && $tonnage && $enterprise) {
                $saleTariff = $this->em->getRepository('App:Sale\SaleTariff')->findOneBy([
                    'year' => $year,
                    'tonnage' => $tonnage,
                    'enterprise' => $enterprise,
                ]);
                if (!$saleTariff) {
                    // new record
                    $saleTariff = new SaleTariff();
                    ++$newRecords;
                }
                $saleTariff
                    ->setEnterprise($enterprise)
                    ->setYear($year)
                    ->setTonnage($tonnage)
                    ->setPriceHour($priceHour)
                    ->setMiniumHours($miniumHours)
                    ->setMiniumHolidayHours($miniumHolidayHours)
                    ->setDisplacement($displacement)
                    ->setIncreaseForHolidays($increaseForHolidays)
                ;
                $this->em->persist($saleTariff);
                if (0 == $rowsRead % self::CSV_BATCH_WINDOW && !$input->getOption('dry-run')) {
                    $this->em->flush();
                }
            } else {
                $output->write('<error>Error at row number #'.$rowsRead);
                if (!$year) {
                    $output->write(' · no year found');
                }
                if (!$tonnage) {
                    $output->write(' · no tonnage found');
                }
                if (!$enterprise) {
                    $output->write(' · no enterprise found');
                }
                $output->writeln('</error>');
                ++$errors;
            }
            ++$rowsRead;
        }
        if (!$input->getOption('dry-run')) {
            $this->em->flush();
        }

        // Print totals
        $endTimestamp = new \DateTime();
        $this->printTotals($output, $rowsRead, $newRecords, $beginTimestamp, $endTimestamp, $errors, $input->getOption('dry-run'));
    }
}
