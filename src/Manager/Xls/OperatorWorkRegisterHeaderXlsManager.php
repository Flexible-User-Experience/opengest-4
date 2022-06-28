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
        usort($operatorWorkRegisterHeaders, function (OperatorWorkRegisterHeader $a, OperatorWorkRegisterHeader $b) {
            return strcasecmp($a->getOperator()->getSurname1(), $b->getOperator()->getSurname1());
        });
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
                ->setCellValue('A2', 'PERIODE:')
                ->setCellValue('B2', $from. ' A ' . $to)
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
                ->setCellValue('O5', 'H.Norm - H.Neg')
                ->setCellValue('P5', 'H.Extra');

            usort($workRegisterHeaders, function ($a, $b) {
                return $a->getDate()->getTimestamp() - $b->getDate()->getTimestamp();
            });
            $i = 6;
            $workingDays = 28;
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
                    ->setCellValue('O' . $i, $detailedHours['normalHours'] + $detailedHours['negativeHours'])
                    ->setCellValue('P' . $i, $detailedHours['extraHours']);

                $otherAmounts = 0;
                /** @var OperatorWorkRegister $workRegister */
                foreach ($workRegisterHeader->getOperatorWorkRegisters() as $workRegister) {
                    if (
                        !str_contains($workRegister->getDescription(), 'Hora laboral') &&
                        !str_contains($workRegister->getDescription(), 'Hora normal') &&
                        !str_contains($workRegister->getDescription(), 'Hora extra') &&
                        !str_contains($workRegister->getDescription(), 'Hora negativa') &&
                        !str_contains($workRegister->getDescription(), 'Comida') &&
                        !str_contains($workRegister->getDescription(), 'Cena') &&
                        !str_contains($workRegister->getDescription(), 'Comida internacional') &&
                        !str_contains($workRegister->getDescription(), 'Cena internacional') &&
                        !str_contains($workRegister->getDescription(), 'Dieta') &&
                        !str_contains($workRegister->getDescription(), 'Dieta internacional') &&
                        !str_contains($workRegister->getDescription(), 'Pernoctación') &&
                        !str_contains($workRegister->getDescription(), 'Plus carretera') &&
                        !str_contains($workRegister->getDescription(), 'Salida')
                    ) {
                        $activeSheet
                            ->setCellValue('A53', 'ALTRES CONCEPTES')
                            ->setCellValue('A54', 'DATA')
                            ->setCellValue('B54', 'CONCEPTE')
                            ->setCellValue('C54', 'IMPORT')
                            ->setCellValue('A55', $workRegisterHeader->getDateFormatted())
                            ->setCellValue('B55', $workRegister->getDescription())
                            ->setCellValue('C55', $workRegister->getAmount());
                    }
                }
                $i++;
            }
            $activeSheet
                ->getStyle('A5:L33')
                ->getBorders()
                ->getAllBorders()
                ->setBorderStyle(Border::BORDER_THIN);

            $column = 'A';
            while($column < 'L')
            {
                $activeSheet
                    ->getColumnDimension($column)
                    ->setAutoSize(TRUE);
                $column ++;
            }
            $i=$workingDays+6;
            $totalHours = $this->operatorWorkRegisterHeaderManager->getTotalsFromDifferentWorkRegisterHeaders($workRegisterHeaders);
            $activeSheet
                ->setCellValue('A' . $i, 'TOTAL')
                ->setCellValue('B' . $i, '=SUM(B6:B33)')
                ->setCellValue('C' . $i, '=SUM(C6:C33)')
                ->setCellValue('D' . $i, '=SUM(D6:D33)')
                ->setCellValue('E' . $i, '=SUM(E6:E33)')
                ->setCellValue('F' . $i, '=SUM(F6:F33)')
                ->setCellValue('G' . $i, '=SUM(G6:G33)')
                ->setCellValue('H' . $i, '=SUM(H6:H33)')
                ->setCellValue('I' . $i, '=SUM(I6:I33)')
                ->setCellValue('J' . $i, '=SUM(J6:J33)')
                ->setCellValue('K' . $i, '=SUM(K6:K33)')
                ->setCellValue('L' . $i, '=SUM(L6:L33)')
                ->setCellValue('M' . $i, '=SUM(M6:M33)')
                ->setCellValue('N' . $i, '=SUM(N6:N33)')
                ->setCellValue('O' . $i, '=SUM(O6:O33)')
                ->setCellValue('P' . $i, '=SUM(P6:P33)');
            $i=$workingDays+6+1;
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

            $i=$workingDays+6+2;
            $activeSheet
                ->setCellValue('A' . $i, 'TOTAL')
                ->setCellValue('B' . $i, '=B34*B35')
                ->setCellValue('C' . $i, '=C34*C35')
                ->setCellValue('D' . $i, '=D34*D35')
                ->setCellValue('E' . $i, '=E34*E35')
                ->setCellValue('F' . $i, '=F34*F35')
                ->setCellValue('G' . $i, '=G34*G35')
                ->setCellValue('H' . $i, '=H34*H35')
                ->setCellValue('I' . $i, '=I34*I35')
                ->setCellValue('J' . $i, '=J34*J35')
                ->setCellValue('K' . $i, '=K34*K35')
                ->setCellValue('L' . $i, '=L34*L35')
                ->setCellValue('M' . $i, '=M34*M35')
                ->setCellValue('N' . $i, '=N34*N35')
                ->setCellValue('P' . $i, '=O34*O35');
            $i=$workingDays+6+4;
            $activeSheet
                ->setCellValue('A'.$i, 'TOTAL VARIS')
                ->setCellValue('B'.$i, '');
            $i=$workingDays+6+5;
            $activeSheet
                ->setCellValue('A'.$i, 'PRIMA NUCLEAR')
                ->setCellValue('B'.$i, '');
            $i=$workingDays+6+6;
            $activeSheet
                ->setCellValue('A'.$i, 'PRIMA TM')
                ->setCellValue('B'.$i, '');
            $i = $workingDays+6+7;
            $activeSheet
                ->setCellValue('A'.$i, 'TOTAL')
                ->setCellValue('B'.$i, '=SUM(B36:L36)+B38+B39+B40');
            $i = $workingDays+6+11;
            $activeSheet
                ->setCellValue('A'.$i, 'DESPLAÇAMENT')
                ->setCellValue('B'.$i, '=B36');
            $i++;
            $activeSheet
                ->setCellValue('A'.$i, 'ESPERA')
                ->setCellValue('B'.$i, '=C36');
            $i++;
            $activeSheet
                ->setCellValue('A'.$i, 'RETEN')
                ->setCellValue('B'.$i, '=D36');
            $i++;
            $activeSheet
                ->setCellValue('A'.$i, 'PERNOCTA')
                ->setCellValue('B'.$i, '=E36');
            $i++;
            $activeSheet
                ->setCellValue('A'.$i, 'CARRETERA')
                ->setCellValue('B'.$i, '=G36');
            $i++;
            $activeSheet
                ->setCellValue('A'.$i, 'EXTRA')
                ->setCellValue('B'.$i, '=H36');

            $spreadsheet->createSheet();
            $x++;

        }

        return $spreadsheet;
    }
}
