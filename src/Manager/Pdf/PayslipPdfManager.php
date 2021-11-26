<?php

namespace App\Manager\Pdf;

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
        $pdf->AddPage(ConstantsEnum::PDF_LANDSCAPE_PAGE_ORIENTATION, ConstantsEnum::PDF_PAGE_A4);
        $pdf->SetFont(ConstantsEnum::PDF_DEFAULT_FONT, '', 9);
        $width = 70;
//        $total = $width + ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT;
        $availableHoritzontalSpace = 149 - (ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT * 2);

        // logo
        $pdf->Image($this->pdfEngineService->getSmartAssetsHelper()->getAbsoluteAssetFilePath('/bundles/app/img/logo_romani.png'), ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT, 5, 30); // TODO replace by enterprise image if defined

        // -- set new background ---
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

//        $pdf->setXY($total, 40);
//        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 10);
        //CLIENT
        $this->pdfEngineService->setStyleSize('', 9);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $payslip->getFromDateFormatted(), 0, 0, 'L', false);

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
