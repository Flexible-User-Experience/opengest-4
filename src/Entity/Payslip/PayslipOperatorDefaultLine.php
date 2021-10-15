<?php

namespace App\Entity\Payslip;

use App\Entity\AbstractBase;
use App\Entity\Operator\Operator;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class PayslipOperatorDefaultLine.
 *
 * @category Entity
 *
 * @author   Jordi Sort
 *
 * @ORM\Entity(repositoryClass="App\Repository\Payslip\PayslipOperatorDefaultLineRepository")
 * @ORM\Table(name="payslip_operator_default_line")
 */
class PayslipOperatorDefaultLine extends AbstractBase
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Operator\Operator", inversedBy="payslipOperatorDefaultLines")
     */
    private Operator $operator;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Payslip\PayslipLineConcept", inversedBy="payslipOperatorDefaultLines")
     */
    private PayslipLineConcept $payslipLineConcept;

    /**
     * @ORM\Column(type="float")
     */
    private float $units = 0;

    /**
     * @ORM\Column(type="float")
     */
    private float $priceUnit = 0;

    /**
     * @ORM\Column(type="float")
     */
    private float $amount = 0;

    /**
     * Methods.
     */
    public function getOperator(): Operator
    {
        return $this->operator;
    }

    public function setOperator(Operator $operator): PayslipOperatorDefaultLine
    {
        $this->operator = $operator;

        return $this;
    }

    public function getPayslipLineConcept(): PayslipLineConcept
    {
        return $this->payslipLineConcept;
    }

    public function setPayslipLineConcept(PayslipLineConcept $payslipLineConcept): PayslipOperatorDefaultLine
    {
        $this->payslipLineConcept = $payslipLineConcept;

        return $this;
    }

    public function getUnits(): float
    {
        return $this->units;
    }

    public function setUnits(float $units): PayslipOperatorDefaultLine
    {
        $this->units = $units;

        return $this;
    }

    public function getPriceUnit(): float
    {
        return $this->priceUnit;
    }

    public function setPriceUnit(float $priceUnit): PayslipOperatorDefaultLine
    {
        $this->priceUnit = $priceUnit;

        return $this;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): PayslipOperatorDefaultLine
    {
        $this->amount = $amount;

        return $this;
    }

    public function __toString(): string
    {
        return $this->getOperator().' - '.$this->getPayslipLineConcept();
    }
}
