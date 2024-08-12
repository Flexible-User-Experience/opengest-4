<?php

namespace App\Command\Partner;

use App\Command\AbstractBaseCommand;
use App\Entity\Enterprise\Enterprise;
use App\Entity\Partner\Partner;
use App\Entity\Partner\PartnerContact;
use DateTimeImmutable;
use Exception;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ImportPartnerContactCommand.
 *
 * @category Command
 *
 * @author   David Romaní <david@flux.cat>
 */
class ImportPartnerContactCommand extends AbstractBaseCommand
{
    /**
     * Configure.
     */
    protected function configure()
    {
        $this->setName('app:import:partner:contact');
        $this->setDescription('Import partner contact from CSV file');
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
            $name = $this->lts->nameCleaner($this->readColumn(2, $row));
            $care = $this->readColumn(3, $row);
            $email = $this->readColumn(7, $row);
            $partnerTaxIdentificationNumber = $this->readColumn(9, $row);
            $enterpriseTaxIdentificationNumber = $this->readColumn(10, $row);
            $output->writeln('#'.$rowsRead.' · ID_'.$this->readColumn(0, $row).' · '.$name.' · '.$care.' · '.$email.' · '.$partnerTaxIdentificationNumber.' · '.$enterpriseTaxIdentificationNumber);
            /** @var Enterprise $enterprise */
            $enterprise = $this->rm->getEnterpriseRepository()->findOneBy(['taxIdentificationNumber' => $enterpriseTaxIdentificationNumber]);
            /** @var Partner $partner */
            $partner = $this->rm->getPartnerRepository()->findOneBy([
                'cifNif' => $partnerTaxIdentificationNumber,
                'enterprise' => $enterprise,
            ]);
            if ($name && $partner && $enterprise) {
                /** @var PartnerContact $partnerContact */
                $partnerContact = $this->rm->getPartnerContactRepository()->findOneBy([
                    'name' => $name,
                    'partner' => $partner,
                ]);
                if (!$partnerContact) {
                    // new record
                    $partnerContact = new PartnerContact();
                    ++$newRecords;
                }
                $partnerContact
                    ->setPartner($partner)
                    ->setName($name)
                    ->setCare($care)
                    ->setPhone($this->readColumn(4, $row))
                    ->setMobile($this->readColumn(5, $row))
                    ->setFax($this->readColumn(6, $row))
                    ->setEmail($email)
                    ->setNotes($this->readColumn(8, $row))
                ;
                $this->em->persist($partnerContact);
                if (0 == $rowsRead % self::CSV_BATCH_WINDOW && !$input->getOption('dry-run')) {
                    $this->em->flush();
                }
            } else {
                $output->write('<error>Error at row number #'.$rowsRead);
                if (!$name) {
                    $output->write(' · no name found');
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
