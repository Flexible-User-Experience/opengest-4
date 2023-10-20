<?php

namespace App\Twig\Components;

use Doctrine\Common\Collections\Collection;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: 'components/purchase_invoice_lines_by_cost_center.html.twig')]
class PurchaseInvoiceLinesByCostCenter
{
    public Collection $purchaseInvoiceLines;
    public array $costCenters;
}
