<?php

namespace App\Manager\Pdf;

use App\Entity\Operator\OperatorWorkRegister;
use App\Entity\Operator\OperatorWorkRegisterHeader;
use App\Entity\Payslip\Payslip;
use App\Enum\ConstantsEnum;
use App\Service\PdfEngineService;
use TCPDF;

/**
 * Class PayslipPdfManager.
 *
 * @category Manager
 */
class PayslipPdfManager
{
    private PdfEngineService $pdfEngineService;

    /**
     * Methods.
     */
    public function __construct(PdfEngineService $pdfEngineService)
    {
        $this->pdfEngineService = $pdfEngineService;
    }

    public function buildSingle(Payslip $payslip): TCPDF
    {
        $this->pdfEngineService->initDefaultPageEngineWithTitle('Nómina detallado '.$payslip);
        $pdf = $this->pdfEngineService->getEngine();

        return $this->buildOnePayslipPerPage($payslip, $pdf);
    }

    public function outputSingle(Payslip $payslip): string
    {
        $pdf = $this->buildSingle($payslip);

        return $pdf->Output('nómina_detallada'.$payslip->getId().'.pdf', 'I');
    }

    private function buildOnePayslipPerPage(Payslip $payslip, TCPDF $pdf): TCPDF
    {
        // add start page
        $pdf->setMargins(ConstantsEnum::PDF_PAGE_A4_MARGIN_LEFT, ConstantsEnum::PDF_PAGE_A4_MARGIN_TOP, ConstantsEnum::PDF_PAGE_A4_MARGIN_RIGHT, true);
        $pdf->AddPage(ConstantsEnum::PDF_LANDSCAPE_PAGE_ORIENTATION, ConstantsEnum::PDF_PAGE_A4);
        $pdf->SetFont(ConstantsEnum::PDF_DEFAULT_FONT, '', 9);
        $width = ConstantsEnum::PDF_PAGE_A4_WIDTH_LANDSCAPE - ConstantsEnum::PDF_PAGE_A4_MARGIN_RIGHT - ConstantsEnum::PDF_PAGE_A4_MARGIN_LEFT;

        // get the current page break margin
        $bMargin = $pdf->getBreakMargin();
        // get current auto-page-break mode
        $auto_page_break = $pdf->getAutoPageBreak();
        // disable auto-page-break
        $pdf->SetAutoPageBreak(false, 0);
        // restore auto-page-break status
        $pdf->SetAutoPageBreak($auto_page_break, $bMargin);
        // set the starting point for the page content
        $pdf->setPageMark();
        // set cell padding
        $pdf->setCellPaddings(1, 1, 1, 1);

        //Heading with date and page number
//        $this->pdfEngineService->setStyleSize('', 9);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT,
            $payslip->getFromDateFormatted(),
            1, 0, 'L', true);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT,
            $pdf->getAliasNumPage().'/'.$pdf->getAliasNbPages(),
            0, 0, 'R', true);
        $pdf->Ln();

        //Operator and period info
        $this->pdfEngineService->setStyleSize('B', 9);
        $pdf->Cell($width / 2, ConstantsEnum::PDF_CELL_HEIGHT,
            'OPERARIO: '.$payslip->getOperator(),
            0, 0, 'L', false);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT,
            'PERIODO: '.$payslip->getFromDateFormatted().' HASTA '.$payslip->getToDateFormatted(),
            0, 0, 'L', false);
        $pdf->Ln();

        //Start table
        $cellWidth = $width/13+1;
        $pdf->Cell($cellWidth * 1, ConstantsEnum::PDF_CELL_HEIGHT,
            '',
            0, 0, 'L', false);

        $pdf->Cell($cellWidth * 4, ConstantsEnum::PDF_CELL_HEIGHT,
            'HORAS',
            1, 0, 'C', false);
        $pdf->Cell($cellWidth * 7, ConstantsEnum::PDF_CELL_HEIGHT,
            'DIETAS',
            1, 0, 'C', false);
        $pdf->Cell($cellWidth * 2, ConstantsEnum::PDF_CELL_HEIGHT,
            'EXTRAS',
            1, 0, 'C', false);
        $pdf->Ln();
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            'DÍA',
            0, 0, 'C', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            'LAB.',
            1, 0, 'L', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            'NORM.',
            1, 0, 'L', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            'EXTRA',
            1, 0, 'L', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            'NEG.',
            1, 0, 'L', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            'COMIDA',
            1, 0, 'L', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            'CENA',
            1, 0, 'L', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            'COM.I',
            1, 0, 'L', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            'CEN.I',
            1, 0, 'L', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            'DIETA',
            1, 0, 'L', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            'DIE.I',
            1, 0, 'L', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            'PERN.',
            1, 0, 'L', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            'CARRET.',
            1, 0, 'L', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            'SALIDA',
            1, 0, 'L', false);
        $pdf->ln();

        // start getting info
        $fromDate = $payslip->getFromDate();
        $toDate = $payslip->getToDate();
        $workRegisterHeaders = $payslip->getOperator()->getWorkRegisterHeaders()->filter(function (OperatorWorkRegisterHeader $owrh) use ($fromDate, $toDate) {
            return ($owrh->getDate() >= $fromDate) && ($owrh->getDate() <= $toDate);
        });
        $bountyGroup = $payslip->getOperator()->getEnterpriseGroupBounty();
        $workingHourPrice = $bountyGroup->getNormalHour();
        // totals
        $totalWorkingHours = 0;
        $totalNormalHours = 0;
        $totalExtraHours = 0;
        $totalNegativeHours = 0;
        $totalLunch = 0;
        $totalLunchInt = 0;
        $totalDinner = 0;
        $totalDinnerInt = 0;
        $totalDiet = 0;
        $totalDietInt = 0;
        $totalOverNight = 0;
        $totalRoadExtra = 0;
        $totalExitExtra = 0;
        /** @var OperatorWorkRegisterHeader $workRegisterHeader */
        foreach ($workRegisterHeaders as $workRegisterHeader) {
            $workRegisters = $workRegisterHeader->getOperatorWorkRegisters();
            $workingHours = 0;
            $normalHours = 0;
            $extraHours = 0;
            $negativeHours = 0;
            $lunch = 0;
            $lunchInt = 0;
            $dinner = 0;
            $dinnerInt = 0;
            $diet = 0;
            $dietInt = 0;
            $overNight = 0;
            $roadExtra = 0;
            $exitExtra = 0;
            /** @var OperatorWorkRegister $workRegister */
            foreach ($workRegisters as $workRegister) {
                if (str_contains($workRegister->getDescription(), 'Hora laboral')) {
                    $workingHours += $workRegister->getUnits();
                }
                if (str_contains($workRegister->getDescription(), 'Hora normal')) {
                    $normalHours += $workRegister->getUnits();
                }
                if (str_contains($workRegister->getDescription(), 'Hora extra')) {
                    $extraHours += $workRegister->getUnits();
                }
                if (str_contains($workRegister->getDescription(), 'Hora negativa')) {
                    $negativeHours += $workRegister->getUnits();
                }
                if (str_contains($workRegister->getDescription(), 'Comida')) {
                    $lunch += $workRegister->getUnits();
                }
                if (str_contains($workRegister->getDescription(), 'Cena')) {
                    $dinner += $workRegister->getUnits();
                }
                if (str_contains($workRegister->getDescription(), 'Comida internacional')) {
                    $lunchInt += $workRegister->getUnits();
                }
                if (str_contains($workRegister->getDescription(), 'Cena internacional')) {
                    $dinnerInt += $workRegister->getUnits();
                }
                if (str_contains($workRegister->getDescription(), 'Dieta')) {
                    $diet+= $workRegister->getUnits();
                }
                if (str_contains($workRegister->getDescription(), 'Dieta internacional')) {
                    $dietInt += $workRegister->getUnits();
                }
                if (str_contains($workRegister->getDescription(), 'Pernoctación')) {
                    $overNight += $workRegister->getUnits();
                }
                if (str_contains($workRegister->getDescription(), 'Plus carretera')) {
                    $roadExtra += $workRegister->getUnits();
                }
                if (str_contains($workRegister->getDescription(), 'Salida')) {
                    $exitExtra += $workRegister->getUnits();
                }
            }
            $totalWorkingHours += $workingHours;
            $totalNormalHours += $normalHours;
            $totalExtraHours += $extraHours;
            $totalNegativeHours += $negativeHours;
            $totalLunch += $lunch;
            $totalLunchInt += $lunchInt;
            $totalDinner += $dinner;
            $totalDinnerInt += $dinnerInt;
            $totalDiet += $diet;
            $totalDietInt += $dietInt;
            $totalOverNight += $overNight;
            $totalRoadExtra += $roadExtra;
            $totalExitExtra += $exitExtra;
            // Draw each line, as every workReagister header refers to a date
            $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
                $workRegisterHeader->getDateFormatted(),
                1, 0, 'L', false);
            $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
                $workingHours,
                1, 0, 'L', false);
            $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
                $normalHours,
                1, 0, 'L', false);
            $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
                $extraHours,
                1, 0, 'L', false);
            $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
                $negativeHours,
                1, 0, 'L', false);
            $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
                $lunch,
                1, 0, 'L', false);
            $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
                $dinner,
                1, 0, 'L', false);
            $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
                $lunchInt,
                1, 0, 'L', false);
            $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
                $dinnerInt,
                1, 0, 'L', false);
            $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
                $diet,
                1, 0, 'L', false);
            $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
                $dietInt,
                1, 0, 'L', false);
            $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
                $overNight,
                1, 0, 'L', false);
            $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
                $roadExtra,
                1, 0, 'L', false);
            $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
                $exitExtra,
                1, 0, 'L', false);
            $pdf->Ln();
        }
        // Draw sum per concept
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            'Suma',
            'B', 0, 'L', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            $totalWorkingHours,
            1, 0, 'L', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            $totalNormalHours,
            1, 0, 'L', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            $totalExtraHours,
            1, 0, 'L', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            $totalNegativeHours,
            1, 0, 'L', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            $totalLunch,
            1, 0, 'L', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            $totalDinner,
            1, 0, 'L', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            $totalLunchInt,
            1, 0, 'L', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            $totalDinnerInt,
            1, 0, 'L', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            $totalDiet,
            1, 0, 'L', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            $totalDietInt,
            1, 0, 'L', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            $totalOverNight,
            1, 0, 'L', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            $totalRoadExtra,
            1, 0, 'L', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            $totalExitExtra,
            1, 0, 'L', false);
        $pdf->Ln();

        // Draw price per concept
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            'Precio unit.',
            'B', 0, 'L', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            $bountyGroup->getNormalHour(),
            1, 0, 'L', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            $bountyGroup->getExtraNormalHour(),
            1, 0, 'L', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            $bountyGroup->getExtraExtraHour(),
            1, 0, 'L', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            $bountyGroup->getNegativeHour(),
            1, 0, 'L', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            $bountyGroup->getLunch(),
            1, 0, 'L', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            $bountyGroup->getDinner(),
            1, 0, 'L', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            $bountyGroup->getInternationalLunch(),
            1, 0, 'L', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            $bountyGroup->getInternationalDinner(),
            1, 0, 'L', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            $bountyGroup->getDiet(),
            1, 0, 'L', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            $bountyGroup->getExtraNight(),
            1, 0, 'L', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            $bountyGroup->getOverNight(),
            1, 0, 'L', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            $bountyGroup->getRoadExtraHour(),
            1, 0, 'L', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            $bountyGroup->getCarOutput(),
            1, 0, 'L', false);
        $pdf->Ln();

        // Draw total per concept
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            'Total (€)',
            'B', 0, 'L', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            $bountyGroup->getNormalHour() * $totalWorkingHours,
            1, 0, 'L', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            $bountyGroup->getExtraNormalHour() * $totalNormalHours,
            1, 0, 'L', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            $bountyGroup->getExtraExtraHour() * $totalExtraHours,
            1, 0, 'L', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            $bountyGroup->getNegativeHour() * $totalNegativeHours,
            1, 0, 'L', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            $bountyGroup->getLunch() * $totalLunch,
            1, 0, 'L', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            $bountyGroup->getDinner() * $totalDinner,
            1, 0, 'L', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            $bountyGroup->getInternationalLunch() * $totalLunchInt,
            1, 0, 'L', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            $bountyGroup->getInternationalDinner() * $totalDinnerInt,
            1, 0, 'L', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            $bountyGroup->getDiet() * $totalDiet,
            1, 0, 'L', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            $bountyGroup->getExtraNight() * $totalDietInt,
            1, 0, 'L', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            $bountyGroup->getOverNight() * $totalOverNight,
            1, 0, 'L', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            $bountyGroup->getRoadExtraHour() * $totalRoadExtra,
            1, 0, 'L', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            $bountyGroup->getCarOutput() * $totalExitExtra,
            1, 0, 'L', false);
        $pdf->Ln(10);
        $finalSum = $bountyGroup->getNormalHour() * $totalWorkingHours +
            $bountyGroup->getExtraNormalHour() * $totalNormalHours +
            $bountyGroup->getExtraExtraHour() * $totalExtraHours +
            $bountyGroup->getNegativeHour() * $totalNegativeHours +
            $bountyGroup->getLunch() * $totalLunch +
            $bountyGroup->getDinner() * $totalDinner +
            $bountyGroup->getInternationalLunch() * $totalLunchInt +
            $bountyGroup->getInternationalDinner() * $totalDinnerInt +
            $bountyGroup->getDiet() * $totalDiet +
            $bountyGroup->getExtraNight() * $totalDietInt +
            $bountyGroup->getOverNight() * $totalOverNight +
            $bountyGroup->getRoadExtraHour() * $totalRoadExtra +
            $bountyGroup->getCarOutput() * $totalExitExtra;
        //Other imports and final totals
        $pdf->Cell($cellWidth*5, ConstantsEnum::PDF_CELL_HEIGHT,
            'Importes varios',
            1, 0, 'C', false);
        $pdf->Ln();
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            'Fecha',
            1, 0, 'L', false);
        $pdf->Cell($cellWidth*3, ConstantsEnum::PDF_CELL_HEIGHT,
            'Concepto',
            1, 0, 'L', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            'Total',
            1, 0, 'L', false);
        $pdf->Ln();
        $totalOtherAmounts = 0;
        /** @var OperatorWorkRegisterHeader $workRegisterHeader */
        foreach ($workRegisterHeaders as $workRegisterHeader) {
            $otherAmounts = 0;
            foreach( $workRegisterHeader->getOperatorWorkRegisters() as $workRegister)
            {
                if
                (
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
                    $otherAmounts += $workRegister->getAmount();
                    $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
                        $workRegisterHeader->getDateFormatted(),
                        1, 0, 'L', false);
                    $pdf->Cell($cellWidth*3, ConstantsEnum::PDF_CELL_HEIGHT,
                        $workRegister->getDescription(),
                        1, 0, 'L', false);
                    $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
                        $workRegister->getAmount(),
                        1, 0, 'L', false);
                    $pdf->Ln();
                }
            }
            $totalOtherAmounts += $otherAmounts;
        }
        $pdf->Ln();
        $pdf->SetX(230);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            'Suma:',
            0, 0, 'R', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            $finalSum.' €',
            0, 0, 'R', false);
        $pdf->Ln();
        $pdf->SetX(230);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            'Prima por TM:',
            0, 0, 'R', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            '???',
            0, 0, 'R', false);
        $pdf->Ln();
        $pdf->SetX(230);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            'Importes varios',
            0, 0, 'R', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            $totalOtherAmounts.' €',
            0, 0, 'R', false);
        $pdf->Ln();
        $pdf->SetX(230);
        $this->pdfEngineService->setStyleSize('B', 12);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            'Total (€)',
            'T', 0, 'R', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            $totalOtherAmounts + $finalSum.' €',
            'T', 0, 'R', false);
        return $pdf;
    }

    /**
     * @param int $availableHoritzontalSpace
     */
    private function drawHoritzontalLineSeparator(TCPDF $pdf, $availableHoritzontalSpace)
    {
        $pdf->ln(4);
        $pdf->Line(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT, $pdf->getY(), $availableHoritzontalSpace + ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT, $pdf->getY());
        $pdf->ln(4);
    }
}
