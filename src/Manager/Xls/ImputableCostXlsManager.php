<?php

namespace App\Manager\Xls;

use App\Manager\RepositoriesManager;
use Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Class ImputableCostXlsManager.
 *
 * @category Manager
 */
class ImputableCostXlsManager
{
    private RepositoriesManager $rm;

    /**
     * Methods.
     */
    public function __construct(RepositoriesManager $rm)
    {
        $this->rm = $rm;
    }

    public function outputXls($parameters)
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet = $this->buildXls($spreadsheet, $parameters);
        $filename = 'imputableCost.xlsx';
        try {
            $writer = new Xlsx($spreadsheet);
            $writer->save($filename);
            $content = file_get_contents($filename);
        } catch (Exception $e) {
            exit($e->getMessage());
        }
        unlink($filename);

        return $content;
    }

    private function buildXls(Spreadsheet $spreadsheet, $parameters)
    {
        // List of parameters:
//        [
//            'saleDeliveryNotes' => $saleDeliveryNotes,
//            'years' => range($currentYear, $oldestYear),
//            'vehicles' => $vehicles,
//            'operators' => $operators,
//            'costCenters' => $costCenters,
//            'selectedYear' => $year,
//            'selectedSaleDeliveryNoteId' => $saleDeliveryNoteId,
//            'selectedVehicleId' => $vehicleId,
//            'selectedOperatorId' => $operatorId,
//            'selectedCostCenterId' => $costCenterId,
//            'totalWorkingHours' => $totalWorkingHours,
//            'totalWorkingHoursCost' => $totalWorkingHoursCost,
//            'totalInvoiceCost' => $totalInvoiceCost,
//            'totalVehicleConsumptionCost' => $totalVehicleConsumptionCost,
//            'purchaseInvoiceLines' => $purchaseInvoiceLines,
//            'operatorWorkRegisters' => $operatorWorkRegisters,
//            'vehicleConsumptions' => $vehicleConsumptions,
//        ]

        return $spreadsheet;
    }
}
