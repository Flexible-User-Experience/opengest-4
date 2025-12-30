<?php

namespace App\Twig\Components;

use App\Entity\Vehicle\Vehicle;
use Doctrine\Common\Collections\Collection;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: 'components/purchase_invoice_lines_by_cost_center.html.twig')]
class PurchaseInvoiceLinesByCostCenter
{
    public Collection $purchaseInvoiceLines;
    public array $costCenters;
    public bool $logBook = false;
    public Vehicle $vehicle;
}
