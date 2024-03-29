<?php

namespace App\Entity\Sale;

use App\Entity\AbstractBase;
use Doctrine\ORM\Mapping as ORM;
use Mirmit\EFacturaBundle\Interfaces\LineFacturaEInterface;

/**
 * Class SaleDeliveryNoteLine.
 *
 * @category
 *
 * @ORM\Entity(repositoryClass="App\Repository\Sale\SaleDeliveryNoteLineRepository")
 * @ORM\Table(name="sale_delivery_note_line")
 */
class SaleDeliveryNoteLine extends AbstractBase implements LineFacturaEInterface
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Sale\SaleDeliveryNote", inversedBy="saleDeliveryNoteLines")
     */
    private SaleDeliveryNote $deliveryNote;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Sale\SaleItem", inversedBy="saleDeliveryNoteLines")
     */
    private SaleItem $saleItem;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true, scale=4)
     */
    private $units;

    /**
     * @var float
     *
     * @ORM\Column(type="float", scale=4)
     */
    private $priceUnit;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true, scale=4)
     */
    private $total;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $discount;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $description;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */
    private $iva;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */
    private $irpf;

    /**
     * Methods.
     */

    /**
     * @return SaleDeliveryNote
     */
    public function getDeliveryNote()
    {
        return $this->deliveryNote;
    }

    /**
     * @param SaleDeliveryNote $deliveryNote
     *
     * @return $this
     */
    public function setDeliveryNote($deliveryNote)
    {
        $this->deliveryNote = $deliveryNote;

        return $this;
    }

    public function getSaleItem(): SaleItem
    {
        return $this->saleItem;
    }

    public function setSaleItem(SaleItem $saleItem): SaleDeliveryNoteLine
    {
        $this->saleItem = $saleItem;

        return $this;
    }

    /**
     * @return float
     */
    public function getUnits()
    {
        return $this->units;
    }

    /**
     * @param float $units
     *
     * @return $this
     */
    public function setUnits($units)
    {
        $this->units = $units;

        return $this;
    }

    /**
     * @return float
     */
    public function getPriceUnit()
    {
        return $this->priceUnit;
    }

    /**
     * @param float $priceUnit
     *
     * @return $this
     */
    public function setPriceUnit($priceUnit)
    {
        $this->priceUnit = $priceUnit;

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

    /**
     * @return float
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * @param float $discount
     *
     * @return $this
     */
    public function setDiscount($discount)
    {
        $this->discount = $discount;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return float
     */
    public function getIva()
    {
        return $this->iva;
    }

    /**
     * @param float $iva
     *
     * @return $this
     */
    public function setIva($iva)
    {
        $this->iva = $iva;

        return $this;
    }

    /**
     * @return float
     */
    public function getIrpf()
    {
        return $this->irpf;
    }

    /**
     * @param float $irpf
     *
     * @return $this
     */
    public function setIrpf($irpf)
    {
        $this->irpf = $irpf;

        return $this;
    }

    public function getTotalWithAllDiscounts(): float
    {
        $saleInvoice = $this->getDeliveryNote()->getSaleInvoice();
        $totalWithDeliveryNoteDiscount = $this->getTotal() * (1 - $this->getDeliveryNote()->getDiscount() / 100);
        if ($saleInvoice) {
            $totalWithAllDiscounts = $totalWithDeliveryNoteDiscount * (1 - $saleInvoice->getDiscount() / 100);
        } else {
            $totalWithAllDiscounts = $totalWithDeliveryNoteDiscount;
        }

        return $totalWithAllDiscounts;
    }

    public function getIvaAmount(): float
    {
        return $this->getTotalWithAllDiscounts() * ($this->getIva() / 100);
    }

    public function getIrpfAmount(): float
    {
        return $this->getTotalWithAllDiscounts() * ($this->getIrpf() / 100);
    }

    /**
     * Factura e methods.
     */
    public function getNameFacturaE(): string
    {
        return $this->getSaleItem()->getDescription();
    }

    public function getDescriptionFacturaE(): string
    {
        return $this->getSaleItem()->getDescription();
    }

    public function getQuantityFacturaE(): float
    {
        return 1;
    }

    public function getUnitPriceFacturaE(): float
    {
        return $this->getTotalWithAllDiscounts();
    }

    public function getIvaFacturaE(): int
    {
        return $this->getIva();
    }

    public function getIrpfFacturaE(): int
    {
        return $this->getIrpf();
    }
}
