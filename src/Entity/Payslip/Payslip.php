<?php

namespace App\Entity\Payslip;

use App\Entity\AbstractBase;
use App\Entity\Operator\Operator;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Payslip.
 *
 * @category Entity
 *
 * @author   Jordi Sort
 *
 * @ORM\Entity(repositoryClass="App\Repository\Payslip\PayslipRepository")
 * @ORM\Table(name="payslip")
 */
class Payslip extends AbstractBase
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Operator\Operator", inversedBy="payslips")
     */
    private Operator $operator;

    /**
     * @ORM\Column(type="datetime")
     */
    private DateTime $fromDate;

    /**
     * @ORM\Column(type="datetime")
     */
    private DateTime $toDate;

    /**
     * @ORM\Column(type="integer")
     */
    private int $year;

    /**
     * @ORM\Column(type="float")
     */
    private float $expenses = 0;

    /**
     * @ORM\Column(type="float")
     */
    private float $socialSecurityCost = 0;

    /**
     * @ORM\Column(type="float")
     */
    private float $extraPay = 0;

    /**
     * @ORM\Column(type="float")
     */
    private float $otherCosts = 0;

    /**
     * @ORM\Column(type="float")
     */
    private float $totalAmount = 0;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Payslip\PayslipLine", mappedBy="payslip", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private ArrayCollection $payslipLines;

    /**
     * Methods.
     */
    public function getOperator(): Operator
    {
        return $this->operator;
    }

    public function setOperator(Operator $operator): Payslip
    {
        $this->operator = $operator;

        return $this;
    }

    public function getFromDate(): DateTime
    {
        return $this->fromDate;
    }

    public function setFromDate(DateTime $fromDate): Payslip
    {
        $this->fromDate = $fromDate;

        return $this;
    }

    public function getToDate(): DateTime
    {
        return $this->toDate;
    }

    public function setToDate(DateTime $toDate): Payslip
    {
        $this->toDate = $toDate;

        return $this;
    }

    public function getYear(): int
    {
        return $this->year;
    }

    public function setYear(int $year): Payslip
    {
        $this->year = $year;

        return $this;
    }

    /**
     * @return float|int
     */
    public function getExpenses(): float
    {
        return $this->expenses;
    }

    /**
     * @param float|int $expenses
     */
    public function setExpenses($expenses): Payslip
    {
        $this->expenses = $expenses;

        return $this;
    }

    /**
     * @return float|int
     */
    public function getSocialSecurityCost(): float
    {
        return $this->socialSecurityCost;
    }

    /**
     * @param float|int $socialSecurityCost
     */
    public function setSocialSecurityCost($socialSecurityCost): Payslip
    {
        $this->socialSecurityCost = $socialSecurityCost;

        return $this;
    }

    /**
     * @return float|int
     */
    public function getExtraPay(): float
    {
        return $this->extraPay;
    }

    /**
     * @param float|int $extraPay
     */
    public function setExtraPay($extraPay): Payslip
    {
        $this->extraPay = $extraPay;

        return $this;
    }

    /**
     * @return float|int
     */
    public function getOtherCosts(): float
    {
        return $this->otherCosts;
    }

    /**
     * @param float|int $otherCosts
     */
    public function setOtherCosts($otherCosts): Payslip
    {
        $this->otherCosts = $otherCosts;

        return $this;
    }

    /**
     * @return float|int
     */
    public function getTotalAmount(): float
    {
        return $this->totalAmount;
    }

    /**
     * @param float|int $totalAmount
     */
    public function setTotalAmount($totalAmount): Payslip
    {
        $this->totalAmount = $totalAmount;

        return $this;
    }

    public function getPayslipLines(): ArrayCollection
    {
        return $this->payslipLines;
    }

    /**
     * @return $this
     */
    public function setPayslipLines(ArrayCollection $payslipLines): Payslip
    {
        $this->payslipLines = $payslipLines;

        return $this;
    }

    /**
     * @return $this
     */
    public function addPayslipLine(PayslipLine $payslipLine): Payslip
    {
        if (!$this->payslipLines->contains($payslipLine)) {
            $this->payslipLines->add($payslipLine);
            $payslipLine->setPayslip($this);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function removePayslipLine(PayslipLine $payslipLine): Payslip
    {
        if ($this->payslipLines->contains($payslipLine)) {
            $this->payslipLines->removeElement($payslipLine);
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->getOperator().' - '.$this->getFromDate()->format('d/m/y');
    }
}
