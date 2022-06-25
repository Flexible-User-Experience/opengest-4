<?php

namespace App\Manager\Xls;

use App\Entity\Operator\Operator;
use App\Entity\Operator\OperatorWorkRegisterHeader;
use App\Manager\RepositoriesManager;
use Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Class OperatorWorkRegisterHeaderXlsManager.
 *
 * @category Manager
 */
class OperatorWorkRegisterHeaderXlsManager
{
    private RepositoriesManager $rm;

    /**
     * Methods.
     */
    public function __construct(RepositoriesManager $rm)
    {
        $this->rm = $rm;
    }


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
        $operatorsFromWorkRegisterHeaders = [];
        /** @var OperatorWorkRegisterHeader $workRegisterHeader */
        foreach ($operatorWorkRegisterHeaders as $workRegisterHeader) {
            $operatorsFromWorkRegisterHeaders[$workRegisterHeader->getOperator()->getId()][] = $workRegisterHeader;
        }
        $x = 0;
        foreach ($operatorsFromWorkRegisterHeaders as $operatorId => $workRegisterHeaders) {
            /** @var Operator $operator */
            $operator = $this->rm->getOperatorRepository()->find($operatorId);
            $spreadsheet->setActiveSheetIndex($x);
            $activeSheet = $spreadsheet->getActiveSheet()
                ->setTitle($operator->getSurname1())
            ;
            $activeSheet
                ->setTitle($operator->getSurname1())
                ->setCellValue('A1', 'NOM:')
                ->setCellValue('B1', $operator->getFullName())
                ->setCellValue('B2','PERIODE:'.$from.'A'.$to)
                ->setCellValue('A5','DIA')
                ->setCellValue('B5','DESPL')
                ->setCellValue('C5','ESPERA')
                ->setCellValue('D5','RETEN')
                ->setCellValue('E5','PLUS PERNOCTA')
                ->setCellValue('F5','PRIMA NITS')
                ->setCellValue('G5','PLUS CARRETERA')
                ->setCellValue('H5','H.EXTRA')
                ->setCellValue('I5','DINAR/SOPAR')
                ->setCellValue('J5','DIETA')
                ->setCellValue('K5','DINAR/SOPAR I')
                ->setCellValue('L5','DIETA I')
            ;
            $activeSheet
                ->getStyle('A5:L5')
                ->getBorders()
                ->getAllBorders()
                ->setBorderStyle(Border::BORDER_THIN)
            ;
            $i=6;
            /** @var OperatorWorkRegisterHeader $workRegisterHeader */
            foreach ($workRegisterHeaders as $workRegisterHeader) {
                $activeSheet
                    ->setCellValue('A'.$i, $workRegisterHeader->getDateFormatted())
                    ->setCellValue('B'.$i, $workRegisterHeader->getHours())
                    ;
                $i++;
            }
            $spreadsheet->createSheet();
            $x++;
        }

//        $activeSheet = $spreadsheet->getActiveSheet();
//        $activeSheet
//            ->setTitle('Informe horas Hoja 1')
//            ->setCellValue('A1', 'Informe horas')
//        ;
//        // Create new sheet
//        $sheet2 = $spreadsheet->createSheet()
//            ->setTitle('Informe Hoja 2')
//            ->setCellValue('B2', $from)
//        ;
//        //Example of iteration
//        $sheet2
//            ->setCellValue('B3', 'Fecha')
//            ->setCellValue('C3', 'Horas')
//            ;
//        $i = 4;
//        /** @var OperatorWorkRegisterHeader $operatorWorkRegisterHeader */
//        foreach ($operatorWorkRegisterHeaders as $operatorWorkRegisterHeader) {
//            $sheet2
//                ->setCellValue('B'.$i, $operatorWorkRegisterHeader->getDate())
//                ->setCellValue('C'.$i, $operatorWorkRegisterHeader->getHours())
//                ;
//            ++$i;
//        }

        return $spreadsheet;
    }
}
