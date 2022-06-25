<?php

namespace App\Manager\Xls;

use App\Entity\Operator\OperatorWorkRegisterHeader;
use Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Class OperatorWorkRegisterHeaderXlsManager.
 *
 * @category Manager
 */
class OperatorWorkRegisterHeaderXlsManager
{
    public function outputXls($operatorWorkRegisterHeaders, $from, $to)
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet = $this->buildXls($spreadsheet, $operatorWorkRegisterHeaders, $from, $to);
        $filename = 'tempfile.xlsx';
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

    private function buildXls(Spreadsheet $spreadsheet, $operatorWorkRegisterHeaders, $from, $to)
    {
        $activeSheet = $spreadsheet->getActiveSheet();
        $activeSheet
            ->setTitle('Informe horas Hoja 1')
            ->setCellValue('A1', 'Informe horas')
        ;
        // Create new sheet
        $sheet2 = $spreadsheet->createSheet()
            ->setTitle('Informe Hoja 2')
            ->setCellValue('B2', $from)
        ;
        //Example of iteration
        $sheet2
            ->setCellValue('B3', 'Fecha')
            ->setCellValue('C3', 'Horas')
            ;
        $i = 4;
        /** @var OperatorWorkRegisterHeader $operatorWorkRegisterHeader */
        foreach ($operatorWorkRegisterHeaders as $operatorWorkRegisterHeader) {
            $sheet2
                ->setCellValue('B'.$i, $operatorWorkRegisterHeader->getDate())
                ->setCellValue('C'.$i, $operatorWorkRegisterHeader->getHours())
                ;
            ++$i;
        }

        return $spreadsheet;
    }
}
