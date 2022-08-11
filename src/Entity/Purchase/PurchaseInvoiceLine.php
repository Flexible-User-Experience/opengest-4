<?php

namespace App\Entity\Purchase;

use App\Entity\AbstractBase;
use App\Entity\Operator\Operator;
use App\Entity\Sale\SaleDeliveryNote;
use App\Entity\Setting\CostCenter;
use App\Entity\Vehicle\Vehicle;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class PurchaseInvoiceLine.
 *
 * @category
 *
 * @ORM\Entity(repositoryClass="App\Repository\Purchase\PurchaseInvoiceLineRepository")
 * @ORM\Table(name="purchase_invoice_line")
 */
class PurchaseInvoiceLine extends AbstractBase
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Purchase\PurchaseInvoice", inversedBy="purchaseInvoiceLines")
     */
    private PurchaseInvoice $purchaseInvoice;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Purchase\PurchaseItem", inversedBy="purchaseInvoiceLines")
     */
    private PurchaseItem $purchaseItem;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Operator\Operator", inversedBy="purchaseInvoiceLines")
     */
    private Operator $operator;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Vehicle\Vehicle", inversedBy="purchaseInvoiceLines")
     */
    private Vehicle $vehicle;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Sale\SaleDeliveryNote", inversedBy="purchaseInvoiceLines")
     */
    private SaleDeliveryNote $saleDeliveryNote;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Setting\CostCenter", inversedBy="purchaseInvoiceLines")
     */
    private CostCenter $costCenter;

    /**
     * @var float|null
     *
     * @ORM\Column(type="float", nullable=true, scale=4)
     */
    private ?float $units = 0;

    /**
     * @var float
     *
     * @ORM\Column(type="float", scale=4)
     */
    private float $priceUnit = 0;

    /**
     * @var float|null
     *
     * @ORM\Column(type="float", nullable=true, scale=4)
     */
    private ?float $total;

    /**
     * @var float|null
     *
     * @ORM\Column(type="float", nullable=true, scale=4)
     */
    private ?float $baseTotal;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $description;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */
    private float $iva;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */
    private float $irpf;

    /**
     * Methods.
     */
    /**
     * @return PurchaseInvoice
     */
    public function getPurchaseInvoice(): PurchaseInvoice
    {
        return $this->purchaseInvoice;
    }

    /**
     * @param PurchaseInvoice $purchaseInvoice
     *
     * @return PurchaseInvoiceLine
     */
    public function setPurchaseInvoice(PurchaseInvoice $purchaseInvoice): PurchaseInvoiceLine
    {
        $this->purchaseInvoice = $purchaseInvoice;

        return $this;
    }

    /**
     * @return PurchaseItem
     */
    public function getPurchaseItem(): PurchaseItem
    {
        return $this->purchaseItem;
    }

    /**
     * @param PurchaseItem $purchaseItem
     *
     * @return PurchaseInvoiceLine
     */
    public function setPurchaseItem(PurchaseItem $purchaseItem): PurchaseInvoiceLine
    {
        $this->purchaseItem = $purchaseItem;

        return $this;
    }

    /**
     * @return Operator
     */
    public function getOperator(): Operator
    {
        return $this->operator;
    }

    /**
     * @param Operator $operator
     *
     * @return PurchaseInvoiceLine
     */
    public function setOperator(Operator $operator): PurchaseInvoiceLine
    {
        $this->operator = $operator;

        return $this;
    }

    /**
     * @return Vehicle
     */
    public function getVehicle(): Vehicle
    {
        return $this->vehicle;
    }

    /**
     * @param Vehicle $vehicle
     *
     * @return PurchaseInvoiceLine
     */
    public function setVehicle(Vehicle $vehicle): PurchaseInvoiceLine
    {
        $this->vehicle = $vehicle;

        return $this;
    }

    /**
     * @return SaleDeliveryNote
     */
    public function getSaleDeliveryNote(): SaleDeliveryNote
    {
        return $this->saleDeliveryNote;
    }

    /**
     * @param SaleDeliveryNote $saleDeliveryNote
     *
     * @return PurchaseInvoiceLine
     */
    public function setSaleDeliveryNote(SaleDeliveryNote $saleDeliveryNote): PurchaseInvoiceLine
    {
        $this->saleDeliveryNote = $saleDeliveryNote;

        return $this;
    }

    /**
     * @return CostCenter
     */
    public function getCostCenter(): CostCenter
    {
        return $this->costCenter;
    }

    /**
     * @param CostCenter $costCenter
     *
     * @return PurchaseInvoiceLine
     */
    public function setCostCenter(CostCenter $costCenter): PurchaseInvoiceLine
    {
        $this->costCenter = $costCenter;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getUnits(): ?float
    {
        return $this->units;
    }

    /**
     * @param float|null $units
     *
     * @return PurchaseInvoiceLine
     */
    public function setUnits(?float $units): PurchaseInvoiceLine
    {
        $this->units = $units;

        return $this;
    }

    /**
     * @return float
     */
    public function getPriceUnit(): float
    {
        return $this->priceUnit;
    }

    /**
     * @param float $priceUnit
     *
     * @return PurchaseInvoiceLine
     */
    public function setPriceUnit(float $priceUnit): PurchaseInvoiceLine
    {
        $this->priceUnit = $priceUnit;

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
     * @param float|null $total
     *
     * @return PurchaseInvoiceLine
     */
    public function setTotal(?float $total): PurchaseInvoiceLine
    {
        $this->total = $total;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getBaseTotal(): ?float
    {
        return $this->baseTotal;
    }

    /**
     * @param float|null $baseTotal
     *
     * @return PurchaseInvoiceLine
     */
    public function setBaseTotal(?float $baseTotal): PurchaseInvoiceLine
    {
        $this->baseTotal = $baseTotal;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     *
     * @return PurchaseInvoiceLine
     */
    public function setDescription(?string $description): PurchaseInvoiceLine
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return float
     */
    public function getIva(): float
    {
        return $this->iva;
    }

    /**
     * @param float $iva
     *
     * @return PurchaseInvoiceLine
     */
    public function setIva(float $iva): PurchaseInvoiceLine
    {
        $this->iva = $iva;

        return $this;
    }

    /**
     * @return float
     */
    public function getIrpf(): float
    {
        return $this->irpf;
    }

    /**
     * @param float $irpf
     *
     * @return PurchaseInvoiceLine
     */
    public function setIrpf(float $irpf): PurchaseInvoiceLine
    {
        $this->irpf = $irpf;

        return $this;
    }

    public function __toString()
    {
        return $this->getPurchaseInvoice().' - '.$this->getId();
    }
}
