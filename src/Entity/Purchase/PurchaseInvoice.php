<?php

namespace App\Entity\Purchase;

use App\Entity\AbstractBase;
use App\Entity\Enterprise\Enterprise;
use App\Entity\Partner\Partner;
use App\Entity\Partner\PartnerDeliveryAddress;
use App\Entity\Setting\City;
use App\Service\Format\NumberFormatService;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class PurchaseInvoice.
 *
 * @category
 *
 * @ORM\Entity(repositoryClass="App\Repository\Purchase\PurchaseInvoiceRepository")
 * @ORM\Table(name="purchase_invoice")
 */
class PurchaseInvoice extends AbstractBase
{
    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private DateTime $date;

    /**
     * @var Partner
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Partner\Partner",inversedBy="purchaseInvoices")
     */
    private Partner $partner;

    /**
     * @var Enterprise
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Enterprise\Enterprise")
     */
    private Enterprise $enterprise;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Purchase\PurchaseInvoiceLine", mappedBy="purchaseInvoice", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private Collection $purchaseInvoiceLines;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Purchase\PurchaseInvoiceDueDate", mappedBy="purchaseInvoice", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private Collection $purchaseInvoiceDueDates;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private int $invoiceNumber;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", nullable = true)
     */
    private ?string $reference;

    /**
     * @var float|null
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private ?float $total;

    /**
     * @var float|null
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private ?float $baseTotal;

    /**
     * @var float|null
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private ?float $iva = 0;

    /**
     * @var float|null
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private ?float $iva21 = 0;
    /**
     * @var float|null
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private ?float $iva10 = 0;

    /**
     * @var float|null
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private ?float $iva4 = 0;

    /**
     * @var float|null
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private ?float $iva0 = 0;

    /**
     * @var float|null
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private ?float $irpf = 0;

    /**
     * @var ?PartnerDeliveryAddress
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Partner\PartnerDeliveryAddress")
     */
    private ?PartnerDeliveryAddress $deliveryAddress;

    /**
     * @var ?string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $observations;

    /**
     * @var ?string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $partnerName;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private string $partnerCifNif;

    /**
     * @var ?string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $partnerMainAddress;

    /**
     * @var City
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Setting\City")
     */
    private City $partnerMainCity;

    /**
     * @var ?string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $partnerIban;

    /**
     * @var ?string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $partnerSwift;

    /**
     * @var ?int
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $accountingAccount;

    /**
     * Methods.
     */
    public function __construct()
    {
        $this->purchaseInvoiceLines = new ArrayCollection();
        $this->purchaseInvoiceDueDates = new ArrayCollection();
    }

    /**
     * @return ArrayCollection|Collection
     */
    public function getPurchaseInvoiceLines(): ArrayCollection|Collection
    {
        return $this->purchaseInvoiceLines;
    }

    /**
     * @param ArrayCollection $purchaseInvoiceLines
     *
     * @return $this
     */
    public function setPurchaseInvoiceLines(ArrayCollection $purchaseInvoiceLines): static
    {
        $this->purchaseInvoiceLines = $purchaseInvoiceLines;

        return $this;
    }

    /**
     * @return $this
     */
    public function addPurchaseInvoiceLine(PurchaseInvoiceLine $purchaseInvoiceLine): static
    {
        if (!$this->purchaseInvoiceLines->contains($purchaseInvoiceLine)) {
            $this->purchaseInvoiceLines->add($purchaseInvoiceLine);
            $purchaseInvoiceLine->setPurchaseInvoice($this);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function removePurchaseInvoiceLine(PurchaseInvoiceLine $purchaseInvoiceLine): static
    {
        if ($this->purchaseInvoiceLines->contains($purchaseInvoiceLine)) {
            $this->purchaseInvoiceLines->removeElement($purchaseInvoiceLine);
        }

        return $this;
    }

    /**
     * @return ArrayCollection|Collection
     */
    public function getPurchaseInvoiceDueDates(): ArrayCollection|Collection
    {
        return $this->purchaseInvoiceDueDates;
    }

    /**
     * @param ArrayCollection $purchaseInvoiceDueDates
     *
     * @return $this
     */
    public function setPurchaseInvoiceDueDates(ArrayCollection $purchaseInvoiceDueDates): static
    {
        $this->purchaseInvoiceDueDates = $purchaseInvoiceDueDates;

        return $this;
    }

    /**
     * @return $this
     */
    public function addPurchaseInvoiceDueDate(PurchaseInvoiceDueDate $purchaseInvoiceDueDate): static
    {
        if (!$this->purchaseInvoiceDueDates->contains($purchaseInvoiceDueDate)) {
            $this->purchaseInvoiceDueDates->add($purchaseInvoiceDueDate);
            $purchaseInvoiceDueDate->setPurchaseInvoice($this);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function removePurchaseInvoiceDueDate(PurchaseInvoiceDueDate $purchaseInvoiceDueDate): static
    {
        if ($this->purchaseInvoiceDueDates->contains($purchaseInvoiceDueDate)) {
            $this->purchaseInvoiceDueDates->removeElement($purchaseInvoiceDueDate);
        }

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDate(): DateTime
    {
        return $this->date;
    }

    /**
     * @param DateTime $date
     *
     * @return $this
     */
    public function setDate(DateTime $date): static
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
    public function setPartner(Partner $partner): static
    {
        $this->partner = $partner;

        return $this;
    }

    /**
     * @return Enterprise
     */
    public function getEnterprise(): Enterprise
    {
        return $this->enterprise;
    }

    /**
     * @param Enterprise $enterprise
     *
     * @return PurchaseInvoice
     */
    public function setEnterprise(Enterprise $enterprise): PurchaseInvoice
    {
        $this->enterprise = $enterprise;

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
    public function setInvoiceNumber(int $invoiceNumber): static
    {
        $this->invoiceNumber = $invoiceNumber;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getTotal(): ?float
    {
        return $this->total;
    }

    /**
     * @param float $total
     *
     * @return $this
     */
    public function setTotal(float $total): static
    {
        $this->total = $total;

        return $this;
    }

    public function getBaseTotal(): ?float
    {
        return $this->baseTotal;
    }

    public function setBaseTotal(float $baseTotal): PurchaseInvoice
    {
        $this->baseTotal = $baseTotal;

        return $this;
    }

    public function getIva(): ?float
    {
        return $this->iva;
    }

    public function setIva(float $iva): PurchaseInvoice
    {
        $this->iva = $iva;

        return $this;
    }

    /**
     * @return float|int|null
     */
    public function getIva21(): float|int|null
    {
        return $this->iva21;
    }

    /**
     * @param float $iva21
     *
     * @return PurchaseInvoice
     */
    public function setIva21(float $iva21): PurchaseInvoice
    {
        $this->iva21 = $iva21;

        return $this;
    }

    /**
     * @return float|int|null
     */
    public function getIva10(): float|int|null
    {
        return $this->iva10;
    }

    public function setIva10(float $iva10): PurchaseInvoice
    {
        $this->iva10 = $iva10;

        return $this;
    }

    /**
     * @return float|int|null
     */
    public function getIva4(): float|int|null
    {
        return $this->iva4;
    }

    public function setIva4(float $iva4): PurchaseInvoice
    {
        $this->iva4 = $iva4;

        return $this;
    }

    /**
     * @return float|int|null
     */
    public function getIva0(): float|int|null
    {
        return $this->iva0;
    }

    public function setIva0(?float $iva0): PurchaseInvoice
    {
        $this->iva0 = $iva0;

        return $this;
    }

    public function getIrpf(): ?float
    {
        return $this->irpf;
    }

    public function setIrpf(float $irpf): PurchaseInvoice
    {
        $this->irpf = $irpf;

        return $this;
    }

    public function getDeliveryAddress(): ?PartnerDeliveryAddress
    {
        return $this->deliveryAddress;
    }

    public function setDeliveryAddress(?PartnerDeliveryAddress $deliveryAddress): PurchaseInvoice
    {
        $this->deliveryAddress = $deliveryAddress;

        return $this;
    }

    public function getObservations(): ?string
    {
        return $this->observations;
    }

    public function setObservations(string $observations): PurchaseInvoice
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
     * @return string|null
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

    /**
     * @return int|null
     */
    public function getAccountingAccount(): ?int
    {
        return $this->accountingAccount;
    }

    /**
     * @param int|null $accountingAccount
     *
     * @return PurchaseInvoice
     */
    public function setAccountingAccount(?int $accountingAccount): PurchaseInvoice
    {
        $this->accountingAccount = $accountingAccount;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getReference(): ?string
    {
        return $this->reference;
    }

    /**
     * @param string|null $reference
     *
     * @return PurchaseInvoice
     */
    public function setReference(?string $reference): PurchaseInvoice
    {
        $this->reference = $reference;

        return $this;
    }

    /**
     * Custom formats
     */

    public function getDateFormatted(): string
    {
        return $this->getDate()->format('d/m/y');
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

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->id ? $this->getInvoiceNumber().'' : '---';
    }
}
