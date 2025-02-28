<?php

namespace App\Entity\Sale;

use App\Entity\AbstractBase;
use App\Entity\Enterprise\CollectionDocumentType;
use App\Entity\Partner\Partner;
use App\Entity\Partner\PartnerDeliveryAddress;
use App\Entity\Setting\City;
use App\Entity\Setting\SaleInvoiceSeries;
use App\Service\Format\NumberFormatService;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use josemmo\Facturae\FacturaePayment;
use Mirmit\EFacturaBundle\Interfaces\BuyerFacturaEInterface;
use Mirmit\EFacturaBundle\Interfaces\InvoiceFacturaEInterface;
use Mirmit\EFacturaBundle\Interfaces\SellerFacturaEInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class SaleInvoice.
 *
 * @category
 *
 *
 */
#[ORM\Table(name: 'sale_invoice')]
#[ORM\Entity(repositoryClass: \App\Repository\Sale\SaleInvoiceRepository::class)]
class SaleInvoice extends AbstractBase implements InvoiceFacturaEInterface
{
    /**
     * @Assert\Count(
     *     min = 1,
     *     minMessage = "La factura tiene que tener un albarán como mínimo"
     * )
     */
    #[Groups('api')]
    #[ORM\OneToMany(targetEntity: \App\Entity\Sale\SaleDeliveryNote::class, mappedBy: 'saleInvoice')]
    private Collection $deliveryNotes;

    /**
     * @var \DateTime
     */
    #[ORM\Column(type: 'datetime')]
    private $date;

    /**
     * @var Partner
     */
    #[ORM\ManyToOne(targetEntity: \App\Entity\Partner\Partner::class, inversedBy: 'saleInvoices')]
    private $partner;

    /**
     * @var int
     */
    #[ORM\Column(type: 'integer')]
    private $invoiceNumber;

    /**
     * @var SaleInvoiceSeries
     */
    #[ORM\ManyToOne(targetEntity: \App\Entity\Setting\SaleInvoiceSeries::class)]
    private $series;

    /**
     * @var int
     */
    #[ORM\Column(type: 'integer')]
    private $type;

    /**
     * @var float
     */
    #[ORM\Column(type: 'float', nullable: true)]
    private $total;

    /**
     * @var float
     */
    #[ORM\Column(type: 'float', nullable: true)]
    private $baseTotal;

    /**
     * @var float
     */
    #[ORM\Column(type: 'float', nullable: true)]
    private $iva = 0;

    /**
     * @var float
     */
    #[ORM\Column(type: 'float', nullable: true)]
    private $iva21 = 0;
    /**
     * @var float
     */
    #[ORM\Column(type: 'float', nullable: true)]
    private $iva10 = 0;
    /**
     * @var float
     */
    #[ORM\Column(type: 'float', nullable: true)]
    private $iva4 = 0;
    /**
     * @var float
     */
    #[ORM\Column(type: 'float', nullable: true)]
    private $iva0 = 0;

    /**
     * @var float
     */
    #[ORM\Column(type: 'float', nullable: true)]
    private $irpf = 0;

    /**
     * @var bool
     */
    #[ORM\Column(type: 'boolean')]
    private $hasBeenCounted = false;

    /**
     * @var float
     */
    #[ORM\Column(type: 'float', nullable: true)]
    private $discount = 0;

    /**
     * @var ?PartnerDeliveryAddress
     */
    #[ORM\ManyToOne(targetEntity: \App\Entity\Partner\PartnerDeliveryAddress::class)]
    private $deliveryAddress;

    /**
     * @var ArrayCollection
     */
    #[ORM\OneToMany(targetEntity: \App\Entity\Sale\SaleInvoiceDueDate::class, mappedBy: 'saleInvoice', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private $saleInvoiceDueDates;

    /**
     * @var ?CollectionDocumentType
     */
    #[ORM\ManyToOne(targetEntity: \App\Entity\Enterprise\CollectionDocumentType::class)]
    private $collectionDocumentType;

    /**
     * @var ?string
     */
    #[ORM\Column(type: 'text', nullable: true)]
    private $observations;

    /**
     * @var ?string
     */
    #[ORM\Column(type: 'text', nullable: true)]
    private $partnerName;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string')]
    private $partnerCifNif;

    /**
     * @var ?string
     */
    #[ORM\Column(type: 'string', nullable: true)]
    private $partnerMainAddress;

    /**
     * @var City
     */
    #[ORM\ManyToOne(targetEntity: \App\Entity\Setting\City::class)]
    private $partnerMainCity;

    /**
     * @var ?string
     */
    #[ORM\Column(type: 'string', nullable: true)]
    private $partnerIban;

    /**
     * @var ?string
     */
    #[ORM\Column(type: 'string', nullable: true)]
    private $partnerSwift;

    
    #[ORM\JoinColumn(nullable: true)]
    #[ORM\OneToOne(targetEntity: \App\Entity\Sale\SaleInvoice::class)]
    private SaleInvoice|null $saleInvoiceGenerated = null;

    /**
     * Methods.
     */

    /**
     * SaleInvoice constructor.
     */
    public function __construct()
    {
        $this->deliveryNotes = new ArrayCollection();
        $this->saleInvoiceDueDates = new ArrayCollection();
    }

    public function getDeliveryNotes(): Collection
    {
        return $this->deliveryNotes;
    }

    /**
     * @return $this
     */
    public function setDeliveryNotes(Collection $deliveryNotes): static
    {
        $this->deliveryNotes = $deliveryNotes;

        return $this;
    }

    /**
     * @return $this
     */
    public function addDeliveryNote(SaleDeliveryNote $deliveryNote): static
    {
        if (!$this->deliveryNotes->contains($deliveryNote)) {
            $this->deliveryNotes->add($deliveryNote);
            $deliveryNote->setSaleInvoice($this);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function removeDeliveryNote(SaleDeliveryNote $deliveryNote): static
    {
        if ($this->deliveryNotes->contains($deliveryNote)) {
            $this->deliveryNotes->removeElement($deliveryNote);
            $deliveryNote->setSaleInvoice(null);
            $deliveryNote->setIsInvoiced(false);
        }

        return $this;
    }

    public function getSaleInvoiceDueDates(): Collection
    {
        return $this->saleInvoiceDueDates;
    }

    /**
     * @return $this
     */
    public function setSaleInvoiceDueDates(Collection $saleInvoiceDueDates): static
    {
        $this->saleInvoiceDueDates = $saleInvoiceDueDates;

        return $this;
    }

    /**
     * @return $this
     */
    public function addSaleInvoiceDueDate(SaleInvoiceDueDate $saleInvoiceDueDate): static
    {
        if (!$this->saleInvoiceDueDates->contains($saleInvoiceDueDate)) {
            $this->saleInvoiceDueDates->add($saleInvoiceDueDate);
            $saleInvoiceDueDate->setSaleInvoice($this);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function removeSaleInvoiceDueDate(SaleInvoiceDueDate $saleInvoiceDueDate): static
    {
        if ($this->saleInvoiceDueDates->contains($saleInvoiceDueDate)) {
            $this->saleInvoiceDueDates->removeElement($saleInvoiceDueDate);
            $saleInvoiceDueDate->setSaleInvoice(null);
        }

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDate(): \DateTime
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     *
     * @return $this
     */
    public function setDate($date): static
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return Partner
     */
    public function getPartner(): Partner
    {
        return $this->partner;
    }

    /**
     * @param Partner $partner
     *
     * @return $this
     */
    public function setPartner($partner): static
    {
        $this->partner = $partner;

        return $this;
    }

    /**
     * @return int
     */
    public function getInvoiceNumber(): int
    {
        return $this->invoiceNumber;
    }

    /**
     * @param int $invoiceNumber
     *
     * @return $this
     */
    public function setInvoiceNumber($invoiceNumber): static
    {
        $this->invoiceNumber = $invoiceNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getFullInvoiceNumber(): string
    {
        return ($this->getSeries() ? $this->getSeries()->getPrefix() : '???').'/'.$this->getInvoiceNumber();
    }

    /**
     * @return SaleInvoiceSeries
     */
    public function getSeries(): SaleInvoiceSeries
    {
        return $this->series;
    }

    /**
     * @param SaleInvoiceSeries $series
     *
     * @return $this
     */
    public function setSeries($series): static
    {
        $this->series = $series;

        return $this;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @param int $type
     *
     * @return $this
     */
    public function setType($type): static
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return float
     */
    public function getTotal(): float
    {
        return $this->total;
    }

    /**
     * @param float $total
     *
     * @return $this
     */
    public function setTotal($total): static
    {
        $this->total = $total;

        return $this;
    }

    public function getBaseTotal(): ?float
    {
        return $this->baseTotal;
    }

    public function setBaseTotal(float $baseTotal): SaleInvoice
    {
        $this->baseTotal = $baseTotal;

        return $this;
    }

    public function getIva(): ?float
    {
        return $this->iva;
    }

    public function setIva(float $iva): SaleInvoice
    {
        $this->iva = $iva;

        return $this;
    }

    /**
     * @return float
     */
    public function getIva21(): float
    {
        return $this->iva21;
    }

    /**
     * @param float $iva21
     *
     * @return SaleInvoice
     */
    public function setIva21($iva21): SaleInvoice
    {
        $this->iva21 = $iva21;

        return $this;
    }

    /**
     * @return float
     */
    public function getIva10(): float
    {
        return $this->iva10;
    }

    public function setIva10(float $iva10): SaleInvoice
    {
        $this->iva10 = $iva10;

        return $this;
    }

    /**
     * @return float
     */
    public function getIva4(): float
    {
        return $this->iva4;
    }

    public function setIva4(float $iva4): SaleInvoice
    {
        $this->iva4 = $iva4;

        return $this;
    }

    /**
     * @return float
     */
    public function getIva0(): float
    {
        return $this->iva0;
    }

    public function setIva0(?float $iva0): SaleInvoice
    {
        $this->iva0 = $iva0;

        return $this;
    }

    public function getIrpf(): ?float
    {
        return $this->irpf;
    }

    public function setIrpf(float $irpf): SaleInvoice
    {
        $this->irpf = $irpf;

        return $this;
    }

    /**
     * @return bool
     */
    public function isHasBeenCounted(): bool
    {
        return $this->hasBeenCounted;
    }

    /**
     * @return bool
     */
    public function getHasBeenCounted(): bool
    {
        return $this->isHasBeenCounted();
    }

    /**
     * @return bool
     */
    public function hasBeenCounted(): bool
    {
        return $this->isHasBeenCounted();
    }

    /**
     * @param bool $hasBeenCounted
     *
     * @return $this
     */
    public function setHasBeenCounted($hasBeenCounted): static
    {
        $this->hasBeenCounted = $hasBeenCounted;

        return $this;
    }

    /**
     * @return float
     */
    public function getDiscount(): float
    {
        return $this->discount;
    }

    /**
     * @param float $discount
     */
    public function setDiscount($discount): void
    {
        $this->discount = $discount;
    }

    public function getCollectionDocumentType(): ?CollectionDocumentType
    {
        return $this->collectionDocumentType;
    }

    public function setCollectionDocumentType(?CollectionDocumentType $collectionDocumentType): SaleInvoice
    {
        $this->collectionDocumentType = $collectionDocumentType;

        return $this;
    }

    public function getDeliveryAddress(): ?PartnerDeliveryAddress
    {
        return $this->deliveryAddress;
    }

    public function setDeliveryAddress(?PartnerDeliveryAddress $deliveryAddress): SaleInvoice
    {
        $this->deliveryAddress = $deliveryAddress;

        return $this;
    }

    public function getObservations(): ?string
    {
        return $this->observations;
    }

    public function setObservations(string $observations): SaleInvoice
    {
        $this->observations = $observations;

        return $this;
    }

    public function getPartnerName(): ?string
    {
        return $this->partnerName;
    }

    public function setPartnerName(?string $partnerName): void
    {
        $this->partnerName = $partnerName;
    }

    public function getPartnerCifNif(): string
    {
        return $this->partnerCifNif;
    }

    public function setPartnerCifNif(string $partnerCifNif): void
    {
        $this->partnerCifNif = $partnerCifNif;
    }

    public function getPartnerMainAddress(): ?string
    {
        return $this->partnerMainAddress;
    }

    public function setPartnerMainAddress(string $partnerMainAddress): void
    {
        $this->partnerMainAddress = $partnerMainAddress;
    }

    public function getPartnerMainCity(): ?City
    {
        return $this->partnerMainCity;
    }

    public function setPartnerMainCity(City $partnerMainCity): void
    {
        $this->partnerMainCity = $partnerMainCity;
    }

    public function getPartnerIban(): ?string
    {
        return $this->partnerIban;
    }

    public function setPartnerIban(?string $partnerIban): void
    {
        $this->partnerIban = $partnerIban;
    }

    public function getPartnerSwift(): ?string
    {
        return $this->partnerSwift;
    }

    public function setPartnerSwift(?string $partnerSwift): void
    {
        $this->partnerSwift = $partnerSwift;
    }

    public function getDateFormatted(): string
    {
        return $this->getDate()->format('d/m/y');
    }

    public function getDiscountFormatted(): string
    {
        return NumberFormatService::formatNumber($this->getDiscount());
    }

    public function getBaseTotalFormatted(): string
    {
        return NumberFormatService::formatNumber($this->getBaseTotal());
    }

    public function getIvaFormatted(): string
    {
        return NumberFormatService::formatNumber($this->getIva());
    }

    public function getIrpfFormatted(): string
    {
        return NumberFormatService::formatNumber($this->getIrpf());
    }

    public function getTotalFormatted(): string
    {
        return NumberFormatService::formatNumber($this->getTotal());
    }

    public function getFirstDeliveryNote(): ?SaleDeliveryNote
    {
        return $this->getDeliveryNotes()->first() ? $this->getDeliveryNotes()->first() : null;
    }

    public function getSaleInvoiceGenerated(): ?SaleInvoice
    {
        return $this->saleInvoiceGenerated;
    }

    public function setSaleInvoiceGenerated(?SaleInvoice $saleInvoiceGenerated): SaleInvoice
    {
        $this->saleInvoiceGenerated = $saleInvoiceGenerated;

        return $this;
    }

    /**
     * FacturaE Methods.
     */
    public function getNumberFacturaE(): string
    {
        return $this->getInvoiceNumber();
    }

    public function getBatchFacturaE(): string
    {
        return $this->getSeries()->getName();
    }

    public function getDateFacturaE(): string
    {
        return $this->getDate()->format('Y-m-d');
    }

    public function getLinesFacturaE(): array
    {
        $lines = [];
        /** @var SaleDeliveryNote $deliveryNote */
        foreach ($this->getDeliveryNotes() as $deliveryNote) {
            /** @var SaleDeliveryNoteLine $deliveryNoteLine */
            foreach ($deliveryNote->getSaleDeliveryNoteLines() as $deliveryNoteLine) {
                $lines[] = $deliveryNoteLine;
            }
        }

        return $lines;
    }

    public function getDueDatesFacturaE(): array
    {
        $dueDates = [];
        foreach ($this->getSaleInvoiceDueDates() as $saleInvoiceDueDate) {
            $dueDates[] = $saleInvoiceDueDate;
        }

        return $dueDates;
    }

    public function getBuyerFacturaE(): BuyerFacturaEInterface
    {
        return $this->getPartner();
    }

    public function getSellerFacturaE(): SellerFacturaEInterface
    {
        return $this->getPartner()->getEnterprise();
    }

    public function getTotalAmountFacturaE(): float
    {
        return $this->getTotal();
    }

    public function getPaymentMethodFacturaE(): string|int|null
    {
        $docType = $this->getCollectionDocumentType()->getName();
        if ($docType === 'CONTADO') {
            return FacturaePayment::TYPE_CASH;
        } elseif (
            $docType === 'TRANSFERENCIA' ||
            $docType === 'INGRESO FACTURA EN BANCO'
        ) {
            return FacturaePayment::TYPE_TRANSFER;
        } elseif ($docType === 'RECIBO') {
            return FacturaePayment::TYPE_DEBIT;
        } elseif ($docType === 'LETRA') {
            return FacturaePayment::TYPE_ACCEPTED_BILL_OF_EXCHANGE;
        } elseif ($docType === 'TALON') {
            return FacturaePayment::TYPE_CHEQUE;
        } elseif ($docType === 'PAGARE') {
            return FacturaePayment::TYPE_CHEQUE;
        } elseif ($docType === 'GIRO POSTAL') {
            return FacturaePayment::TYPE_POSTGIRO;
        } elseif ($docType === '******* ABONO *******') {
            return FacturaePayment::TYPE_REIMBURSEMENT;
        } elseif (
            $docType === 'Confirming per transferencia' ||
            $docType === 'CONFIRMING'
        ) {
            return 20;
        } else {
            return null;
        }
    }

    public function getSaleInvoiceDiscountFacturaE(): float
    {
        return $this->getDiscount() ?: 0;
    }

    public function __toString(): string
    {
        return $this->id ? $this->getInvoiceNumber().'' : '---';
    }
}
