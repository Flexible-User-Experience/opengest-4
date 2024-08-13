<?php

namespace App\Command\Partner;

use App\Command\AbstractBaseCommand;
use App\Entity\Enterprise\Enterprise;
use App\Entity\Partner\Partner;
use App\Entity\Partner\PartnerUnableDays;
use DateTimeImmutable;
use Exception;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ImportPartnerUnableDaysCommand.
 *
 * @category Command
 *
 * @author   David Romaní <david@flux.cat>
 */
class ImportPartnerUnableDaysCommand extends AbstractBaseCommand
{
    /**
     * Configure.
     */
    protected function configure(): void
    {
        $this->setName('app:import:partner:unable:days');
        $this->setDescription('Import partner unable days from CSV file');
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
            $begin = $this->dts->convertStringWithDayAndMonthToDateTime($this->readColumn(2, $row));
            $end = $this->dts->convertStringWithDayAndMonthToDateTime($this->readColumn(3, $row));
            $partnerTaxIdentificationNumber = $this->readColumn(4, $row);
            $enterpriseTaxIdentificationNumber = $this->readColumn(5, $row);
            $output->writeln('#'.$rowsRead.' · ID_'.$this->readColumn(0, $row).' · '.$begin->format('d/m').' · '.$end->format('d/m').' · '.$partnerTaxIdentificationNumber.' · '.$enterpriseTaxIdentificationNumber);
            $enterprise = $this->em->getRepository(Enterprise::class)->findOneBy(['taxIdentificationNumber' => $enterpriseTaxIdentificationNumber]);
            $partner = $this->em->getRepository(Partner::class)->findOneBy([
                'cifNif' => $partnerTaxIdentificationNumber,
                'enterprise' => $enterprise,
            ]);
            if ($begin && $end && $partner && $enterprise) {
                /** @var PartnerUnableDays $partnerUnableDays */
                $partnerUnableDays = $this->rm->getPartnerUnableDaysRepository()->findOneBy([
                    'begin' => $begin,
                    'end' => $end,
                    'partner' => $partner,
                ]);
                if (!$partnerUnableDays) {
                    // new record
                    $partnerUnableDays = new PartnerUnableDays();
                    ++$newRecords;
                }
                $partnerUnableDays
                    ->setPartner($partner)
                    ->setBegin($begin)
                    ->setEnd($end)
                ;
                $this->em->persist($partnerUnableDays);
                if (0 == $rowsRead % self::CSV_BATCH_WINDOW && !$input->getOption('dry-run')) {
                    $this->em->flush();
                }
            } else {
                $output->write('<error>Error at row number #'.$rowsRead);
                if (!$begin) {
                    $output->write(' · no begin found');
                }
                if (!$end) {
                    $output->write(' · no end found');
                }
                if (!$partner) {
                    $output->write(' · no partner found');
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
