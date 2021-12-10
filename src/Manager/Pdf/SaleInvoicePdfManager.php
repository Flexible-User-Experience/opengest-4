<?php

namespace App\Manager\Pdf;

use App\Entity\Sale\SaleInvoice;
use App\Enum\ConstantsEnum;
use App\Service\PdfEngineService;
use Doctrine\Common\Collections\ArrayCollection;
use TCPDF;

/**
 * Class SaleInvoicePdfManager.
 *
 * @category Manager
 */
class SaleInvoicePdfManager
{
    private PdfEngineService $pdfEngineService;

    /**
     * Methods.
     */
    public function __construct(PdfEngineService $pdfEngineService)
    {
        $this->pdfEngineService = $pdfEngineService;
    }

    /**
     * @return TCPDF
     */
    public function buildSingle(SaleInvoice $saleInvoice)
    {
        $this->pdfEngineService->initDefaultPageEngineWithTitle('Factura '.$saleInvoice);
        $pdf = $this->pdfEngineService->getEngine();

        return $this->buildOneSaleRequestPerPage($saleInvoice, $pdf);
    }

    /**
     * @return string
     */
    public function outputSingle(SaleInvoice $saleInvoice)
    {
        $pdf = $this->buildSingle($saleInvoice);

        return $pdf->Output('factura'.$saleInvoice->getInvoiceNumber().'.pdf', 'I');
    }

    /**
     * @param SaleInvoice[]|ArrayCollection|array $saleInvoices
     *
     * @return TCPDF
     */
    public function buildCollection($saleInvoices)
    {
        $this->pdfEngineService->initDefaultPageEngineWithTitle('Grupo de facturas');
        $pdf = $this->pdfEngineService->getEngine();
        /** @var SaleInvoice $saleInvoice */
        foreach ($saleInvoices as $saleInvoice) {
            $pdf = $this->buildOneSaleInvoicePerPage($saleInvoice, $pdf);
        }

        return $pdf;
    }

    /**
     * @param SaleInvoice[]|ArrayCollection|array $saleInvoices
     *
     * @return string
     */
    public function outputCollection($saleInvoices)
    {
        $pdf = $this->buildCollection($saleInvoices);

        return $pdf->Output('grupo_facturas.pdf', 'I');
    }

    /**
     * @return TCPDF
     */
    private function buildOneSaleInvoicePerPage(SaleInvoice $saleInvoice, TCPDF $pdf)
    {
        // add start page
        // TODO make invoice print
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

        //Heading with date, invoice number, etc.
        $pdf->setXY(100, 20);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT,
            $saleInvoice->getFromDateFormatted(),
            0, 0, 'L', false);
        $pdf->setXY(100, 20);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT,
            $saleInvoice->getFromDateFormatted(),
            0, 0, 'L', false);
        $pdf->Ln();

        //page number
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT,
            $pdf->getAliasNumPage().'/'.$pdf->getAliasNbPages(),
            0, 0, 'R', false);

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
