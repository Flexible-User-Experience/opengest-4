<?php

namespace App\Manager\Pdf;

use App\Entity\Operator\Operator;
use App\Enum\ConstantsEnum;
use App\Service\PdfEngineService;
use Doctrine\Common\Collections\Collection;
use TCPDF;

/**
 * Class OperatorDocumentationPdfManager.
 *
 * @category Manager
 */
class OperatorDocumentationPdfManager
{
    private PdfEngineService $pdfEngineService;

    /**
     * Methods.
     */
    public function __construct(PdfEngineService $pdfEngineService)
    {
        $this->pdfEngineService = $pdfEngineService;
    }

    public function buildSingle(Collection $operators, $documents): TCPDF
    {
        $this->pdfEngineService->initDefaultPageEngineWithTitle('Documentación operarios');
        $pdf = $this->pdfEngineService->getEngine();

        return $this->buildOnePayslipPerPage($operators, $documents, $pdf);
    }

    public function outputSingle(Collection $operators, $documents): string
    {
        $pdf = $this->buildSingle($operators, $documents);

        return $pdf->Output('Documentacion operario/s.pdf', 'I');
    }

    private function buildOnePayslipPerPage(Collection $operators, $documents, TCPDF $pdf): TCPDF
    {
        $pdf->setMargins(ConstantsEnum::PDF_PAGE_A4_MARGIN_LEFT, ConstantsEnum::PDF_PAGE_A4_MARGIN_TOP, ConstantsEnum::PDF_PAGE_A4_MARGIN_RIGHT, true);
        /** @var Operator $operator */
        foreach ($operators as $operator) {
            foreach ($documents[$operator->getId()] as $document) {
                $pdf->AddPage(ConstantsEnum::PDF_PORTRAIT_PAGE_ORIENTATION, ConstantsEnum::PDF_PAGE_A4);
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
                $today = date('d/m/Y');
                $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT,
                    $pdf->getAliasNumPage().'/'.$pdf->getAliasNbPages(),
                    0, 0, 'R', true);
                $pdf->Ln(15);
                $pdf->setY(15);
                $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT,
                    'Documento: '.$document['nameTranslated'],
                    0, 0, 'L', false);
                $pdf->setY(30);
                if ('pdf' === $document['fileType']) {
                    //TODO Show correctly when documentation is pdf
                } elseif (in_array($document['fileType'], ['png', 'jpeg', 'jpg'])) {
                    $pdf->Image('@'.$document['content']);
                }
            }
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
