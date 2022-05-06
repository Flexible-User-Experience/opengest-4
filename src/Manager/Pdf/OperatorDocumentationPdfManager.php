<?php

namespace App\Manager\Pdf;

use App\Entity\Operator\Operator;
use App\Enum\ConstantsEnum;
use Doctrine\Common\Collections\Collection;
use setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException;
use setasign\Fpdi\PdfParser\Filter\FilterException;
use setasign\Fpdi\PdfParser\PdfParserException;
use setasign\Fpdi\PdfParser\StreamReader;
use setasign\Fpdi\PdfParser\Type\PdfTypeException;
use setasign\Fpdi\PdfReader\PdfReaderException;
use setasign\Fpdi\Tcpdf\Fpdi;
use TCPDF;

/**
 * Class OperatorDocumentationPdfManager.
 *
 * @category Manager
 */
class OperatorDocumentationPdfManager
{
    public function buildSingle(Collection $operators, $documents): TCPDF
    {
        $pdf = new Fpdi();

        return $this->buildOnePayslipPerPage($operators, $documents, $pdf);
    }

    public function outputSingle(Collection $operators, $documents): string
    {
        $pdf = $this->buildSingle($operators, $documents);

        return $pdf->Output('Documentacion operario/s.pdf', 'I');
    }

    /**
     * @throws CrossReferenceException
     * @throws PdfReaderException
     * @throws PdfParserException
     * @throws PdfTypeException
     * @throws FilterException
     */
    private function buildOnePayslipPerPage(Collection $operators, $documents, Fpdi $pdf): TCPDF
    {
        $pdf->setMargins(ConstantsEnum::PDF_PAGE_A4_MARGIN_LEFT, ConstantsEnum::PDF_PAGE_A4_MARGIN_TOP, ConstantsEnum::PDF_PAGE_A4_MARGIN_RIGHT, true);
        $today = date('d/m/Y');
        /** @var Operator $operator */
        foreach ($operators as $operator) {
            foreach ($documents[$operator->getId()] as $document) {
                $pdf->AddPage(ConstantsEnum::PDF_PORTRAIT_PAGE_ORIENTATION, ConstantsEnum::PDF_PAGE_A4);
                //Heading with date and page number
                $pdf->SetFont(ConstantsEnum::PDF_DEFAULT_FONT, '', 9);
                $bMargin = $pdf->getBreakMargin();
                $auto_page_break = $pdf->getAutoPageBreak();
                $pdf->SetAutoPageBreak(false, 0);
                $pdf->SetAutoPageBreak($auto_page_break, $bMargin);
                $pdf->setPageMark();
                $pdf->setCellPaddings(1, 1, 1, 1);
                $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT,
                    'Fecha generación: '.$today.'      '.$pdf->getAliasNumPage().'/'.$pdf->getAliasNbPages(),
                    0, 0, 'R', false);
                $pdf->setY(5);
                $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT,
                    'Documento: '.$document['nameTranslated'],
                    0, 0, 'L', false);
                if ('pdf' === $document['fileType']) {
                    //TODO Show correctly when documentation is pdf
                    $pdf->setSourceFile(StreamReader::createByString($document['content']));
                    $pdfDocumentPage = $pdf->importPage(1);
                    $pdf->useImportedPage($pdfDocumentPage, 5, 10, 200);
                } elseif (in_array($document['fileType'], ['png', 'jpeg', 'jpg'])) {
                    $pdf->setY(15);
                    $pdf->Image('@'.$document['content'], 10, 10, 180);
                }
            }
        }

        return $pdf;
    }
}
