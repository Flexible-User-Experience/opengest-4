<?php

namespace App\Command\Partner;

use App\Command\AbstractBaseCommand;
use App\Entity\Partner\PartnerOrder;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ImportPartnerOrderCommand.
 *
 * @category Command
 *
 * @author   David Romaní <david@flux.cat>
 */
class ImportPartnerOrderCommand extends AbstractBaseCommand
{
    /**
     * Configure.
     */
    protected function configure()
    {
        $this->setName('app:import:partner:order');
        $this->setDescription('Import partner orders from CSV file');
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
            $number = $this->lts->nameCleaner($this->readColumn(2, $row));
            $code = $this->readColumn(3, $row);
            $partnerTaxIdentificationNumber = $this->readColumn(4, $row);
            $enterpriseTaxIdentificationNumber = $this->readColumn(5, $row);
            $output->writeln('#'.$rowsRead.' · ID_'.$this->readColumn(0, $row).' · '.$number.' · '.$code.' · '.$partnerTaxIdentificationNumber.' · '.$enterpriseTaxIdentificationNumber);
            $enterprise = $this->em->getRepository('App:Enterprise\Enterprise')->findOneBy(['taxIdentificationNumber' => $enterpriseTaxIdentificationNumber]);
            $partner = $this->em->getRepository('App:Partner\Partner')->findOneBy([
                'cifNif' => $partnerTaxIdentificationNumber,
                'enterprise' => $enterprise,
            ]);
            if ($number && $partner && $enterprise) {
                $partnerOrder = $this->em->getRepository('App:Partner\PartnerOrder')->findOneBy([
                    'number' => $number,
                    'partner' => $partner,
                ]);
                if (!$partnerOrder) {
                    // new record
                    $partnerOrder = new PartnerOrder();
                    ++$newRecords;
                }
                $partnerOrder
                    ->setPartner($partner)
                    ->setNumber($number)
                    ->setProviderReference($code)
                ;
                $this->em->persist($partnerOrder);
                if (0 == $rowsRead % self::CSV_BATCH_WINDOW && !$input->getOption('dry-run')) {
                    $this->em->flush();
                }
            } else {
                $output->write('<error>Error at row number #'.$rowsRead);
                if (!$number) {
                    $output->write(' · no number found');
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
        $endTimestamp = new \DateTime();
        $this->printTotals($output, $rowsRead, $newRecords, $beginTimestamp, $endTimestamp, $errors, $input->getOption('dry-run'));
    }
}
