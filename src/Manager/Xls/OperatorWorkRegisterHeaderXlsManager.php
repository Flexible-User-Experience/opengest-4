<?php

namespace App\Manager\Xls;

use App\Entity\Operator\Operator;
use App\Entity\Operator\OperatorWorkRegisterHeader;
use App\Manager\OperatorWorkRegisterHeaderManager;
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

    private OperatorWorkRegisterHeaderManager $operatorWorkRegisterHeaderManager;

    /**
     * Methods.
     */
    public function __construct(RepositoriesManager $rm, OperatorWorkRegisterHeaderManager $operatorWorkRegisterHeaderManager)
    {
        $this->rm = $rm;
        $this->operatorWorkRegisterHeaderManager = $operatorWorkRegisterHeaderManager;
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
                ->setTitle($operator->getSurname1());
            $activeSheet
                ->setTitle($operator->getSurname1())
                ->setCellValue('A1', 'NOM: ')
                ->setCellValue('B1', $operator->getFullName())
                ->setCellValue('B2', 'PERIODE: ' . $from . ' A ' . $to)
                ->setCellValue('A5', 'DIA')
                ->setCellValue('B5', 'DESPL')
                ->setCellValue('C5', 'ESPERA')
                ->setCellValue('D5', 'RETEN (???)')
                ->setCellValue('E5', 'PLUS PERNOCTA (Extras Pernoctación)')
                ->setCellValue('F5', 'PRIMA NITS (Extras Salida)')
                ->setCellValue('G5', 'PLUS CARRETERA (??)')
                ->setCellValue('H5', 'H.EXTRA')
                ->setCellValue('I5', 'DINAR/SOPAR')
                ->setCellValue('J5', 'DIETA')
                ->setCellValue('K5', 'DINAR/SOPAR I')
                ->setCellValue('L5', 'DIETA I')
                ->setCellValue('M5', 'H.Norm')
                ->setCellValue('N5', 'H.Neg')
                ->setCellValue('N5', 'H.Norm - H.Neg')
                ->setCellValue('N5', 'H.Extra');
            $activeSheet
                ->getStyle('A5:L5')
                ->getBorders()
                ->getAllBorders()
                ->setBorderStyle(Border::BORDER_THIN);
            $i = 6;
            /** @var OperatorWorkRegisterHeader $workRegisterHeader */
            foreach ($workRegisterHeaders as $workRegisterHeader) {
                $detailedHours = $this->operatorWorkRegisterHeaderManager->getTotalsFromWorkRegisterHeader($workRegisterHeader);
                $activeSheet
                    ->setCellValue('A' . $i, $workRegisterHeader->getDateFormatted())
                    ->setCellValue('B' . $i, $detailedHours['displacement'])
                    ->setCellValue('C' . $i, $detailedHours['waiting'])
                    ->setCellValue('D' . $i, '')
                    ->setCellValue('E' . $i, $detailedHours['overNight'])
                    ->setCellValue('F' . $i, $detailedHours['exitExtra'])
                    ->setCellValue('G' . $i, '')
                    ->setCellValue('H' . $i, $detailedHours['extraHours'])
                    ->setCellValue('I' . $i, $detailedHours['lunch'] + $detailedHours['dinner'])
                    ->setCellValue('J' . $i, $detailedHours['diet'])
                    ->setCellValue('K' . $i, $detailedHours['lunchInt'] + $detailedHours['dinnerInt'])
                    ->setCellValue('L' . $i, $detailedHours['dietInt'])
                    ->setCellValue('M' . $i, $detailedHours['normalHours'])
                    ->setCellValue('N' . $i, $detailedHours['negativeHours'])
                    ->setCellValue('O' . $i, $detailedHours['normalHours'] - $detailedHours['negativeHours'])
                    ->setCellValue('P' . $i, $detailedHours['extraHours']);
                $i++;
        }
            $i++;
            $totalHours = $this->operatorWorkRegisterHeaderManager->getTotalsFromDifferentWorkRegisterHeaders($workRegisterHeaders);
            $activeSheet
                ->setCellValue('A' . $i, 'TOTAL')
                ->setCellValue('B' . $i, $totalHours['displacement'])
                ->setCellValue('C' . $i, $totalHours['waiting'])
                ->setCellValue('D' . $i, '')
                ->setCellValue('E' . $i, $totalHours['overNight'])
                ->setCellValue('F' . $i, $totalHours['exitExtra'])
                ->setCellValue('G' . $i, '')
                ->setCellValue('H' . $i, $totalHours['extraHours'])
                ->setCellValue('I' . $i, $totalHours['lunch'] + $totalHours['dinner'])
                ->setCellValue('J' . $i, $totalHours['diet'])
                ->setCellValue('K' . $i, $totalHours['lunchInt'] + $totalHours['dinnerInt'])
                ->setCellValue('L' . $i, $totalHours['dietInt'])
                ->setCellValue('M' . $i, $totalHours['normalHours'])
                ->setCellValue('N' . $i, $totalHours['negativeHours'])
                ->setCellValue('O' . $i, $totalHours['normalHours'] - $totalHours['negativeHours'])
                ->setCellValue('P' . $i, $totalHours['extraHours']);
            $i++;
            $prices = $this->operatorWorkRegisterHeaderManager->getPricesForOperator($operator);
            $activeSheet
                ->setCellValue('A' . $i, 'PRECIO')
                ->setCellValue('B' . $i, $prices['normalHourPrice'])
                ->setCellValue('C' . $i, $prices['normalHourPrice'])
                ->setCellValue('D' . $i, '')
                ->setCellValue('E' . $i, $prices['overNightPrice'])
                ->setCellValue('F' . $i, $prices['exitExtraPrice'])
                ->setCellValue('G' . $i, '')
                ->setCellValue('H' . $i, $prices['extraHourPrice'])
                ->setCellValue('I' . $i, $prices['lunchPrice'])
                ->setCellValue('J' . $i, $prices['dietPrice'])
                ->setCellValue('K' . $i, $prices['lunchIntPrice'])
                ->setCellValue('L' . $i, $prices['dietIntPrice'])
                ->setCellValue('M' . $i, $prices['normalHourPrice'])
                ->setCellValue('N' . $i, $prices['negativeHourPrice'])
                ->setCellValue('P' . $i, $prices['extraHourPrice']);

            $i++;
            $activeSheet
                ->setCellValue('A' . $i, 'TOTAL')
                ->setCellValue('B' . $i, $totalHours['displacement']*$prices['normalHourPrice'])
                ->setCellValue('C' . $i, $totalHours['waiting']*$prices['normalHourPrice'])
                ->setCellValue('D' . $i, '')
                ->setCellValue('E' . $i, $totalHours['overNight']*$prices['overNightPrice'])
                ->setCellValue('F' . $i, $totalHours['exitExtra']*$prices['exitExtraPrice'])
                ->setCellValue('G' . $i, '')
                ->setCellValue('H' . $i, $totalHours['extraHours']*$prices['extraHourPrice'])
                ->setCellValue('I' . $i, $totalHours['lunch']*$prices['lunchPrice'])
                ->setCellValue('J' . $i, $totalHours['diet']*$prices['dietPrice'])
                ->setCellValue('K' . $i, $totalHours['lunchInt']*$prices['lunchIntPrice'])
                ->setCellValue('L' . $i, $totalHours['dietInt']*$prices['dietIntPrice'])
                ->setCellValue('M' . $i, $prices['normalHourPrice'])
                ->setCellValue('N' . $i, $prices['negativeHourPrice'])
                ->setCellValue('P' . $i, $prices['extraHourPrice']);
            $i++;
            $activeSheet
                ->setCellValue('A'.$i, 'TOTAL VARIS')
                ->setCellValue('B'.$i, '');
            $i++;
            $activeSheet
                ->setCellValue('A'.$i, 'PRIMA NUCLEAR')
                ->setCellValue('B'.$i, '');
            $i++;
            $activeSheet
                ->setCellValue('A'.$i, 'PRIMA TM')
                ->setCellValue('B'.$i, '');
            $i++;
            $activeSheet
                ->setCellValue('A'.$i, 'TOTAL')
                ->setCellValue('B'.$i, '');
            $i = $i+5;
            $activeSheet
                ->setCellValue('A'.$i, 'DESPLAÇAMENT')
                ->setCellValue('B'.$i, '');
            $i++;
            $activeSheet
                ->setCellValue('A'.$i, 'ESPERA')
                ->setCellValue('B'.$i, '');
            $i++;
            $activeSheet
                ->setCellValue('A'.$i, 'RETEN')
                ->setCellValue('B'.$i, '');
            $i++;
            $activeSheet
                ->setCellValue('A'.$i, 'PERNOCTA')
                ->setCellValue('B'.$i, '');
            $i++;
            $activeSheet
                ->setCellValue('A'.$i, 'CARRETERA')
                ->setCellValue('B'.$i, '');
            $i++;
            $activeSheet
                ->setCellValue('A'.$i, 'EXTRA')
                ->setCellValue('B'.$i, '');

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
