<?php

namespace App\Listener;

use App\Entity\Sale\SaleInvoice;
use App\Manager\InvoiceManager;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\Filesystem\Filesystem;

#[AsEntityListener(event: Events::preUpdate, method: 'preUpdate', entity: SaleInvoice::class)]
class SaleInvoiceChangeEventListener
{
    public function __construct(
        private readonly InvoiceManager $invoiceManager,
        private readonly string $einvoiceFolderPath
    ) {

    }

    public function preUpdate(SaleInvoice $saleInvoice, PreUpdateEventArgs $event): void
    {
        if($event->hasChangedField('hasBeenCounted') && $saleInvoice->getHasBeenCounted()) {
            $xml = $this->invoiceManager->createEInvoice($saleInvoice);
            $filesystem = new Filesystem();
            $newFileFullPath = $this->einvoiceFolderPath.$saleInvoice->getInvoiceNumber().'_'.$saleInvoice->getSeries()->getName().'.xml';
            $filesystem->dumpFile($newFileFullPath, $xml);
        }
    }
}