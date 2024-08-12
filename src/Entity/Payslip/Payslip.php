<?php

namespace App\Entity\Payslip;

use App\Entity\AbstractBase;
use App\Entity\Operator\Operator;
use App\Service\Format\NumberFormatService;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
    private float $totalAccrued = 0;

    /**
     * @ORM\Column(type="float")
     */
    private float $totalDeductions = 0;

    /**
     * @ORM\Column(type="float")
     */
    private float $totalAmount = 0;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Payslip\PayslipLine", mappedBy="payslip", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private Collection $payslipLines;

    /**
     * Methods.
     */

    /**
     * Payslip constructor.
     */
    public function __construct()
    {
        $this->payslipLines = new ArrayCollection();
    }

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
        $this->year = $fromDate->format('Y');

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

    public function getExpenses(): float
    {
        return $this->expenses;
    }

    public function setExpenses(float $expenses): Payslip
    {
        $this->expenses = $expenses;

        return $this;
    }

    public function getSocialSecurityCost(): float
    {
        return $this->socialSecurityCost;
    }

    public function setSocialSecurityCost(float $socialSecurityCost): Payslip
    {
        $this->socialSecurityCost = $socialSecurityCost;

        return $this;
    }

    public function getExtraPay(): float
    {
        $this->setExtraPay(0);

        return $this->extraPay;
    }

    public function setExtraPay(float $extraPay): Payslip
    {
        $extraPay = 0;
        $payslipLineExtraPays = $this->getPayslipLines()->filter(function (PayslipLine $payslipLine) {
            return 5 == $payslipLine->getPayslipLineConcept()->getId();
        });
        /** @var PayslipLine $lineExtraPay */
        foreach ($payslipLineExtraPays as $lineExtraPay) {
            $extraPay += $lineExtraPay->getAmount();
        }
        $this->extraPay = $extraPay;

        return $this;
    }

    public function getOtherCosts(): float
    {
        return $this->otherCosts;
    }

    public function setOtherCosts(float $otherCosts): Payslip
    {
        $this->otherCosts = $otherCosts;

        return $this;
    }


    public function getTotalAccrued(): float
    {
        return $this->totalAccrued;
    }

    public function setTotalAccrued(float $totalAccrued): Payslip
    {
        $this->totalAccrued = $totalAccrued;

        return $this;
    }

    public function getTotalDeductions(): float
    {
        return $this->totalDeductions;
    }

    public function setTotalDeductions(float $totalDeductions): Payslip
    {
        $this->totalDeductions = $totalDeductions;

        return $this;
    }

    public function getTotalAmount(): float
    {
        return $this->totalAmount;
    }

    public function setTotalAmount(float $totalAmount): Payslip
    {
        $this->totalAmount = $totalAmount;

        return $this;
    }

    public function getPayslipLines(): Collection
    {
        return $this->payslipLines;
    }

    /**
     * @return $this
     */
    public function setPayslipLines(Collection $payslipLines): Payslip
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

    public function getFromDateFormatted(): string
    {
        return $this->getFromDate()->format('d/m/y');
    }

    public function getToDateFormatted(): string
    {
        return $this->getToDate()->format('d/m/y');
    }

    public function getExpensesFormatted(): string
    {
        return NumberFormatService::formatNumber($this->getExpenses());
    }

    public function getSocialSecurityCostFormatted(): string
    {
        return NumberFormatService::formatNumber($this->getSocialSecurityCost());
    }

    public function getExtraPayFormatted(): string
    {
        return NumberFormatService::formatNumber($this->getExtraPay());
    }

    public function getOtherCostsFormatted(): string
    {
        return NumberFormatService::formatNumber($this->getOtherCosts());
    }

    public function getTotalAmountFormatted(): string
    {
        return NumberFormatService::formatNumber($this->getTotalAmount());
    }

    public function __toString(): string
    {
        return $this->getId();
    }
}
