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
        $cellWidth = 20;
        $pdf->Cell($cellWidth * 1, ConstantsEnum::PDF_CELL_HEIGHT,
            '',
            0, 0, 'L', false);

        $pdf->Cell($cellWidth * 4, ConstantsEnum::PDF_CELL_HEIGHT,
            'HORAS',
            1, 0, 'L', false);
        $pdf->Cell($cellWidth * 7, ConstantsEnum::PDF_CELL_HEIGHT,
            'DIETAS',
            1, 0, 'L', false);
        $pdf->Cell($cellWidth * 2, ConstantsEnum::PDF_CELL_HEIGHT,
            'EXTRAS',
            1, 0, 'L', false);
        $pdf->Ln();
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            'DÍA',
            0, 0, 'L', false);
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
        $totalLunch = 0;
        /** @var OperatorWorkRegisterHeader $workRegisterHeader */
        foreach ($workRegisterHeaders as $workRegisterHeader) {
            $workRegisters = $workRegisterHeader->getOperatorWorkRegisters();
            $workingHours = 0;
            $normalHours = 0;
            $extraHours = 0;
            $lunch = 0;
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
                if (str_contains($workRegister->getDescription(), 'Comida')) {
                    $lunch += $workRegister->getUnits();
                }
            }
            $totalWorkingHours += $workingHours;
            $totalNormalHours += $normalHours;
            $totalExtraHours += $extraHours;
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
                'NEG.',
                1, 0, 'L', false);
            $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
                $lunch,
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
        }

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
