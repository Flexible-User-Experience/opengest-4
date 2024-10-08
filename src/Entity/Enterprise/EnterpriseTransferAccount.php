<?php

namespace App\Entity\Enterprise;

use App\Entity\AbstractBase;
use App\Entity\Partner\Partner;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class EnterpriseTransferAccount.
 *
 * @category Entity
 *
 * @author   Rubèn Hierro <info@rubenhierro.com>
 */
#[ORM\Table(name: 'enterprise_transfer_account')]
#[ORM\Entity(repositoryClass: \App\Repository\Enterprise\EnterpriseTransferAccountRepository::class)]
class EnterpriseTransferAccount extends AbstractBase
{
    /**
     * @var Enterprise
     */
    #[ORM\ManyToOne(targetEntity: \App\Entity\Enterprise\Enterprise::class, inversedBy: 'enterpriseTransferAccounts')]
    private $enterprise;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string')]
    private $name;

    /**
     * @var string
     *
     * @Assert\Iban()
     */
    #[ORM\Column(type: 'string', nullable: true)]
    private $iban;

    /**
     * @var string
     *
     * @Assert\Bic()
     */
    #[ORM\Column(type: 'string', nullable: true)]
    private $swift;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', nullable: true)]
    private $bankCode;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', nullable: true)]
    private $officeNumber;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', nullable: true)]
    private $controlDigit;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', nullable: true)]
    private $accountNumber;

    /**
     * @var ArrayCollection
     */
    #[ORM\OneToMany(targetEntity: \App\Entity\Partner\Partner::class, mappedBy: 'transferAccount')]
    private $partners;

    /**
     * @var ArrayCollection
     */
    #[ORM\OneToMany(targetEntity: \App\Entity\Purchase\PurchaseInvoiceDueDate::class, mappedBy: 'enterpriseTransferAccount')]
    private Collection $purchaseInvoiceDueDates;

    /**
     * @var ArrayCollection
     */
    #[ORM\OneToMany(targetEntity: \App\Entity\Sale\SaleInvoiceDueDate::class, mappedBy: 'enterpriseTransferAccount')]
    private Collection $saleInvoiceDueDates;

    /**
     * Methods.
     */

    /**
     * EnterpriseTransferAccount constructor.
     */
    public function __construct()
    {
        $this->partners = new ArrayCollection();
        $this->purchaseInvoiceDueDates = new ArrayCollection();
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
     * @return $this
     */
    public function setEnterprise($enterprise): static
    {
        $this->enterprise = $enterprise;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getIban(): string
    {
        return $this->iban;
    }

    /**
     * @param string $iban
     *
     * @return $this
     */
    public function setIban($iban): static
    {
        $this->iban = $iban;

        return $this;
    }

    /**
     * @return string
     */
    public function getSwift(): string
    {
        return $this->swift;
    }

    /**
     * @param string $swift
     *
     * @return $this
     */
    public function setSwift($swift): static
    {
        $this->swift = $swift;

        return $this;
    }

    /**
     * @return string
     */
    public function getBankCode(): string
    {
        return $this->bankCode;
    }

    /**
     * @param string $bankCode
     *
     * @return $this
     */
    public function setBankCode($bankCode): static
    {
        $this->bankCode = $bankCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getOfficeNumber(): string
    {
        return $this->officeNumber;
    }

    /**
     * @param string $officeNumber
     *
     * @return $this
     */
    public function setOfficeNumber($officeNumber): static
    {
        $this->officeNumber = $officeNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getControlDigit(): string
    {
        return $this->controlDigit;
    }

    /**
     * @param string $controlDigit
     *
     * @return $this
     */
    public function setControlDigit($controlDigit): static
    {
        $this->controlDigit = $controlDigit;

        return $this;
    }

    /**
     * @return string
     */
    public function getAccountNumber(): string
    {
        return $this->accountNumber;
    }

    /**
     * @param string $accountNumber
     *
     * @return $this
     */
    public function setAccountNumber($accountNumber): static
    {
        $this->accountNumber = $accountNumber;

        return $this;
    }

    public function getPartners(): Collection
    {
        return $this->partners;
    }

    /**
     * @param ArrayCollection $partners
     *
     * @return $this
     */
    public function setPartners($partners): static
    {
        $this->partners = $partners;

        return $this;
    }

    /**
     * @param Partner $partner
     *
     * @return $this
     */
    public function addPartner($partner): static
    {
        if (!$this->partners->contains($partner)) {
            $this->partners->add($partner);
            $partner->setTransferAccount($this);
        }

        return $this;
    }

    /**
     * @param Partner $partner
     *
     * @return $this
     */
    public function removePartner($partner): static
    {
        if ($this->partners->contains($partner)) {
            $this->partners->removeElement($partner);
        }

        return $this;
    }

    public function getPurchaseInvoiceDueDates(): Collection
    {
        return $this->purchaseInvoiceDueDates;
    }

    public function setPurchaseInvoiceDueDates(Collection $purchaseInvoiceDueDates): EnterpriseTransferAccount
    {
        $this->purchaseInvoiceDueDates = $purchaseInvoiceDueDates;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->id ? $this->getName() : '---';
    }
}
