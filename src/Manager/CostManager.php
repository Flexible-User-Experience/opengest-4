<?php

namespace App\Manager;

use App\Entity\Purchase\PurchaseInvoiceLine;

class CostManager
{
    /**
     * @param PurchaseInvoiceLine[] $purchaseInvoiceLines
     */
    public function getTotalCostFromPurchaseInvoiceLines(array $purchaseInvoiceLines): float
    {
        $totalCost = 0;
        foreach ($purchaseInvoiceLines as $purchaseInvoiceLine) {
            $totalCost += $purchaseInvoiceLine->getPriceUnit() * $purchaseInvoiceLine->getUnits();
        }

        return $totalCost;
    }
}
