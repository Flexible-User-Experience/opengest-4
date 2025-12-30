<?php

namespace App\Entity\Purchase;

use App\Entity\AbstractBase;
use App\Entity\Enterprise\EnterpriseTransferAccount;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class PurchaseInvoiceDueDate.
 *
 * @category
 */
#[ORM\Table(name: 'purchase_invoice_due_date')]
#[ORM\Entity(repositoryClass: \App\Repository\Purchase\PurchaseInvoiceDueDateRepository::class)]
class PurchaseInvoiceDueDate extends AbstractBase
{
    #[ORM\ManyToOne(targetEntity: \App\Entity\Purchase\PurchaseInvoice::class, inversedBy: 'purchaseInvoiceDueDates')]
    private PurchaseInvoice $purchaseInvoice;

    #[ORM\ManyToOne(targetEntity: \App\Entity\Enterprise\EnterpriseTransferAccount::class, inversedBy: 'purchaseInvoiceDueDates')]
    private ?EnterpriseTransferAccount $enterpriseTransferAccount;

    #[ORM\Column(type: 'float', nullable: true)]
    private float $amount;

    #[ORM\Column(type: 'datetime')]
    private DateTime $date;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?DateTime $paymentDate;

    #[ORM\Column(type: 'boolean')]
    private bool $paid = false;

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
     * @return PurchaseInvoiceDueDate
     */
    public function setPurchaseInvoice(PurchaseInvoice $purchaseInvoice): PurchaseInvoiceDueDate
    {
        $this->purchaseInvoice = $purchaseInvoice;
        return $this;
    }

    /**
     * @return EnterpriseTransferAccount|null
     */
    public function getEnterpriseTransferAccount(): ?EnterpriseTransferAccount
    {
        return $this->enterpriseTransferAccount;
    }

    /**
     * @param EnterpriseTransferAccount|null $enterpriseTransferAccount
     *
     * @return PurchaseInvoiceDueDate
     */
    public function setEnterpriseTransferAccount(?EnterpriseTransferAccount $enterpriseTransferAccount): PurchaseInvoiceDueDate
    {
        $this->enterpriseTransferAccount = $enterpriseTransferAccount;

        return $this;
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     *
     * @return PurchaseInvoiceDueDate
     */
    public function setAmount(float $amount): PurchaseInvoiceDueDate
    {
        $this->amount = $amount;

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
     * @return PurchaseInvoiceDueDate
     */
    public function setDate(DateTime $date): PurchaseInvoiceDueDate
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getPaymentDate(): ?DateTime
    {
        return $this->paymentDate;
    }

    /**
     * @param DateTime|null $paymentDate
     *
     * @return PurchaseInvoiceDueDate
     */
    public function setPaymentDate(?DateTime $paymentDate): PurchaseInvoiceDueDate
    {
        $this->paymentDate = $paymentDate;

        return $this;
    }

    /**
     * @return bool
     */
    public function isPaid(): bool
    {
        return $this->paid;
    }

    /**
     * @param bool $paid
     *
     * @return PurchaseInvoiceDueDate
     */
    public function setPaid(bool $paid): PurchaseInvoiceDueDate
    {
        $this->paid = $paid;

        return $this;
    }

    public function __toString()
    {
        return $this->getDate()->format('d/m/Y').' - '.$this->getAmount();
    }
}
