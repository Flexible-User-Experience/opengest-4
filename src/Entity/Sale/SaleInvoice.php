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
 * @ORM\Entity(repositoryClass="App\Repository\Sale\SaleInvoiceRepository")
 * @ORM\Table(name="sale_invoice")
 */
class SaleInvoice extends AbstractBase implements InvoiceFacturaEInterface
{
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Sale\SaleDeliveryNote", mappedBy="saleInvoice")
     * @Groups({"api"})
     * @Assert\Count(
     *     min = 1,
     *     minMessage = "La factura tiene que tener un albarán como mínimo"
     * )
     */
    private Collection $deliveryNotes;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @var Partner
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Partner\Partner",inversedBy="saleInvoices")
     */
    private $partner;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $invoiceNumber;

    /**
     * @var SaleInvoiceSeries
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Setting\SaleInvoiceSeries")
     */
    private $series;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $type;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $total;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $baseTotal;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $iva = 0;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $iva21 = 0;
    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $iva10 = 0;
    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $iva4 = 0;
    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $iva0 = 0;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $irpf = 0;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    private $hasBeenCounted = false;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $discount = 0;

    /**
     * @var ?PartnerDeliveryAddress
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Partner\PartnerDeliveryAddress")
     */
    private $deliveryAddress;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Sale\SaleInvoiceDueDate", mappedBy="saleInvoice", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $saleInvoiceDueDates;

    /**
     * @var ?CollectionDocumentType
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Enterprise\CollectionDocumentType")
     */
    private $collectionDocumentType;

    /**
     * @var ?string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $observations;

    /**
     * @var ?string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $partnerName;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $partnerCifNif;

    /**
     * @var ?string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $partnerMainAddress;

    /**
     * @var City
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Setting\City")
     */
    private $partnerMainCity;

    /**
     * @var ?string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $partnerIban;

    /**
     * @var ?string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $partnerSwift;

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
    public function setDeliveryNotes(Collection $deliveryNotes): SaleInvoice
    {
        $this->deliveryNotes = $deliveryNotes;

        return $this;
    }

    /**
     * @return $this
     */
    public function addDeliveryNote(SaleDeliveryNote $deliveryNote): SaleInvoice
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
    public function removeDeliveryNote(SaleDeliveryNote $deliveryNote): SaleInvoice
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
    public function setSaleInvoiceDueDates(Collection $saleInvoiceDueDates): SaleInvoice
    {
        $this->saleInvoiceDueDates = $saleInvoiceDueDates;

        return $this;
    }

    /**
     * @return $this
     */
    public function addSaleInvoiceDueDate(SaleInvoiceDueDate $saleInvoiceDueDate): SaleInvoice
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
    public function removeSaleInvoiceDueDate(SaleInvoiceDueDate $saleInvoiceDueDate): SaleInvoice
    {
        if ($this->saleInvoiceDueDates->contains($saleInvoiceDueDate)) {
            $this->saleInvoiceDueDates->removeElement($saleInvoiceDueDate);
            $saleInvoiceDueDate->setSaleInvoice(null);
        }

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param DateTime $date
     *
     * @return $this
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return Partner
     */
    public function getPartner()
    {
        return $this->partner;
    }

    /**
     * @param Partner $partner
     *
     * @return $this
     */
    public function setPartner($partner)
    {
        $this->partner = $partner;

        return $this;
    }

    /**
     * @return int
     */
    public function getInvoiceNumber()
    {
        return $this->invoiceNumber;
    }

    /**
     * @param int $invoiceNumber
     *
     * @return $this
     */
    public function setInvoiceNumber($invoiceNumber)
    {
        $this->invoiceNumber = $invoiceNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getFullInvoiceNumber()
    {
        return ($this->getSeries() ? $this->getSeries()->getPrefix() : '???').'/'.$this->getInvoiceNumber();
    }

    /**
     * @return SaleInvoiceSeries
     */
    public function getSeries()
    {
        return $this->series;
    }

    /**
     * @param SaleInvoiceSeries $series
     *
     * @return $this
     */
    public function setSeries($series)
    {
        $this->series = $series;

        return $this;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param int $type
     *
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return float
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @param float $total
     *
     * @return $this
     */
    public function setTotal($total)
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
    public function getIva21()
    {
        return $this->iva21;
    }

    /**
     * @param float $iva21
     *
     * @return SaleInvoice
     */
    public function setIva21($iva21)
    {
        $this->iva21 = $iva21;

        return $this;
    }

    /**
     * @return float
     */
    public function getIva10()
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
    public function getIva4()
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
    public function getIva0()
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
    public function isHasBeenCounted()
    {
        return $this->hasBeenCounted;
    }

    /**
     * @return bool
     */
    public function getHasBeenCounted()
    {
        return $this->isHasBeenCounted();
    }

    /**
     * @return bool
     */
    public function hasBeenCounted()
    {
        return $this->isHasBeenCounted();
    }

    /**
     * @param bool $hasBeenCounted
     *
     * @return $this
     */
    public function setHasBeenCounted($hasBeenCounted)
    {
        $this->hasBeenCounted = $hasBeenCounted;

        return $this;
    }

    /**
     * @return float
     */
    public function getDiscount()
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

    /**
     * @return string
     */
    public function getPartnerMainAddress(): ?string
    {
        return $this->partnerMainAddress;
    }

    public function setPartnerMainAddress(string $partnerMainAddress): void
    {
        $this->partnerMainAddress = $partnerMainAddress;
    }

    /**
     * @return ?City
     */
    public function getPartnerMainCity(): ?City
    {
        return $this->partnerMainCity;
    }

    public function setPartnerMainCity(City $partnerMainCity): void
    {
        $this->partnerMainCity = $partnerMainCity;
    }

    /**
     * @return ?string
     */
    public function getPartnerIban(): ?string
    {
        return $this->partnerIban;
    }

    /**
     * @param ?string $partnerIban
     */
    public function setPartnerIban(?string $partnerIban): void
    {
        $this->partnerIban = $partnerIban;
    }

    /**
     * @return ?string
     */
    public function getPartnerSwift(): ?string
    {
        return $this->partnerSwift;
    }

    /**
     * @param ?string $partnerSwift
     */
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

    /**
     * FacturaE Methods.
     */
    public function getNumberFacturaE(): string
    {
        return $this->getInvoiceNumber();
    }

    public function getBatchFacturaE(): string
    {
        return $this->getSeries()->getPrefix();
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

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->id ? $this->getInvoiceNumber().'' : '---';
    }
}
