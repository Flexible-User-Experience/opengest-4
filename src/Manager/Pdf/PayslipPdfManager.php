<?php

namespace App\Manager\Pdf;

use App\Entity\Operator\OperatorWorkRegister;
use App\Entity\Operator\OperatorWorkRegisterHeader;
use App\Entity\Payslip\Payslip;
use App\Entity\Payslip\PayslipLine;
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

    /**
     * @param Payslip[]|ArrayCollection|array $payslips
     *
     * @return TCPDF
     */
    public function buildCollection($payslips)
    {
        $this->pdfEngineService->initDefaultPageEngineWithTitle('Grupo de nóminas');
        $pdf = $this->pdfEngineService->getEngine();
        /** @var Payslip $payslips */
        foreach ($payslips as $payslip) {
            $pdf = $this->buildOnePayslipPerPage($payslip, $pdf);
        }

        return $pdf;
    }

    /**
     * @param Payslip[]|ArrayCollection|array $payslips
     *
     * @return string
     */
    public function outputCollection($payslips)
    {
        $pdf = $this->buildCollection($payslips);

        return $pdf->Output('grupo_nóminas.pdf', 'I');
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
        $today = date('d/m/Y');
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT,
            'Informe nómina general - Grúas Romaní - '.$today,
            1, 0, 'L', true);

        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT,
            $pdf->getAliasNumPage().'/'.$pdf->getAliasNbPages(),
            0, 0, 'R', true);
        $pdf->Ln(15);

        //Operator and period info
        $this->pdfEngineService->setStyleSize('B', 12);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT,
            'PERIODO DE LIQUIDACIÓN: DESDE '.$payslip->getFromDateFormatted().' HASTA '.$payslip->getToDateFormatted(),
            0, 0, 'L', false);
        $pdf->Ln(10);
        $this->pdfEngineService->setStyleSize('B', 10);
        $pdf->Cell($width / 2, ConstantsEnum::PDF_CELL_HEIGHT,
            'OPERARIO: '.$payslip->getOperator(),
            0, 0, 'L', false);
        $pdf->Ln(10);

        //Start table
        $this->pdfEngineService->setStyleSize('', 9);
        $col1 = 50;
        $col2 = 30;
        $col3 = 35;
        $pdf->Cell($col1, ConstantsEnum::PDF_CELL_HEIGHT,
            '',
            0, 0, 'L', false);
        $pdf->Cell($col2, ConstantsEnum::PDF_CELL_HEIGHT,
            'Precio/Unidad',
            1, 0, 'C', false);
        $pdf->Cell($col2, ConstantsEnum::PDF_CELL_HEIGHT,
            'Unidades',
            1, 0, 'C', false);
        $pdf->Cell($col2, ConstantsEnum::PDF_CELL_HEIGHT,
            'Total',
            1, 0, 'C', false);
        $pdf->Ln();
        /** @var PayslipLine $payslipLine */
        foreach($payslip->getPayslipLines() as $payslipLine){
            //payslip lines info
            $pdf->Cell($col1, ConstantsEnum::PDF_CELL_HEIGHT,
                $payslipLine->getPayslipLineConcept(),
                1, 0, 'L', false);
            $pdf->Cell($col2, ConstantsEnum::PDF_CELL_HEIGHT,
                $payslipLine->getPriceUnit(),
                1, 0, 'C', false);
            $pdf->Cell($col2, ConstantsEnum::PDF_CELL_HEIGHT,
                $payslipLine->getUnits(),
                1, 0, 'C', false);
            $pdf->Cell($col2, ConstantsEnum::PDF_CELL_HEIGHT,
                $payslipLine->getAmount(),
                1, 0, 'C', false);
            $pdf->Ln();
        }

        //Totals section
        $payslipInicialAmount = $payslip->getTotalAmount() + $payslip->getSocialSecurityCost()
            - $payslip->getOtherCosts() - $payslip->getExpenses() - $payslip->getExtraPay();
        $pdf->setXY(190,40);
        $pdf->Cell($col3, ConstantsEnum::PDF_CELL_HEIGHT,
            'Nómina:',
            0, 0, 'R', false);
        $pdf->Cell($col3, ConstantsEnum::PDF_CELL_HEIGHT,
            $payslipInicialAmount.'€',
            0, 0, 'L', false);
        $pdf->Ln();
        $pdf->SetX(190);
        $pdf->Cell($col3, ConstantsEnum::PDF_CELL_HEIGHT,
            'Plus Productividad:',
            0, 0, 'R', false);
        $pdf->Cell($col3, ConstantsEnum::PDF_CELL_HEIGHT,
            $payslip->getExtraPay().'€',
            0, 0, 'L', false);
        $pdf->Ln();
        $pdf->SetX(190);
        $pdf->Cell($col3, ConstantsEnum::PDF_CELL_HEIGHT,
            'Dietas:',
            0, 0, 'R', false);
        $pdf->Cell($col3, ConstantsEnum::PDF_CELL_HEIGHT,
            $payslip->getExpenses().'€',
            0, 0, 'L', false);
        $pdf->Ln();
        $pdf->SetX(190);
        $pdf->Cell($col3, ConstantsEnum::PDF_CELL_HEIGHT,
            'Otros costes:',
            0, 0, 'R', false);
        $pdf->Cell($col3, ConstantsEnum::PDF_CELL_HEIGHT,
            $payslip->getOtherCosts().'€',
            0, 0, 'L', false);
        $pdf->Ln();
        $pdf->SetX(190);
        $YDim = $pdf->GetY();
        $pdf->Line(190,$YDim,250,$YDim);
        $pdf->Cell($col3, ConstantsEnum::PDF_CELL_HEIGHT,
            'Total:',
            0, 0, 'R', false);
        $payslipAmountWithoutSS = $payslip->getTotalAmount() + $payslip->getSocialSecurityCost();
        $pdf->Cell($col3, ConstantsEnum::PDF_CELL_HEIGHT,
            $payslipAmountWithoutSS.'€',
            0, 0, 'L', false);
        $pdf->Ln();
        $pdf->SetX(190);
        $pdf->Cell($col3, ConstantsEnum::PDF_CELL_HEIGHT,
            'Retenciones:',
            0, 0, 'R', false);
        $pdf->Cell($col3, ConstantsEnum::PDF_CELL_HEIGHT,
            $payslip->getSocialSecurityCost().'€',
            0, 0, 'L', false);
        $pdf->Ln();
        $pdf->SetX(190);
        $YDim = $pdf->GetY();
        $pdf->Line(190,$YDim,250,$YDim);
        $pdf->Cell($col3, ConstantsEnum::PDF_CELL_HEIGHT,
            'TOTAL:',
            0, 0, 'R', false);
        $pdf->Cell($col3, ConstantsEnum::PDF_CELL_HEIGHT,
            $payslip->getTotalAmount().'€',
            0, 0, 'L', false);
        $pdf->Ln();

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
