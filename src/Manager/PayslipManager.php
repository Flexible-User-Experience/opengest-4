<?php

namespace App\Manager;

use App\Entity\Payslip\Payslip;
use App\Entity\Payslip\PayslipLine;

class PayslipManager
{
    public function updatePayslipTotals(Payslip $payslip): Payslip
    {
        $totalAccrued = 0;
        $totalDeductions = 0;
        /** @var PayslipLine $payslipLine */
        foreach ($payslip->getPayslipLines() as $payslipLine) {
            $amount = $payslipLine->getUnits() * $payslipLine->getPriceUnit();
            $payslipLine->setAmount($amount);
            if ($payslipLine->getPayslipLineConcept()->isDeduction()) {
                $totalDeductions += $amount;
            } else {
                $totalAccrued += $amount;
            }

        }
        $totalAmount = $totalAccrued + $totalDeductions;
        $payslip->setTotalAccrued($totalAccrued);
        $payslip->setTotalDeductions($totalDeductions);
        $payslip->setTotalAmount($totalAmount);

        return $payslip;
    }
}