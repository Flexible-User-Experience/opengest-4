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
    private ?PurchaseInvoice $purchaseInvoice = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Purchase\PurchaseItem", inversedBy="purchaseInvoiceLines")
     */
    private ?PurchaseItem $purchaseItem;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Operator\Operator", inversedBy="purchaseInvoiceLines")
     */
    private ?Operator $operator;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Vehicle\Vehicle", inversedBy="purchaseInvoiceLines")
     */
    private ?Vehicle $vehicle;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Sale\SaleDeliveryNote", inversedBy="purchaseInvoiceLines")
     */
    private ?SaleDeliveryNote $saleDeliveryNote;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Setting\CostCenter", inversedBy="purchaseInvoiceLines")
     */
    private ?CostCenter $costCenter;

    /**
     * @ORM\Column(type="float", nullable=true, scale=4)
     */
    private ?float $units = 0;

    /**
     * @ORM\Column(type="float", scale=4)
     */
    private float $priceUnit = 0;

    /**
     * @ORM\Column(type="float", nullable=true, scale=4)
     */
    private ?float $total;

    /**
     * @ORM\Column(type="float", nullable=true, scale=4)
     */
    private ?float $baseTotal;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $description;

    /**
     * @ORM\Column(type="float")
     */
    private float $iva;

    /**
     * @ORM\Column(type="float")
     */
    private float $irpf;

    /**
     * Methods.
     */
    public function getPurchaseInvoice(): ?PurchaseInvoice
    {
        return $this->purchaseInvoice;
    }

    public function setPurchaseInvoice(PurchaseInvoice $purchaseInvoice): PurchaseInvoiceLine
    {
        $this->purchaseInvoice = $purchaseInvoice;

        return $this;
    }

    /**
     * @return ?PurchaseItem
     */
    public function getPurchaseItem(): ?PurchaseItem
    {
        return $this->purchaseItem;
    }

    public function setPurchaseItem(PurchaseItem $purchaseItem): PurchaseInvoiceLine
    {
        $this->purchaseItem = $purchaseItem;

        return $this;
    }

    /**
     * @return ?Operator
     */
    public function getOperator(): ?Operator
    {
        return $this->operator;
    }

    public function setOperator(?Operator $operator): PurchaseInvoiceLine
    {
        $this->operator = $operator;

        return $this;
    }

    /**
     * @return ?Vehicle
     */
    public function getVehicle(): ?Vehicle
    {
        return $this->vehicle;
    }

    public function setVehicle(?Vehicle $vehicle): PurchaseInvoiceLine
    {
        $this->vehicle = $vehicle;

        return $this;
    }

    /**
     * @return ?SaleDeliveryNote
     */
    public function getSaleDeliveryNote(): ?SaleDeliveryNote
    {
        return $this->saleDeliveryNote;
    }

    public function setSaleDeliveryNote(?SaleDeliveryNote $saleDeliveryNote): PurchaseInvoiceLine
    {
        $this->saleDeliveryNote = $saleDeliveryNote;

        return $this;
    }

    /**
     * @return ?CostCenter
     */
    public function getCostCenter(): ?CostCenter
    {
        return $this->costCenter;
    }

    public function setCostCenter(?CostCenter $costCenter): PurchaseInvoiceLine
    {
        $this->costCenter = $costCenter;

        return $this;
    }

    public function getUnits(): ?float
    {
        return $this->units;
    }

    public function setUnits(?float $units): PurchaseInvoiceLine
    {
        $this->units = $units;

        return $this;
    }

    public function getPriceUnit(): float
    {
        return $this->priceUnit;
    }

    public function setPriceUnit(float $priceUnit): PurchaseInvoiceLine
    {
        $this->priceUnit = $priceUnit;

        return $this;
    }

    public function getTotal(): ?float
    {
        return $this->total;
    }

    public function setTotal(?float $total): PurchaseInvoiceLine
    {
        $this->total = $total;

        return $this;
    }

    public function getBaseTotal(): ?float
    {
        return $this->baseTotal;
    }

    public function setBaseTotal(?float $baseTotal): PurchaseInvoiceLine
    {
        $this->baseTotal = $baseTotal;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): PurchaseInvoiceLine
    {
        $this->description = $description;

        return $this;
    }

    public function getIva(): float
    {
        return $this->iva;
    }

    public function setIva(float $iva): PurchaseInvoiceLine
    {
        $this->iva = $iva;

        return $this;
    }

    public function getIrpf(): float
    {
        return $this->irpf;
    }

    public function setIrpf(float $irpf): PurchaseInvoiceLine
    {
        $this->irpf = $irpf;

        return $this;
    }

    /**
     * Custom methods.
     */
    public function getImputedTo()
    {
        if ($this->getSaleDeliveryNote()) {
            return $this->getSaleDeliveryNote();
        }
        if ($this->getVehicle()) {
            return $this->getVehicle();
        }
        if ($this->getOperator()) {
            return $this->getOperator();
        }
        if ($this->getCostCenter()) {
            return $this->getCostCenter();
        }
    }

    public function getImputedToType(): string
    {
        if ($this->getSaleDeliveryNote()) {
            return 'Albarán';
        }
        if ($this->getVehicle()) {
            return 'Vehículo';
        }
        if ($this->getOperator()) {
            return 'Operario';
        }
        if ($this->getCostCenter()) {
            return 'Centro de coste';
        }

        return '';
    }

    public function __toString()
    {
        return $this->getPurchaseInvoice().' - '.$this->getId();
    }
}
