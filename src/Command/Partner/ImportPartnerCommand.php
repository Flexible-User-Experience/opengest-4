<?php

namespace App\Command\Partner;

use App\Command\AbstractBaseCommand;
use App\Entity\Enterprise\Enterprise;
use App\Entity\Enterprise\EnterpriseTransferAccount;
use App\Entity\Partner\Partner;
use App\Entity\Partner\PartnerClass;
use App\Entity\Partner\PartnerType;
use App\Entity\Setting\City;
use DateTimeImmutable;
use Exception;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ImportPartnerCommand.
 *
 * @category Command
 *
 * @author   David Romaní <david@flux.cat>
 */
class ImportPartnerCommand extends AbstractBaseCommand
{
    /**
     * Configure.
     */
    protected function configure()
    {
        $this->setName('app:import:partner');
        $this->setDescription('Import partner class from CSV file');
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
            $enterprise = $this->readColumn(40, $row);
            $partnerType = $this->readColumn(41, $row);
            $partnerClass = $this->readColumn(42, $row);
            $partnerTaxIdentificationNumber = $this->lts->taxIdentificationNumberCleaner($this->readColumn(11, $row));
            $enterpriseTransferAccount = $this->readColumn(43, $row);
            $collectionDocument = $this->readColumn(44, $row);
            $accountingAccount = $this->readColumn(45, $row);
            $collectionTerm1 = $this->readColumn(46, $row);
            $collectionTerm2 = $this->readColumn(47, $row);
            $collectionTerm3 = $this->readColumn(48, $row);
            $payDay1 = $this->readColumn(49, $row);
            $payDay2 = $this->readColumn(50, $row);
            $invoiceCopiesNumber = $this->readColumn(51, $row);
            $name = $this->lts->nameCleaner($this->readColumn(5, $row));
            $cityName = $this->lts->cityNameCleaner($this->readColumn(8, $row));
            $postalCode = $this->lts->postalCodeCleaner($this->readColumn(6, $row));
            /** @var City $city */
            $city = $this->rm->getCityRepository()->findOneBy([
                'postalCode' => $postalCode,
                'name' => $cityName,
            ]);
            $output->writeln('#'.$rowsRead.' · ID_'.$this->readColumn(0, $row).' · '.$partnerTaxIdentificationNumber.' · '.$name.' · '.$enterprise.' · '.$this->readColumn(8, $row).' · '.$this->readColumn(6, $row));
            if ($enterprise && $partnerType && $partnerClass && $city) {
                /** @var Enterprise $enterprise */
                $enterprise = $this->rm->getEnterpriseRepository()->findOneBy(['taxIdentificationNumber' => $enterprise]);
                /** @var PartnerType $partnerType */
                $partnerType = $this->rm->getPartnerTypeRepository()->findOneBy(['name' => $partnerType]);
                /** @var PartnerClass $partnerClass */
                $partnerClass = $this->rm->getPartnerClassRepository()->findOneBy(['name' => $partnerClass]);
                if ($enterpriseTransferAccount) {
                    /** @var EnterpriseTransferAccount $enterpriseTransferAccount */
                    $enterpriseTransferAccount = $this->rm->getEnterpriseTransferAccountRepository()->findOneBy(['name' => $enterpriseTransferAccount]);
                }
                if ($enterprise && $partnerType && $partnerClass) {
                    /** @var Partner $partner */
                    $partner = $this->rm->getPartnerRepository()->findOneBy([
                        'cifNif' => $partnerTaxIdentificationNumber,
                        'enterprise' => $enterprise,
                        'type' => $partnerType,
                        'class' => $partnerClass,
                        'transferAccount' => $enterpriseTransferAccount,
                    ]);
                    if (!$partner) {
                        // new record
                        $partner = new Partner();
                        ++$newRecords;
                    }
                    $partner
                        ->setCifNif($partnerTaxIdentificationNumber)
                        ->setName($name)
                        ->setEnterprise($enterprise)
                        ->setClass($partnerClass)
                        ->setType($partnerType)
                        ->setEnabled('1' == $this->readColumn(3, $row) ? true : false)
                        ->setNotes($this->readColumn(4, $row))
                        ->setMainAddress($this->readColumn(7, $row))
                        ->setMainCity($city)
                        ->setSecondaryAddress($this->readColumn(16, $row))
                        ->setPhoneNumber1($this->readColumn(12, $row))
                        ->setPhoneNumber2($this->readColumn(13, $row))
                        ->setPhoneNumber3($this->readColumn(23, $row))
                        ->setPhoneNumber4($this->readColumn(22, $row))
                        ->setPhoneNumber5($this->readColumn(23, $row))
                        ->setFaxNumber1($this->readColumn(14, $row))
                        ->setFaxNumber2($this->readColumn(24, $row))
                        ->setEmail($this->readColumn(25, $row))
                        ->setWww($this->readColumn(26, $row))
                        ->setDiscount($this->readColumn(31, $row))
                        ->setCode($this->readColumn(33, $row))
                        ->setProviderReference($this->readColumn(36, $row))
                        ->setReference($this->readColumn(34, $row))
                        ->setIvaTaxFree('1' == $this->readColumn(32, $row) ? true : false)
                        ->setIban(
                            $this->readColumn(37, $row).
                            $this->readColumn(27, $row).
                            $this->readColumn(28, $row).
                            $this->readColumn(29, $row).
                            $this->readColumn(30, $row)
                        )
                        ->setSwift($this->readColumn(38, $row))
                        ->setBankCode($this->readColumn(27, $row))
                        ->setOfficeNumber($this->readColumn(28, $row))
                        ->setControlDigit($this->readColumn(29, $row))
                        ->setAccountNumber($this->readColumn(30, $row))
                        ->setAccountingAccount($accountingAccount)
                        ->setCollectionTerm1($collectionTerm1)
                        ->setCollectionTerm2($collectionTerm2)
                        ->setCollectionTerm3($collectionTerm3)
                        ->setPayDay1($payDay1)
                        ->setPayDay2($payDay2)
                        ->setInvoiceCopiesNumber($invoiceCopiesNumber)
                    ;
                    if ($enterpriseTransferAccount) {
                        $partner->setTransferAccount($enterpriseTransferAccount);
                    }
                    if ($collectionDocument) {
                        $collectionDocumentType = $this->rm->getCollectionDocumentTypeRepository()->findOneBy(['name' => $collectionDocument]);
                        if ($collectionDocumentType) {
                            $partner->setCollectionDocumentType($collectionDocumentType);
                        }
                    }
                    $secondaryCityName = $this->lts->cityNameCleaner($this->readColumn(18, $row));
                    $secondaryPostalCode = $this->lts->postalCodeCleaner($this->readColumn(17, $row));
                    /** @var City $secondaryCity */
                    $secondaryCity = $this->rm->getCityRepository()->findOneBy([
                        'postalCode' => $secondaryPostalCode,
                        'name' => $secondaryCityName,
                    ]);
                    if ($secondaryCity) {
                        $partner->setSecondaryCity($secondaryCity);
                    }
                    $this->em->persist($partner);
                    if (0 == $rowsRead % self::CSV_BATCH_WINDOW && !$input->getOption('dry-run')) {
                        $this->em->flush();
                    }
                }
            } else {
                $output->write('<error>Error at row number #'.$rowsRead);
                if (!$enterprise) {
                    $output->write(' · no enterprise found');
                }
                if (!$partnerType) {
                    $output->write(' · no partner type found');
                }
                if (!$partnerClass) {
                    $output->write(' · no partner class found');
                }
                if (!$enterpriseTransferAccount) {
                    $output->write(' · no enterprise transfer account found');
                }
                if (!$city) {
                    $output->write(' · no city '.$cityName.' with postal code '.$postalCode.' found');
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
        $this->printTotals($output, $rowsRead, $newRecords, $beginTimestamp, $endTimestamp, $errors, $input->getOption('dry-run'));
    }
}
