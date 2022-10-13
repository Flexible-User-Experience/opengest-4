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
        $x = 0;
        $spreadsheet->setActiveSheetIndex($x);
        $activeSheet = $spreadsheet->getActiveSheet()
            ->setTitle('Facturas de compra')
            ->setCellValue('A1', 'Id Fra')
            ->setCellValue('B1', 'Fecha')
            ->setCellValue('C1', 'Unidades')
            ->setCellValue('D1', 'Precio')
            ->setCellValue('E1', 'Importe(€)')
            ->setCellValue('F1', 'Artículo')
            ->setCellValue('G1', 'Descripción')
            ->setCellValue('H1', 'Imputado a')
        ;
        $i = 2;
        /** @var PurchaseInvoiceLine $purchaseInvoiceLine */
        foreach($parameters['purchaseInvoiceLines'] as $purchaseInvoiceLine)
        {
            $activeSheet = $spreadsheet->getActiveSheet()
                ->setCellValue('A'.$i, 'Id Fra')
                ->setCellValue('B'.$i, 'Fecha')
                ->setCellValue('C'.$i, 'Unidades')
                ->setCellValue('D'.$i, 'Precio')
                ->setCellValue('E'.$i, 'Importe(€)')
                ->setCellValue('F'.$i, 'Artículo')
                ->setCellValue('G'.$i, 'Descripción')
                ->setCellValue('H'.$i, 'Imputado a')
            ;
            $i++;
        }

        $spreadsheet->createSheet();
        $x = 1;
        $spreadsheet->setActiveSheetIndex($x);
        $activeSheet = $spreadsheet->getActiveSheet()
            ->setTitle('Partes de trabajo')
            ->setCellValue('A1', 'Id Parte')
            ->setCellValue('B1', 'Fecha')
            ->setCellValue('C1', 'Unidades')
            ->setCellValue('D1', 'Precio')
            ->setCellValue('E1', 'Importe(€)')
            ->setCellValue('F1', 'Albarán de venta')
            ->setCellValue('G1', 'Descripción')
        ;
        $i = 2;
        /** @var OperatorWorkRegister $operatorWorkRegister */
        foreach($parameters['operatorWorkRegisters'] as $operatorWorkRegister)
        {
            $activeSheet = $spreadsheet->getActiveSheet()
                ->setCellValue('A'.$i, $operatorWorkRegister->getId())
                ->setCellValue('B'.$i, $operatorWorkRegister->getStart())
                ->setCellValue('C'.$i, $operatorWorkRegister->getUnits())
                ->setCellValue('D'.$i, $operatorWorkRegister->getPriceUnit())
                ->setCellValue('E'.$i, $operatorWorkRegister->getAmount())
                ->setCellValue('F'.$i, $operatorWorkRegister->getSaleDeliveryNote())
                ->setCellValue('G'.$i, $operatorWorkRegister->getDescription())
            ;
            $i++;
        }

        return $spreadsheet;
    }
}
