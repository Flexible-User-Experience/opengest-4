<?php

namespace App\Entity\Sale;

use App\Entity\AbstractBase;
use App\Entity\Enterprise\ActivityLine;
use App\Entity\Enterprise\CollectionDocumentType;
use App\Entity\Enterprise\Enterprise;
use App\Entity\Operator\Operator;
use App\Entity\Operator\OperatorWorkRegister;
use App\Entity\Partner\Partner;
use App\Entity\Partner\PartnerBuildingSite;
use App\Entity\Partner\PartnerDeliveryAddress;
use App\Entity\Partner\PartnerOrder;
use App\Entity\Partner\PartnerProject;
use App\Entity\Vehicle\Vehicle;
use App\Service\Format\NumberFormatService;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Class SaleDeliveryNote.
 *
 * @category
 *
 * @ORM\Entity(repositoryClass="App\Repository\Sale\SaleDeliveryNoteRepository")
 *
 * @ORM\Table(name="sale_delivery_note")
 */
class SaleDeliveryNote extends AbstractBase
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @var Enterprise
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Enterprise\Enterprise")
     */
    private $enterprise;

    /**
     * @var Partner
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Partner\Partner", inversedBy="saleDeliveryNotes")
     *
     * @Groups({"api"})
     */
    private $partner;

    /**
     * @var PartnerBuildingSite
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Partner\PartnerBuildingSite")
     *
     * @Groups({"api"})
     */
    private $buildingSite;

    /**
     * @var SaleServiceTariff
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Sale\SaleServiceTariff")
     */
    private $saleServiceTariff;

    /**
     * @var Vehicle
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Vehicle\Vehicle", inversedBy="saleDeliveryNotes")
     *
     * @Groups({"api"})
     */
    private $vehicle;

    /**
     * @var Vehicle
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Vehicle\Vehicle")
     */
    private $secondaryVehicle;

    /**
     * @var Operator
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Operator\Operator")
     */
    private $operator;

    /**
     * @var PartnerOrder
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Partner\PartnerOrder", inversedBy="saleDeliveryNotes")
     *
     * @Groups({"api"})
     */
    private $order;

    /**
     * @var PartnerProject
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Partner\PartnerProject", inversedBy="saleDeliveryNotes")
     */
    private $project;

    /**
     * @var ?string
     *
     * @ORM\Column(type="string", length=50, nullable=true)
     *
     * @Groups({"api"})
     */
    private ?string $deliveryNoteReference;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     *
     * @Groups({"api"})
     */
    private $baseAmount = 0;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $discount = 0;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     *
     * @Groups({"api"})
     */
    private $collectionTerm;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $collectionTerm2;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $collectionTerm3;

    /**
     * @var CollectionDocumentType
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Enterprise\CollectionDocumentType")
     *
     * @Groups({"api"})
     */
    private $collectionDocument;

    /**
     * @var ActivityLine
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Enterprise\ActivityLine")
     */
    private $activityLine;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    private $wontBeInvoiced = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $isInvoiced = false;

    /**
     * @var ?SaleInvoice
     *
     * @ORM\ManyToOne (targetEntity="App\Entity\Sale\SaleInvoice", inversedBy="deliveryNotes")
     */
    private ?SaleInvoice $saleInvoice;

    /**
     * @var ?PartnerDeliveryAddress
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Partner\PartnerDeliveryAddress")
     */
    private $deliveryAddress;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Sale\SaleDeliveryNoteLine", mappedBy="deliveryNote", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $saleDeliveryNoteLines;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Sale\SaleRequestHasDeliveryNote", mappedBy="saleDeliveryNote", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $saleRequestHasDeliveryNotes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Operator\OperatorWorkRegister", mappedBy="saleDeliveryNote", cascade={"persist"})
     */
    private Collection $operatorWorkRegisters;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Purchase\PurchaseInvoiceLine", mappedBy="saleDeliveryNote")
     */
    private Collection $purchaseInvoiceLines;

    /**
     * @var ?string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $serviceDescription;

    /**
     * @var ?string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $place;

    /**
     * @var ?string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $observations;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $printed = false;

    /**
     * Methods.
     */

    /**
     * SaleDeliveryNote constructor.
     */
    public function __construct()
    {
        $this->saleDeliveryNoteLines = new ArrayCollection();
        $this->saleRequestHasDeliveryNotes = new ArrayCollection();
        $this->operatorWorkRegisters = new ArrayCollection();
        $this->purchaseInvoiceLines = new ArrayCollection();
    }

    public function setId(int $id): SaleDeliveryNote
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     *
     * @return $this
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return Enterprise
     */
    public function getEnterprise()
    {
        return $this->enterprise;
    }

    /**
     * @param string $enterprise
     */
    public function setEnterprise($enterprise): void
    {
        $this->enterprise = $enterprise;
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
     */
    public function setPartner($partner): void
    {
        $this->partner = $partner;
    }

    /**
     * @return PartnerBuildingSite
     */
    public function getBuildingSite()
    {
        return $this->buildingSite;
    }

    /**
     * @param PartnerBuildingSite $buildingSite
     */
    public function setBuildingSite($buildingSite): void
    {
        $this->buildingSite = $buildingSite;
    }

    /**
     * @return ?SaleServiceTariff
     */
    public function getSaleServiceTariff(): ?SaleServiceTariff
    {
        return $this->saleServiceTariff;
    }

    public function setSaleServiceTariff(SaleServiceTariff $saleServiceTariff): SaleDeliveryNote
    {
        $this->saleServiceTariff = $saleServiceTariff;

        return $this;
    }

    /**
     * @return ?Vehicle
     */
    public function getVehicle(): ?Vehicle
    {
        return $this->vehicle;
    }

    public function setVehicle(Vehicle $vehicle): SaleDeliveryNote
    {
        $this->vehicle = $vehicle;

        return $this;
    }

    /**
     * @return ?Vehicle
     */
    public function getSecondaryVehicle(): ?Vehicle
    {
        return $this->secondaryVehicle;
    }

    public function setSecondaryVehicle(Vehicle $secondaryVehicle): SaleDeliveryNote
    {
        $this->secondaryVehicle = $secondaryVehicle;

        return $this;
    }

    /**
     * @return ?Operator
     */
    public function getOperator(): ?Operator
    {
        return $this->operator;
    }

    public function setOperator(Operator $operator): SaleDeliveryNote
    {
        $this->operator = $operator;

        return $this;
    }

    /**
     * @return PartnerOrder
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param PartnerOrder $order
     */
    public function setOrder($order): SaleDeliveryNote
    {
        $this->order = $order;

        return $this;
    }

    public function getProject(): ?PartnerProject
    {
        return $this->project;
    }

    public function setProject(?PartnerProject $project): SaleDeliveryNote
    {
        $this->project = $project;

        return $this;
    }

    public function getDeliveryNoteReference(): ?string
    {
        return $this->deliveryNoteReference;
    }

    public function setDeliveryNoteReference($deliveryNoteReference): SaleDeliveryNote
    {
        $this->deliveryNoteReference = $deliveryNoteReference;

        return $this;
    }

    /**
     * @return float
     */
    public function getBaseAmount()
    {
        return $this->baseAmount;
    }

    /**
     * @param float $baseAmount
     */
    public function setBaseAmount($baseAmount): SaleDeliveryNote
    {
        $this->baseAmount = $baseAmount;

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
    public function setDiscount($discount): SaleDeliveryNote
    {
        $this->discount = $discount;

        return $this;
    }

    public function getCollectionTerm(): ?int
    {
        return $this->collectionTerm;
    }

    public function setCollectionTerm(?int $collectionTerm): SaleDeliveryNote
    {
        $this->collectionTerm = $collectionTerm;

        return $this;
    }

    public function getCollectionTerm2(): ?int
    {
        return $this->collectionTerm2;
    }

    public function setCollectionTerm2(?int $collectionTerm2): SaleDeliveryNote
    {
        $this->collectionTerm2 = $collectionTerm2;

        return $this;
    }

    public function getCollectionTerm3(): ?int
    {
        return $this->collectionTerm3;
    }

    public function setCollectionTerm3(?int $collectionTerm3): SaleDeliveryNote
    {
        $this->collectionTerm3 = $collectionTerm3;

        return $this;
    }

    /**
     * @return CollectionDocumentType
     */
    public function getCollectionDocument()
    {
        return $this->collectionDocument;
    }

    /**
     * @param CollectionDocumentType $collectionDocument
     */
    public function setCollectionDocument($collectionDocument): SaleDeliveryNote
    {
        $this->collectionDocument = $collectionDocument;

        return $this;
    }

    /**
     * @return ActivityLine
     */
    public function getActivityLine()
    {
        return $this->activityLine;
    }

    /**
     * @param ActivityLine $activityLine
     */
    public function setActivityLine($activityLine): SaleDeliveryNote
    {
        $this->activityLine = $activityLine;

        return $this;
    }

    public function isWontBeInvoiced(): bool
    {
        return $this->wontBeInvoiced;
    }

    /**
     * @param bool $wontBeInvoiced
     *
     * @return $this
     */
    public function setWontBeInvoiced($wontBeInvoiced): SaleDeliveryNote
    {
        $this->wontBeInvoiced = $wontBeInvoiced;

        return $this;
    }

    /**
     * @return ?SaleInvoice
     */
    public function getSaleInvoice(): ?SaleInvoice
    {
        return $this->saleInvoice;
    }

    /**
     * @param ?SaleInvoice $saleInvoice
     */
    public function setSaleInvoice(?SaleInvoice $saleInvoice): SaleDeliveryNote
    {
        $this->saleInvoice = $saleInvoice;
        if ($saleInvoice) {
            $this->setIsInvoiced(true);
        }

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getSaleDeliveryNoteLines()
    {
        return $this->saleDeliveryNoteLines;
    }

    /**
     * @param ArrayCollection $SaleDeliveryNoteLines
     *
     * @return $this
     */
    public function setSaleDeliveryNoteLines($SaleDeliveryNoteLines)
    {
        $this->saleDeliveryNoteLines = $SaleDeliveryNoteLines;

        return $this;
    }

    /**
     * @return $this
     */
    public function addSaleDeliveryNoteLine(SaleDeliveryNoteLine $saleDeliveryNoteLine)
    {
        if (!$this->saleDeliveryNoteLines->contains($saleDeliveryNoteLine)) {
            $this->saleDeliveryNoteLines->add($saleDeliveryNoteLine);
            $saleDeliveryNoteLine->setDeliveryNote($this);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function removeSaleDeliveryNoteLine(SaleDeliveryNoteLine $saleDeliveryNoteLine)
    {
        if ($this->saleDeliveryNoteLines->contains($saleDeliveryNoteLine)) {
            $this->saleDeliveryNoteLines->removeElement($saleDeliveryNoteLine);
        }

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getSaleRequestHasDeliveryNotes()
    {
        return $this->saleRequestHasDeliveryNotes;
    }

    /**
     * @param ArrayCollection $saleRequestHasDeliveryNotes
     *
     * @return $this
     */
    public function setSaleRequestHasDeliveryNotes($saleRequestHasDeliveryNotes)
    {
        $this->saleRequestHasDeliveryNotes = $saleRequestHasDeliveryNotes;

        return $this;
    }

    /**
     * @param SaleRequestHasDeliveryNote $saleRequestHasDeliveryNotes
     *
     * @return $this
     */
    public function addSaleRequestHasDeliveryNote($saleRequestHasDeliveryNotes)
    {
        if (!$this->saleRequestHasDeliveryNotes->contains($saleRequestHasDeliveryNotes)) {
            $this->saleRequestHasDeliveryNotes->add($saleRequestHasDeliveryNotes);
            $saleRequestHasDeliveryNotes->setSaleDeliveryNote($this);
        }

        return $this;
    }

    /**
     * @param SaleRequestHasDeliveryNote $saleRequestHasDeliveryNotes
     *
     * @return $this
     */
    public function removeRequestHasDeliveryNote($saleRequestHasDeliveryNotes)
    {
        if ($this->saleRequestHasDeliveryNotes->contains($saleRequestHasDeliveryNotes)) {
            $this->saleRequestHasDeliveryNotes->removeElement($saleRequestHasDeliveryNotes);
        }

        return $this;
    }

    public function getOperatorWorkRegisters(): Collection
    {
        return $this->operatorWorkRegisters;
    }

    /**
     * @return $this
     */
    public function setOperatorWorkRegisters(Collection $operatorWorkRegisters): SaleDeliveryNote
    {
        $this->operatorWorkRegisters = $operatorWorkRegisters;

        return $this;
    }

    /**
     * @return $this
     */
    public function addOperatorWorkRegister(OperatorWorkRegister $operatorWorkRegister): SaleDeliveryNote
    {
        if (!$this->operatorWorkRegisters->contains($operatorWorkRegister)) {
            $this->operatorWorkRegisters->add($operatorWorkRegister);
            $operatorWorkRegister->setSaleDeliveryNote($this);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function removeOperatorWorkRegister(OperatorWorkRegister $operatorWorkRegister): SaleDeliveryNote
    {
        if ($this->operatorWorkRegisters->contains($operatorWorkRegister)) {
            $this->operatorWorkRegisters->removeElement($operatorWorkRegister);
        }

        return $this;
    }

    /**
     * @return ?string
     */
    public function getServiceDescription(): ?string
    {
        return $this->serviceDescription;
    }

    public function setServiceDescription(?string $serviceDescription): SaleDeliveryNote
    {
        $this->serviceDescription = $serviceDescription;

        return $this;
    }

    /**
     * @return ?string
     */
    public function getPlace(): ?string
    {
        return $this->place;
    }

    public function setPlace(?string $place): SaleDeliveryNote
    {
        $this->place = $place;

        return $this;
    }

    /**
     * @return ?string
     */
    public function getObservations(): ?string
    {
        return $this->observations;
    }

    public function setObservations($observations): SaleDeliveryNote
    {
        $this->observations = $observations;

        return $this;
    }

    public function getDeliveryAddress(): ?PartnerDeliveryAddress
    {
        return $this->deliveryAddress;
    }

    public function setDeliveryAddress(?PartnerDeliveryAddress $deliveryAddress): SaleDeliveryNote
    {
        $this->deliveryAddress = $deliveryAddress;

        return $this;
    }

    public function getPurchaseInvoiceLines(): Collection
    {
        return $this->purchaseInvoiceLines;
    }

    public function setPurchaseInvoiceLines(Collection $purchaseInvoiceLines): SaleDeliveryNote
    {
        $this->purchaseInvoiceLines = $purchaseInvoiceLines;

        return $this;
    }

    /**
     * @Groups({"api"})
     *
     * @return string
     */
    public function getDateToString()
    {
        return $this->getDate()->format('d/m/Y');
    }

    /**
     * Custom getters without property.
     */
    public function getSaleRequestServiceDate(): ?\DateTime
    {
        return $this->getSaleRequest() ? $this->getSaleRequest()->getServiceDate() : null;
    }

    public function getSaleRequestServiceTime(): ?int
    {
        return $this->getSaleRequest() ? $this->getSaleRequest()->getServiceTimeString() : null;
    }

    public function getSaleRequestNumber(): ?int
    {
        return $this->getSaleRequest() ? $this->getSaleRequest()->getId() : null;
    }

    public function getHourPrice(): ?float
    {
        return $this->getSaleRequest() ? $this->getSaleRequest()->getHourPrice() : null;
    }

    public function getMiniumHours(): ?float
    {
        return $this->getSaleRequest() ? $this->getSaleRequest()->getMiniumHours() : null;
    }

    public function getDisplacement(): ?float
    {
        return $this->getSaleRequest() ? $this->getSaleRequest()->getDisplacement() : null;
    }

    public function getMiniumHolidayHours(): ?float
    {
        return $this->getSaleRequest() ? $this->getSaleRequest()->getMiniumHolidayHours() : null;
    }

    public function getIncreaseForHolidays(): ?float
    {
        return $this->getSaleRequest() ? $this->getSaleRequest()->getIncreaseForHolidays() : null;
    }

    public function getIncreaseForHolidaysPercentage(): ?float
    {
        return $this->getSaleRequest() ? $this->getSaleRequest()->getIncreaseForHolidaysPercentage() : null;
    }

    public function getContactPersonName(): ?string
    {
        return $this->getSaleRequest() ? $this->getSaleRequest()->getContactPersonName() : null;
    }

    public function getContactPersonPhone(): ?string
    {
        return $this->getSaleRequest() ? $this->getSaleRequest()->getContactPersonPhone() : null;
    }

    public function getContactPersonEmail(): ?string
    {
        return $this->getSaleRequest() ? $this->getSaleRequest()->getContactPersonEmail() : null;
    }

    public function getTotalLines(): float
    {
        $totalPrice = 0;
        foreach ($this->getSaleDeliveryNoteLines() as $deliveryNoteLine) {
            $subtotal = $deliveryNoteLine->getTotal();
            $totalPrice = $totalPrice + $subtotal;
        }

        return $totalPrice;
    }

    public function getFinalTotal(): float
    {
        $finalTotal = 0;
        /** @var SaleDeliveryNoteLine $deliveryNoteLine */
        foreach ($this->getSaleDeliveryNoteLines() as $deliveryNoteLine) {
            $subtotal = $deliveryNoteLine->getTotal() * (1 - $this->getDiscount() / 100) * (1 + $deliveryNoteLine->getIva() / 100 - $deliveryNoteLine->getIrpf() / 100);
            $finalTotal = $finalTotal + $subtotal;
        }

        return $finalTotal;
    }

    public function getFinalTotalWithDiscounts(): float
    {
        $finalTotalWithDiscounts = 0;
        /** @var SaleDeliveryNoteLine $deliveryNoteLine */
        foreach ($this->getSaleDeliveryNoteLines() as $deliveryNoteLine) {
            $subtotal = $deliveryNoteLine->getTotal() * (1 + $deliveryNoteLine->getIva() / 100 - $deliveryNoteLine->getIrpf() / 100);
            $finalTotalWithDiscounts = $finalTotalWithDiscounts + $subtotal;
        }

        return $finalTotalWithDiscounts * (1 - $this->getDiscount() / 100) * (1 - ($this->getSaleInvoice() ? $this->getSaleInvoice()->getDiscount() : 0) / 100);
    }

    /**
     * @Groups({"api"})
     */
    public function getBaseTotalWithDiscounts(): float
    {
        $baseTotalWithDiscounts = 0;
        /** @var SaleDeliveryNoteLine $deliveryNoteLine */
        foreach ($this->getSaleDeliveryNoteLines() as $deliveryNoteLine) {
            $subtotal = $deliveryNoteLine->getTotal();
            $baseTotalWithDiscounts += $subtotal;
        }

        return $baseTotalWithDiscounts * (1 - $this->getDiscount() / 100) * (1 - ($this->getSaleInvoice() ? $this->getSaleInvoice()->getDiscount() : 0) / 100);
    }

    /**
     * @Groups({"api"})
     */
    public function getBaseTotalWithDiscountsFormatted(): string
    {
        return NumberFormatService::formatNumber($this->getBaseTotalWithDiscounts(), true);
    }

    public function getDiscountTotal(): float
    {
        $discountTotal = 0;
        /** @var SaleDeliveryNoteLine $deliveryNoteLine */
        foreach ($this->getSaleDeliveryNoteLines() as $deliveryNoteLine) {
            $subtotal = $deliveryNoteLine->getTotal() * ($this->getDiscount() / 100) + $deliveryNoteLine->getUnits() * $deliveryNoteLine->getPriceUnit() * ($deliveryNoteLine->getDiscount() / 100);
            $discountTotal += $subtotal;
        }

        return $discountTotal;
    }

    public function getIvaTotal(): float
    {
        $ivaTotal = 0;
        /** @var SaleDeliveryNoteLine $deliveryNoteLine */
        foreach ($this->getSaleDeliveryNoteLines() as $deliveryNoteLine) {
            $subtotal = $deliveryNoteLine->getTotal() * (1 - $this->getDiscount() / 100) * ($deliveryNoteLine->getIva() / 100);
            $ivaTotal += $subtotal;
        }

        return $ivaTotal;
    }

    public function getIrpfTotal(): float
    {
        $irpfTotal = 0;
        /** @var SaleDeliveryNoteLine $deliveryNoteLine */
        foreach ($this->getSaleDeliveryNoteLines() as $deliveryNoteLine) {
            $subtotal = $deliveryNoteLine->getTotal() * (1 - $this->getDiscount() / 100) * ($deliveryNoteLine->getIrpf() / 100);
            $irpfTotal += $subtotal;
        }

        return $irpfTotal;
    }

    public function getTotalHours(): float
    {
        $totalHours = 0;
        /** @var SaleDeliveryNoteLine $deliveryNoteLine */
        foreach ($this->getSaleDeliveryNoteLines() as $deliveryNoteLine) {
            if ($deliveryNoteLine->getSaleItem()->getId() <= 3) {
                $totalHours += $deliveryNoteLine->getUnits();
            }
        }

        return $totalHours;
    }

    public function getTotalHoursFromWorkRegisters(): float
    {
        $totalHoursFromWorkRegisters = 0;
        /** @var OperatorWorkRegister $operatorWorkRegister */
        foreach ($this->getOperatorWorkRegisters() as $operatorWorkRegister) {
            if (str_contains($operatorWorkRegister->getDescription(), 'Hora')) {
                $totalHoursFromWorkRegisters += $operatorWorkRegister->getUnits();
            }
        }

        return $totalHoursFromWorkRegisters;
    }

    public function getHourPriceFormatted(): string
    {
        return NumberFormatService::formatNumber($this->getHourPrice());
    }

    public function getTotalLinesFormatted(): string
    {
        return NumberFormatService::formatNumber($this->getTotalLines());
    }

    public function getTotalHoursFormatted(): string
    {
        return NumberFormatService::formatNumber($this->getTotalHours());
    }

    public function getTotalHoursFromWorkRegistersFormatted(): string
    {
        return NumberFormatService::formatNumber($this->getTotalHoursFromWorkRegisters());
    }

    public function getDiscountFormatted(): string
    {
        return NumberFormatService::formatNumber($this->getDiscount());
    }

    public function getBaseAmountFormatted(): string
    {
        return NumberFormatService::formatNumber($this->getBaseAmount());
    }

    public function getFinalTotalFormatted(): string
    {
        return NumberFormatService::formatNumber($this->getFinalTotal());
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getId() ?: '---';
    }

    public function getSaleRequest(): ?SaleRequest
    {
        $value = null;
        if ($this->getSaleRequestHasDeliveryNotes()->count() > 0) {
            /** @var SaleRequestHasDeliveryNote $saleRequestHasDeliveryNote */
            $saleRequestHasDeliveryNote = $this->getSaleRequestHasDeliveryNotes()->first();
            if ($saleRequestHasDeliveryNote) {
                $value = $saleRequestHasDeliveryNote->getSaleRequest();
            }
        }

        return $value;
    }

    public function isInvoiced(): bool
    {
        return $this->isInvoiced;
    }

    public function setIsInvoiced(bool $isInvoiced): SaleDeliveryNote
    {
        $this->isInvoiced = $isInvoiced;

        return $this;
    }

    public function isPrinted(): bool
    {
        return $this->printed;
    }

    public function setPrinted(bool $printed): void
    {
        $this->printed = $printed;
    }
}
