<?php

namespace App\Entity\Sale;

use App\Entity\AbstractBase;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class SaleInvoiceDueDate.
 *
 * @category
 *
 * @ORM\Entity(repositoryClass="App\Repository\Sale\SaleInvoiceDueDateRepository")
 * @ORM\Table(name="sale_invoice_due_date")
 */
class SaleInvoiceDueDate extends AbstractBase
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Sale\SaleInvoice", inversedBy="saleInvoiceDueDates")
     */
    private SaleInvoice $saleInvoice;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $amount;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime")
     */
    protected $date;

    /**
     * Methods.
     */
    public function getSaleInvoice(): ?SaleInvoice
    {
        return $this->saleInvoice;
    }

    public function setSaleInvoice(SaleInvoice $saleInvoice): SaleInvoiceDueDate
    {
        $this->saleInvoice = $saleInvoice;

        return $this;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): SaleInvoiceDueDate
    {
        $this->amount = $amount;

        return $this;
    }

    public function getDate(): ?DateTime
    {
        return $this->date;
    }

    public function setDate(DateTime $date): SaleInvoiceDueDate
    {
        $this->date = $date;

        return $this;
    }

    public function __toString()
    {
        return $this->getDate()->format('d/m/Y').' - '.$this->getAmount();
    }
}
