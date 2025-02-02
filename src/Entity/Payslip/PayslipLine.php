<?php

namespace App\Entity\Payslip;

use App\Entity\AbstractBase;
use App\Repository\Payslip\PayslipLineRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class PayslipLine.
 *
 * @category Entity
 *
 * @author   Jordi Sort
 */
#[ORM\Table(name: 'payslip_line')]
#[ORM\Entity(repositoryClass: PayslipLineRepository::class)]
class PayslipLine extends AbstractBase
{
    #[ORM\ManyToOne(targetEntity: Payslip::class, inversedBy: 'payslipLines')]
    private Payslip $payslip;

    #[ORM\ManyToOne(targetEntity: PayslipLineConcept::class, inversedBy: 'payslipLines')]
    private ?PayslipLineConcept $payslipLineConcept = null;

    #[ORM\Column(type: 'integer')]
    private int $units = 0;

    #[ORM\Column(type: 'float')]
    private float $priceUnit = 0;

    #[ORM\Column(type: 'float')]
    private float $amount = 0;

    /**
     * Methods.
     */
    public function getPayslip(): Payslip
    {
        return $this->payslip;
    }

    public function setPayslip(Payslip $payslip): PayslipLine
    {
        $this->payslip = $payslip;

        return $this;
    }

    public function getPayslipLineConcept(): ?PayslipLineConcept
    {
        return $this->payslipLineConcept;
    }

    public function setPayslipLineConcept(PayslipLineConcept $payslipLineConcept): PayslipLine
    {
        $this->payslipLineConcept = $payslipLineConcept;

        return $this;
    }

    public function getUnits(): int
    {
        return $this->units;
    }

    public function setUnits(int $units): PayslipLine
    {
        $this->units = $units;

        return $this;
    }

    /**
     * @return float|int
     */
    public function getPriceUnit(): float|int
    {
        return $this->priceUnit;
    }

    /**
     * @param float|int $priceUnit
     */
    public function setPriceUnit($priceUnit): PayslipLine
    {
        $this->priceUnit = $priceUnit;

        return $this;
    }

    /**
     * @return float|int
     */
    public function getAmount(): float|int
    {
        return $this->amount;
    }

    /**
     * @param float|int $amount
     */
    public function setAmount($amount): PayslipLine
    {
        $this->amount = $amount;

        return $this;
    }

    public function __toString(): string
    {
        return $this->getPayslip().' - '.$this->getId();
    }
}
