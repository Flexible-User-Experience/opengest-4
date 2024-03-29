<?php

namespace App\Manager\Pdf;

use App\Entity\Operator\Operator;
use App\Entity\Operator\OperatorChecking;
use App\Enum\ConstantsEnum;
use App\Manager\RepositoriesManager;
use App\Service\PdfEngineService;
use TCPDF;

/**
 * Class OperatorCheckingPdfManager.
 *
 * @category Manager
 */
class OperatorCheckingPdfManager
{
    private PdfEngineService $pdfEngineService;

    private RepositoriesManager $rm;

    /**
     * Methods.
     */
    public function __construct(PdfEngineService $pdfEngineService, RepositoriesManager $rm)
    {
        $this->pdfEngineService = $pdfEngineService;
        $this->rm = $rm;
    }

    public function buildSingle($operatorCheckings): TCPDF
    {
        $this->pdfEngineService->initDefaultPageEngineWithTitle('Revisiones de operarios');
        $pdf = $this->pdfEngineService->getEngine();

        return $this->buildOperatorCheckingsPdf($operatorCheckings, $pdf);
    }

    public function outputSingle($operatorCheckings): string
    {
        $pdf = $this->buildSingle($operatorCheckings);

        return $pdf->Output('Revisiones de operarios'.'.pdf', 'I');
    }

    private function buildOperatorCheckingsPdf(array $operatorCheckings, TCPDF $pdf): TCPDF
    {
        $this->startPage($pdf);

        $operatorsFromOperatorCheckings = [];
        /** @var OperatorChecking $operatorChecking */
        foreach ($operatorCheckings as $operatorChecking) {
            $operatorsFromOperatorCheckings[$operatorChecking->getOperator()->getId()][] = $operatorChecking;
        }
        $pdf->setXY(30, 40);
        foreach ($operatorsFromOperatorCheckings as $operatorId => $operatorCheckingsFromOperator) {
            if ($pdf->GetY() > 250){
                $this->startPage($pdf);
                $pdf->setXY(30, 40);
            }
            /** @var Operator $operator */
            $operator = $this->rm->getOperatorRepository()->find($operatorId);
            //Header

            $pdf->setX(ConstantsEnum::PDF_PAGE_A4_MARGIN_LEFT);
            $this->pdfEngineService->setStyleSize('b', 10);
            $pdf->Cell(90, ConstantsEnum::PDF_CELL_HEIGHT,
                $operator->getName(),
                true, 0, 'L', true);
            $pdf->Cell(80, ConstantsEnum::PDF_CELL_HEIGHT,
                'D.N.I. - '.$operator->getTaxIdentificationNumber(),
                true, 0, 'L', true);
            $pdf->Ln();
            //subheader
            $pdf->setX(ConstantsEnum::PDF_PAGE_A4_MARGIN_LEFT);
            $this->pdfEngineService->setStyleSize('', 9);
            $pdf->Cell(50, ConstantsEnum::PDF_CELL_HEIGHT,
                'Revisión',
                true, 0, 'C', true);
            $pdf->Cell(40, ConstantsEnum::PDF_CELL_HEIGHT,
                'Caduca',
                true, 0, 'C', true);
            $pdf->Cell(50, ConstantsEnum::PDF_CELL_HEIGHT,
                'Cita previa',
                true, 0, 'C', true);
            $pdf->Cell(30, ConstantsEnum::PDF_CELL_HEIGHT,
                'Revisado',
                true, 0, 'C', true);
            $pdf->Ln();
            //info
            /** @var OperatorChecking $operatorChecking */
            foreach ($operatorCheckingsFromOperator as $operatorChecking) {
                $pdf->setX(ConstantsEnum::PDF_PAGE_A4_MARGIN_LEFT);
                $this->pdfEngineService->setStyleSize('', 9);
                $pdf->Cell(50, ConstantsEnum::PDF_CELL_HEIGHT,
                    $operatorChecking->getType(),
                    true, 0, 'L', false);
                $pdf->Cell(40, ConstantsEnum::PDF_CELL_HEIGHT,
                    $operatorChecking->getEnd()->format('d/m/Y'),
                    true, 0, 'L', false);
                $pdf->Cell(50, ConstantsEnum::PDF_CELL_HEIGHT,
                    '',
                    true, 0, 'L', false);
                $pdf->Cell(30, ConstantsEnum::PDF_CELL_HEIGHT,
                    '',
                    true, 0, 'L', false);
                $pdf->Ln();
            }
            $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT,
                '',
                false, 0, 'L', false);
            $pdf->Ln();
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

    /**
     * @param TCPDF $pdf
     * @return void
     */
    private function startPage(TCPDF $pdf): void
    {
// add start page
        $pdf->setMargins(ConstantsEnum::PDF_PAGE_A4_MARGIN_LEFT, ConstantsEnum::PDF_PAGE_A4_MARGIN_TOP, ConstantsEnum::PDF_PAGE_A4_MARGIN_RIGHT, true);
        $pdf->AddPage(ConstantsEnum::PDF_PORTRAIT_PAGE_ORIENTATION, ConstantsEnum::PDF_PAGE_A4);
        $pdf->SetFont(ConstantsEnum::PDF_DEFAULT_FONT, '', 9);
        $width = ConstantsEnum::PDF_PAGE_A4_WIDTH_PORTRAIT - ConstantsEnum::PDF_PAGE_A4_MARGIN_RIGHT - ConstantsEnum::PDF_PAGE_A4_MARGIN_LEFT;

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

        // logo
        $pdf->Image($this->pdfEngineService->getSmartAssetsHelper()->getAbsoluteAssetFilePath('/build/img/logo_empresa.png'), ConstantsEnum::PDF_PAGE_A4_MARGIN_LEFT, 5, 30); // TODO replace by enterprise image if defined
    }
}
