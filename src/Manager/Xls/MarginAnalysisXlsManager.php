<?php

namespace App\Manager\Xls;

use App\Manager\RepositoriesManager;
use Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Class MarginAnalysisXlsManager.
 *
 * @category Manager
 */
class MarginAnalysisXlsManager
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
        $filename = 'marginAnalysis.xlsx';
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
                $x = 0;
        $spreadsheet->setActiveSheetIndex($x);
        $activeSheet = $spreadsheet->getActiveSheet()
            ->setTitle('Análisis')
            ->setCellValue('A1', 'Id Fra')
            ->setCellValue('B1', 'Fecha')
            ->setCellValue('C1', 'Cliente')
            ->setCellValue('D1', 'Ingresos')
            ->setCellValue('E1', 'Costes Directos Operario')
            ->setCellValue('F1', 'Costes Directos Facturas Albarán')
            ->setCellValue('G1', 'Costes Indirectos Vehículo')
            ->setCellValue('H1', 'Costes Indirectos Facturas Operario')
            ->setCellValue('I1', 'Costes Indirectos Nóminas Operario')
            ->setCellValue('J1', 'Total Costes')
            ->setCellValue('K1', 'Margen')
            ->setCellValue('L1', 'Margen %')
            ->setCellValue('M1', 'Línea actividad')
        ;
        $i = 2;
        foreach($parameters['current'] as $currentYearInfo)
        {
            $activeSheet = $spreadsheet->getActiveSheet()
                ->setCellValue('A'.$i, $currentYearInfo['id'])
                ->setCellValue('B'.$i, $currentYearInfo['date'])
                ->setCellValue('C'.$i,  $currentYearInfo['partner_id'].'-'.$currentYearInfo['partner_name'])
                ->setCellValue('D'.$i, $currentYearInfo['income'])
                ->setCellValue('E'.$i, $currentYearInfo['workingHoursDirectCost'])
                ->setCellValue('F'.$i, $currentYearInfo['purchaseInvoiceDirectCost'])
                ->setCellValue('G'.$i, $currentYearInfo['vehicleIndirectCost'])
                ->setCellValue('H'.$i, $currentYearInfo['operatorPurchaseInvoiceIndirectCost'])
                ->setCellValue('I'.$i, $currentYearInfo['operatorPayslipIndirectCost'])
                ->setCellValue('J'.$i, $currentYearInfo['totalCost'])
                ->setCellValue('K'.$i, $currentYearInfo['margin'])
                ->setCellValue('L'.$i, $currentYearInfo['marginPercentage'])
                ->setCellValue('M'.$i, $currentYearInfo['activityLine'])
            ;
            $i++;
        }
        return $spreadsheet;
    }
}
