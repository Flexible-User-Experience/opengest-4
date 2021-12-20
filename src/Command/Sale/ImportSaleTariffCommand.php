<?php

namespace App\Command\Sale;

use App\Command\AbstractBaseCommand;
use App\Entity\Enterprise\Enterprise;
use App\Entity\Sale\SaleServiceTariff;
use App\Entity\Sale\SaleTariff;
use DateTimeImmutable;
use Exception;
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
     * @return int|void|null
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
            $year = $this->readColumn(2, $row);
            $tonnage = $this->readColumn(3, $row);
            $priceHour = $this->readColumn(4, $row);
            $miniumHours = $this->readColumn(5, $row);
            $miniumHolidayHours = $this->readColumn(6, $row);
            $displacement = $this->readColumn(7, $row);
            $increaseForHolidays = $this->readColumn(8, $row);
            $enterpriseTaxIdentificationNumber = $this->readColumn(9, $row);
            /** @var Enterprise $enterprise */
            $enterprise = $this->rm->getEnterpriseRepository()->findOneBy(['taxIdentificationNumber' => $enterpriseTaxIdentificationNumber]);
            $output->writeln('#'.$rowsRead.' · ID_'.$this->readColumn(0, $row).' · '.$year.' · '.$tonnage.' · '.$priceHour.' · '.$miniumHours.' · '.$miniumHolidayHours.' · '.$displacement.' · '.$increaseForHolidays.' · '.$enterpriseTaxIdentificationNumber);

            if ($year && $tonnage && $enterprise) {
                //Todo Check if SaleServiceTariff exists, if not, create new one
                /** @var SaleServiceTariff $saleServiceTariff */
                $saleServiceTariff = $this->rm->getSaleServiceTariffRepository()->findOneBy([
                    'description' => $tonnage,
                ]);
                if (!$saleServiceTariff) {
                    // new record
                    $saleServiceTariff = new SaleServiceTariff();
                    $saleServiceTariff->setDescription($tonnage);
                    $this->em->persist($saleServiceTariff);
                    $this->em->flush();
                }
                /** @var SaleTariff $saleTariff */
                $saleTariff = $this->rm->getSaleTariffRepository()->findOneBy([
                    'year' => $year,
                    'saleServiceTariff' => $saleServiceTariff,
                    'enterprise' => $enterprise,
                ]);
                if (!$saleTariff) {
                    // new record
                    $saleTariff = new SaleTariff();
                    ++$newRecords;
                }
                //Todo Add fields data, saleServiceTariff (as they are mandatory)
                $saleTariff
                    ->setEnterprise($enterprise)
                    ->setYear($year)
                    ->setTonnage($tonnage)
                    ->setPriceHour($priceHour)
                    ->setMiniumHours($miniumHours)
                    ->setMiniumHolidayHours($miniumHolidayHours)
                    ->setDisplacement($displacement)
                    ->setIncreaseForHolidays($increaseForHolidays)
                    ->setDate(date_create('1-1-'.$year))
                    ->setSaleServiceTariff($saleServiceTariff)
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
        $endTimestamp = new DateTimeImmutable();

        return $this->printTotals($output, $rowsRead, $newRecords, $beginTimestamp, $endTimestamp, $errors, $input->getOption('dry-run'));
    }
}
