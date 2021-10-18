<?php

namespace App\Entity\Payslip;

use App\Entity\AbstractBase;
use App\Entity\Operator\Operator;
use DateTime;
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Operator\Operator", inversedBy="payslipOperatorDefaultLines")
     */
    private Operator $operator;

    /**
     * @ORM\Column(type="datetime")
     */
    private DateTime $from;

    /**
     * @ORM\Column(type="datetime")
     */
    private DateTime $to;

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

    public function getFrom(): DateTime
    {
        return $this->from;
    }

    public function setFrom(DateTime $from): Payslip
    {
        $this->from = $from;

        return $this;
    }

    public function getTo(): DateTime
    {
        return $this->to;
    }

    public function setTo(DateTime $to): Payslip
    {
        $this->to = $to;

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

    public function __toString(): string
    {
        return $this->getOperator().' - '.$this->getFrom()->format('d/m/y');
    }
}
