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
 * Class DocumentationPdfManager.
 *
 * @category Manager
 */
class DocumentationPdfManager
{
    public function buildSingle(Collection $operators, $documents, $enterpriseDocumentation): TCPDF
    {
        $pdf = new Fpdi();

        return $this->buildOneDocument($operators, $documents, $enterpriseDocumentation, $pdf);
    }

    public function outputSingle(Collection $operators, $documents, $enterpriseDocumentation): string
    {
        $pdf = $this->buildSingle($operators, $documents, $enterpriseDocumentation);

        return $pdf->Output('Documentacion operario/s.pdf', 'I');
    }

    /**
     * @throws CrossReferenceException
     * @throws PdfReaderException
     * @throws PdfParserException
     * @throws PdfTypeException
     * @throws FilterException
     */
    private function buildOneDocument(Collection $operators, $documents, $enterpriseDocumentation, Fpdi $pdf): TCPDF
    {
        $pdf->setMargins(ConstantsEnum::PDF_PAGE_A4_MARGIN_LEFT, ConstantsEnum::PDF_PAGE_A4_MARGIN_TOP, ConstantsEnum::PDF_PAGE_A4_MARGIN_RIGHT, true);
        $today = date('d/m/Y');
        if (count($documents)) {
            /** @var Operator $operator */
            foreach ($operators as $operator) {
                foreach ($documents[$operator->getId()] as $document) {
                    $this->generateNewPdfPage($pdf, $today, $document);
                }
            }
        }
        if (count($enterpriseDocumentation)) {
            foreach ($enterpriseDocumentation as $enterpriseDocument) {
                $this->generateNewPdfPage($pdf, $today, $enterpriseDocument);
            }
        }

        return $pdf;
    }

    /**
     * @param $today
     * @param $document
     *
     * @throws CrossReferenceException
     * @throws FilterException
     * @throws PdfParserException
     * @throws PdfReaderException
     * @throws PdfTypeException
     */
    protected function generateNewPdfPage(Fpdi $pdf, $today, $document): void
    {
        if ('pdf' === $document['fileType']) {
            $pageCount = $pdf->setSourceFile(StreamReader::createByString($document['content']));
            for ($pageNumber = 1; $pageNumber <= $pageCount; $pageNumber++) {
                $this->addNewPageAndSetHeaders($pdf, $today, $document['nameTranslated']);
                $pdfDocumentPage = $pdf->importPage($pageNumber);
                $pdf->useImportedPage($pdfDocumentPage, 5, 10, 200);
            }
        } elseif (in_array($document['fileType'], ['png', 'jpeg', 'jpg'])) {
            $this->addNewPageAndSetHeaders($pdf, $today, $document['nameTranslated']);
            $pdf->setY(15);
            $pdf->Image('@'.$document['content'], 10, 10, 180);
        }
    }

    /**
     * @param Fpdi $pdf
     * @param $today
     * @param $nameTranslated
     *
     * @return void
     */
    private function addNewPageAndSetHeaders(Fpdi $pdf, $today, $nameTranslated): void
    {
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
            'Fecha generación: ' . $today . '      ' . $pdf->getAliasNumPage() . '/' . $pdf->getAliasNbPages(),
            0, 0, 'R', false);
        $pdf->setY(5);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT,
            'Documento: ' . $nameTranslated,
            0, 0, 'L', false);
    }
}
