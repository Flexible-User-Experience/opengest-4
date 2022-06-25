<?php

namespace App\Manager\Xls;

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
    public function outputXls($operators, $from, $to)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet
            ->setCellValue('A1', 'Informe horas')
            ->setTitle('Informe horas Hoja 1')
            ;

        $date = date('d-m-y');
        $filename = 'export_'.$date.'.xlsx';
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
}
