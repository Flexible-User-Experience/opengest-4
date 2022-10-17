<?php

namespace App\Entity\Sale;

use App\Entity\AbstractBase;
use App\Entity\Enterprise\EnterpriseTransferAccount;
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Sale\SaleInvoice", inversedBy="saleInvoiceDueDates", cascade={"persist"})
     */
    private SaleInvoice $saleInvoice;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Enterprise\EnterpriseTransferAccount", inversedBy="saleInvoiceDueDates")
     */
    private ?EnterpriseTransferAccount $enterpriseTransferAccount;

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
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?DateTime $paymentDate;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $paid = false;

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

    public function getEnterpriseTransferAccount(): ?EnterpriseTransferAccount
    {
        return $this->enterpriseTransferAccount;
    }

    public function setEnterpriseTransferAccount(?EnterpriseTransferAccount $enterpriseTransferAccount): SaleInvoiceDueDate
    {
        $this->enterpriseTransferAccount = $enterpriseTransferAccount;

        return $this;
    }

    public function getPaymentDate(): ?DateTime
    {
        return $this->paymentDate;
    }

    public function setPaymentDate(?DateTime $paymentDate): SaleInvoiceDueDate
    {
        $this->paymentDate = $paymentDate;

        return $this;
    }

    public function isPaid(): bool
    {
        return $this->paid;
    }

    public function setPaid(bool $paid): SaleInvoiceDueDate
    {
        $this->paid = $paid;

        return $this;
    }

    public function __toString()
    {
        return $this->getDate()->format('d/m/Y').' - '.$this->getAmount();
    }
}
