<?php

namespace App\Command\Enterprise;

use App\Command\AbstractBaseCommand;
use App\Entity\Enterprise\ActivityLine;
use App\Entity\Enterprise\Enterprise;
use App\Entity\Operator\Operator;
use App\Entity\Partner\Partner;
use App\Entity\Sale\SaleDeliveryNote;
use App\Entity\Sale\SaleRequest;
use App\Entity\Sale\SaleRequestHasDeliveryNote;
use App\Entity\Sale\SaleServiceTariff;
use DateTimeImmutable;
use Exception;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class RemoveUnusedEnterprisesCommand.
 *
 * @category Command
 *
 * @author   Jordi Sort <jordi.sort@mirmit.com>
 */
class RemoveUnusedEnterprisesCommand extends AbstractBaseCommand
{
    /**
     * Configure.
     */
    protected function configure()
    {
        // Unused method, prefering not to import those enterprises in the first place
        $this->setName('app:remove:unused:enterprises');
        $this->setDescription('Remove unused enterprises and their related fields');
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
        // Set counters
        $beginTimestamp = new DateTimeImmutable();
        $rowsRead = 0;
        $rowsDeleted = 0;
        $errors = 0;
//        $enterprises = array_filter($this->rm->getEnterpriseRepository()->findAll(), function (Enterprise $enterprise) {
//            return $enterprise->getId() > 2;
//        });
//        /** @var Enterprise $enterprise */
//        foreach ($enterprises as $enterprise) {
//            $operators = $this->rm->getOperatorRepository()->findBy(['enterprise' => $enterprise]);
//            /** @var Operator $operator */
//            foreach ($operators as $operator) {
//                foreach ($operator->getOperatorVariousAmount() as $operatorVariousAmount) {
//                    $this->em->remove($operatorVariousAmount);
//                }
//                $this->em->remove($operator);
//            }
//            $vehicles = $this->rm->getVehicleRepository()->findBy(['enterprise' => $enterprise]);
//            foreach ($vehicles as $vehicle) {
//                $this->em->remove($vehicle);
//            }
//            foreach ($enterprise->getSaleRequests() as $saleRequest) {
//                /** @var SaleRequestHasDeliveryNote $saleRequestHasDeliveryNote */
//                foreach ($saleRequest->getSaleRequestHasDeliveryNotes() as $saleRequestHasDeliveryNote) {
//                    $saleDeliveryNote = $saleRequestHasDeliveryNote->getSaleDeliveryNote();
//                    $this->em->remove($saleDeliveryNote);
//                    $this->em->remove($saleRequestHasDeliveryNote);
//                }
//                $this->em->remove($saleRequest);
//            }
//            foreach ($enterprise->getSaleTariffs() as $saleTariff) {
//                $this->em->remove($saleTariff);
//            }
//            /** @var ActivityLine $activityLine */
//            foreach ($enterprise->getActivityLines() as $activityLine) {
//                /** @var SaleServiceTariff $saleServiceTariff */
//                foreach ($activityLine->getSaleServiceTariffs() as $saleServiceTariff) {
//                    /* @var SaleRequest $saleRequest */
//                    $this->em->remove($saleServiceTariff);
//                }
//                $this->em->remove($activityLine);
//            }
//            foreach ($enterprise->getUsers() as $user) {
//                $this->em->remove($user);
//            }
//            foreach ($enterprise->getCollectionDocumentTypes() as $collectionDocumentType) {
//                $this->em->remove($collectionDocumentType);
//            }
//            foreach ($enterprise->getEnterpriseHolidays() as $enterpriseHoliday) {
//                $this->em->remove($enterpriseHoliday);
//            }
//            foreach ($enterprise->getEnterpriseTransferAccounts() as $enterpriseTransferAccount) {
//                $this->em->remove($enterpriseTransferAccount);
//            }
//            /** @var Partner $partner */
//            foreach ($enterprise->getPartners() as $partner) {
//                foreach ($partner->getBuildingSites() as $buildingSite) {
//                    $this->em->remove($buildingSite);
//                }
//                foreach ($partner->getContacts() as $contact) {
//                    $this->em->remove($contact);
//                }
//                if ($partner->getSaleInvoices()) {
//                    foreach ($partner->getSaleInvoices() as $saleInvoice) {
//                        $this->em->remove($saleInvoice);
//                    }
//                    $saleInvoices = $this->rm->getSaleInvoiceRepository()->findBy(['partner' => $partner]);
//                    foreach ($saleInvoices as $saleInvoice) {
//                        $this->em->remove($saleInvoice);
//                    }
//                }
//                /** @var SaleDeliveryNote $saleDeliveryNote */
//                foreach ($partner->getSaleDeliveryNotes() as $saleDeliveryNote) {
//                    $this->em->remove($saleDeliveryNote->getSaleInvoice());
//                    $this->em->remove($saleDeliveryNote);
//                }
//                $this->em->remove($partner);
//            }
//            $this->em->remove($enterprise);
//        }
//
//        $this->em->flush();
//        if (!$input->getOption('dry-run')) {
//            $this->em->flush();
//        }

        // Print totals
        $endTimestamp = new DateTimeImmutable();
        $this->printTotals($output, $rowsRead, $rowsDeleted, $beginTimestamp, $endTimestamp, $errors, $input->getOption('dry-run'));
    }
}
