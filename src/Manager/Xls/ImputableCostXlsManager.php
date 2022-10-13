<?php

namespace App\Manager\Xls;

use App\Entity\Operator\OperatorWorkRegister;
use App\Entity\Purchase\PurchaseInvoiceLine;
use App\Entity\Vehicle\VehicleConsumption;
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
                ->setCellValue('A'.$i, $purchaseInvoiceLine->getId())
                ->setCellValue('B'.$i, $purchaseInvoiceLine->getPurchaseInvoice()->getDateFormatted())
                ->setCellValue('C'.$i,  $purchaseInvoiceLine->getUnits())
                ->setCellValue('D'.$i, $purchaseInvoiceLine->getPriceUnit())
                ->setCellValue('E'.$i, $purchaseInvoiceLine->getBaseTotal())
                ->setCellValue('F'.$i, $purchaseInvoiceLine->getPurchaseItem())
                ->setCellValue('G'.$i, $purchaseInvoiceLine->getDescription())
                ->setCellValue('H'.$i, $purchaseInvoiceLine->getImputedTo())
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
            ->setCellValue('C1', 'Operario')
            ->setCellValue('D1', 'Unidades')
            ->setCellValue('E1', 'Precio')
            ->setCellValue('F1', 'Importe(€)')
            ->setCellValue('G1', 'Albarán de venta')
            ->setCellValue('H1', 'Descripción')
        ;
        $i = 2;
        /** @var OperatorWorkRegister $operatorWorkRegister */
        foreach($parameters['operatorWorkRegisters'] as $operatorWorkRegister)
        {
            $activeSheet = $spreadsheet->getActiveSheet()
                ->setCellValue('A'.$i, $operatorWorkRegister->getId())
                ->setCellValue('B'.$i, $operatorWorkRegister->getOperatorWorkRegisterHeader()->getDateFormatted())
                ->setCellValue('C'.$i, $operatorWorkRegister->getOperatorWorkRegisterHeader()->getOperator())
                ->setCellValue('D'.$i, $operatorWorkRegister->getUnits())
                ->setCellValue('E'.$i, $operatorWorkRegister->getPriceUnit())
                ->setCellValue('F'.$i, $operatorWorkRegister->getAmount())
                ->setCellValue('G'.$i, $operatorWorkRegister->getSaleDeliveryNote())
                ->setCellValue('H'.$i, $operatorWorkRegister->getDescription())
            ;
            $i++;
        }

        $spreadsheet->createSheet();
        $x = 2;
        $spreadsheet->setActiveSheetIndex($x);
        $activeSheet = $spreadsheet->getActiveSheet()
            ->setTitle('Suministros')
            ->setCellValue('A1', 'Id Sum.')
            ->setCellValue('B1', 'Fecha')
            ->setCellValue('C1', 'Unidades')
            ->setCellValue('D1', 'Precio')
            ->setCellValue('E1', 'Importe(€)')
            ->setCellValue('F1', 'Vehículo')
            ->setCellValue('G1', 'Combustible')
        ;
        $i = 2;
        /** @var VehicleConsumption $vehicleConsumption */
        foreach($parameters['vehicleConsumptions'] as $vehicleConsumption)
        {
            $activeSheet = $spreadsheet->getActiveSheet()
                ->setCellValue('A'.$i, $vehicleConsumption->getId())
                ->setCellValue('B'.$i, $vehicleConsumption->getSupplyDateFormatted())
                ->setCellValue('C'.$i, $vehicleConsumption->getQuantity())
                ->setCellValue('D'.$i, $vehicleConsumption->getPriceUnit())
                ->setCellValue('E'.$i, $vehicleConsumption->getAmount())
                ->setCellValue('F'.$i, $vehicleConsumption->getVehicle())
                ->setCellValue('G'.$i, $vehicleConsumption->getVehicleFuel())
            ;
            $i++;
        }

        return $spreadsheet;
    }
}
