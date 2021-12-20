<?php

namespace App\Manager\Pdf;

use App\Entity\Sale\SaleDeliveryNote;
use App\Entity\Sale\SaleDeliveryNoteLine;
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
    public function buildSingle($saleInvoices)
    {
        $this->pdfEngineService->initDefaultPageEngineWithTitle('Facturas');
        $pdf = $this->pdfEngineService->getEngine();

        return $this->buildInvoiceList($saleInvoices, $pdf);
    }

    /**
     * @return string
     */
    public function outputSingle($saleInvoices)
    {
        $pdf = $this->buildSingle($saleInvoices);

        return $pdf->Output('facturas'.'.pdf', 'I');
    }

    /**
     * @param $saleInvoices
     * @param TCPDF $pdf
     * @return TCPDF
     */
    public function buildInvoiceList($saleInvoices, TCPDF $pdf): TCPDF
    {
        // add start page
        $pdf->AddPage(ConstantsEnum::PDF_LANDSCAPE_PAGE_ORIENTATION, ConstantsEnum::PDF_PAGE_A4);
        $pdf->SetFont(ConstantsEnum::PDF_DEFAULT_FONT, '', 9);
        $width = ConstantsEnum::PDF_PAGE_A4_WIDTH_LANDSCAPE;
        // logo
        $pdf->Image($this->pdfEngineService->getSmartAssetsHelper()->getAbsoluteAssetFilePath('/build/img/logo_empresa.png'), ConstantsEnum::PDF_PAGE_A4_MARGIN_LEFT, 5, 30); // TODO replace by enterprise image if defined

        // today date
        $this->pdfEngineService->setStyleSize('', 18);
        $pdf->SetXY(40,20);
        $today = date('d/m/Y');
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT,
            $today,
            0, 0, 'L', false);
        // header
        $this->pdfEngineService->setStyleSize('', 12);
        $pdf->SetXY(40,40);
        //TODO add from to date
        /** @var SaleInvoice $oneSaleInvoice */
        $oneSaleInvoice =$saleInvoices[0];
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT,
            'Listado de facturas del cliente '.$oneSaleInvoice->getPartner(),
            0, 0, 'L', false);
        $pdf->SetXY(40,45);
        $this->drawHoritzontalLineSeparator($pdf,$width);
        //table headers
        $this->pdfEngineService->setStyleSize('', 8);
        $pdf->SetXY(ConstantsEnum::PDF_PAGE_A4_MARGIN_LEFT,50);
        $colWidth1 = 32;
        $colWidth2 = 82;
        $colWidth3 = 48;
        $pdf->Cell($colWidth1, ConstantsEnum::PDF_CELL_HEIGHT,
            'Nº factura',
            1, 0, 'C', true);
        $pdf->Cell($colWidth1, ConstantsEnum::PDF_CELL_HEIGHT,
            'Fecha',
            1, 0, 'C', true);
        $pdf->Cell($colWidth2, ConstantsEnum::PDF_CELL_HEIGHT,
            'Obra',
            1, 0, 'C', true);
        $pdf->Cell($colWidth3, ConstantsEnum::PDF_CELL_HEIGHT,
            'Pedido',
            1, 0, 'C', true);
        $pdf->Cell($colWidth1, ConstantsEnum::PDF_CELL_HEIGHT,
            'Base Imponible',
            1, 0, 'C', true);
        $pdf->Cell($colWidth1, ConstantsEnum::PDF_CELL_HEIGHT,
            'TOTAL',
            1, 0, 'C', true);
        $pdf->Ln();
        $totalBases = 0;
        $totalTotal = 0;
        /** @var SaleInvoice $saleInvoice */
        foreach($saleInvoices as $saleInvoice){
            $totalBases = $saleInvoice->getBaseTotal() + $totalBases;
            $totalTotal = $saleInvoice->getTotal() + $totalTotal;
            $pdf->SetX(ConstantsEnum::PDF_PAGE_A4_MARGIN_LEFT);
            $pdf->Cell($colWidth1, ConstantsEnum::PDF_CELL_HEIGHT,
                $saleInvoice->getInvoiceNumber(),
                1, 0, 'C', false);
            $pdf->Cell($colWidth1, ConstantsEnum::PDF_CELL_HEIGHT,
                $saleInvoice->getDateFormatted(),
                1, 0, 'C', false);
            //TODO add obra and pedido to the pdf
            $pdf->Cell($colWidth2, ConstantsEnum::PDF_CELL_HEIGHT,
                'obra',
                1, 0, 'L', false,'',1);
            $pdf->Cell($colWidth3, ConstantsEnum::PDF_CELL_HEIGHT,
                'pedido',
                1, 0, 'L', false,'',1);
            $pdf->Cell($colWidth1, ConstantsEnum::PDF_CELL_HEIGHT,
                number_format($saleInvoice->getBaseTotal(),2,',','.'),
                1, 0, 'C', false);
            $pdf->Cell($colWidth1, ConstantsEnum::PDF_CELL_HEIGHT,
                number_format($saleInvoice->getTotal(),2,',','.').'€',
                1, 0, 'C', false);
            $pdf->Ln();
        }
        $pdf->SetX(ConstantsEnum::PDF_PAGE_A4_MARGIN_LEFT);
        $pdf->Cell($colWidth1, ConstantsEnum::PDF_CELL_HEIGHT,
            '',
            0, 0, 'C', false);
        $pdf->Cell($colWidth1, ConstantsEnum::PDF_CELL_HEIGHT,
            '',
            0, 0, 'C', false);
        $pdf->Cell($colWidth2, ConstantsEnum::PDF_CELL_HEIGHT,
            '',
            0, 0, 'L', false,'',1);
        $pdf->Cell($colWidth3, ConstantsEnum::PDF_CELL_HEIGHT,
            '',
            0, 0, 'L', false,'',1);
        $pdf->Cell($colWidth1, ConstantsEnum::PDF_CELL_HEIGHT,
            number_format($totalBases,2,',','.'),
            1, 0, 'C', false);
        $pdf->Cell($colWidth1, ConstantsEnum::PDF_CELL_HEIGHT,
            number_format($totalTotal,2,',','.').'€',
            1, 0, 'C', false);

        return $pdf;
    }

    /**
     * @param SaleInvoice[]|ArrayCollection|array $saleInvoices
     *
     * @return TCPDF
     */
    public function buildCollection($saleInvoices, $withBackground)
    {
        $this->pdfEngineService->initDefaultPageEngineWithTitle('Grupo de facturas');
        $pdf = $this->pdfEngineService->getEngine();
        /** @var SaleInvoice $saleInvoice */
        foreach ($saleInvoices as $saleInvoice) {
            $pdf = $this->buildOneSaleInvoicePerPage($saleInvoice, $withBackground, $pdf);
        }

        return $pdf;
    }

    /**
     * @param SaleInvoice[]|ArrayCollection|array $saleInvoices
     *
     * @return string
     */
    public function outputCollectionEmail($saleInvoices)
    {
        $pdf = $this->buildCollection($saleInvoices, true);

        return $pdf->Output('grupo_facturas.pdf', 'I');
    }

    /**
     * @param SaleInvoice[]|ArrayCollection|array $saleInvoices
     *
     * @return string
     */
    public function outputCollectionPrint($saleInvoices)
    {
        $pdf = $this->buildCollection($saleInvoices, false);

        return $pdf->Output('grupo_facturas.pdf', 'I');
    }

    /**
     * @return TCPDF
     */
    private function buildOneSaleInvoicePerPage(SaleInvoice $saleInvoice, $withBackground, TCPDF $pdf)
    {
        // add start page
        // TODO make invoice print
        $pdf->AddPage(ConstantsEnum::PDF_PORTRAIT_PAGE_ORIENTATION, ConstantsEnum::PDF_PAGE_A4);
        $pdf->SetFont(ConstantsEnum::PDF_DEFAULT_FONT, '', 9);

        $width = ConstantsEnum::PDF_PAGE_A4_WIDTH_PORTRAIT - ConstantsEnum::PDF_PAGE_A4_MARGIN_RIGHT - ConstantsEnum::PDF_PAGE_A4_MARGIN_LEFT;

        if ($withBackground) {
            // -- set new background ---

            // get the current page break margin
            $bMargin = $pdf->getBreakMargin();
            // get current auto-page-break mode
            $auto_page_break = $pdf->getAutoPageBreak();
            // disable auto-page-break
            $pdf->SetAutoPageBreak(false, 0);
            // set background image
            $img_file = $this->pdfEngineService->getSmartAssetsHelper()->getAbsoluteAssetFilePath('/build/img/Invoice_template.png');
            $pdf->Image($img_file, 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0);
            // restore auto-page-break status
            $pdf->SetAutoPageBreak($auto_page_break, $bMargin);
            // set the starting point for the page content
            $pdf->setPageMark();
        }

        // set cell padding
        $pdf->setCellPaddings(1, 1, 1, 1);

        //Heading with sending address
        $xDim = 20;
        $pdf->setXY($xDim, 55);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT,
            $saleInvoice->getPartner()->getName(),
            0, 0, 'L', false);
        $pdf->Ln();
        if(!$saleInvoice->getDeliveryAddress()){
            $pdf->setX($xDim);
            $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT,
                $saleInvoice->getPartner()->getMainAddress(),
                0, 0, 'L', false);
            $pdf->Ln();
            $pdf->setX($xDim);
            $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT,
                $saleInvoice->getPartner()->getMainCity(),
                0, 0, 'L', false);
            $pdf->Ln();
            $pdf->setX($xDim);
            $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT,
                $saleInvoice->getPartner()->getMainCity()->getProvince(),
                0, 0, 'L', false);
        } else {
            $pdf->setX($xDim);
            $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT,
                $saleInvoice->getDeliveryAddress()->getAddress(),
                0, 0, 'L', false);
            $pdf->Ln();
            $pdf->setX($xDim);
            $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT,
                $saleInvoice->getDeliveryAddress()->getCity(),
                0, 0, 'L', false);
            $pdf->Ln();
            $pdf->setX($xDim);
            $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT,
                $saleInvoice->getDeliveryAddress()->getCity()->getProvince(),
                0, 0, 'L', false);
        }


        //Heading with date, invoice number, etc.
        $xVar = 130;
        $xVar2 = 170;
        $yVarStart = 49;
        $incrY = 16;
        $pdf->setXY($xVar, $yVarStart);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT,
            $saleInvoice->getDateFormatted(),
            0, 0, 'L', false);
        $pdf->setXY($xVar2, $yVarStart);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT,
            $saleInvoice->getInvoiceNumber(),
            0, 0, 'L', false);
        $pdf->setXY($xVar, $yVarStart + $incrY);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT,
            $saleInvoice->getPartner()->getCode(),
            0, 0, 'L', false);
        $pdf->setXY($xVar2, $yVarStart + $incrY);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT,
            $saleInvoice->getPartner()->getCifNif(),
            0, 0, 'L', false);
        $pdf->setXY($xVar, $yVarStart + $incrY * 2);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT,
            $saleInvoice->getPartner()->getProviderReference(),
            0, 0, 'L', false);
        $pdf->setXY($xVar2, $yVarStart + $incrY * 2);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT,
            $saleInvoice->getDeliveryNotes()->first()->getOrder(),
            0, 0, 'L', false);

        //deliveryNoteInfo
        $YDim = 110;
        /** @var SaleDeliveryNote $deliveryNote */
        foreach ($saleInvoice->getDeliveryNotes() as $deliveryNote) {
            $col1 = 28;
            $col2 = 43;
            $col3 = 124;
            $col4 = 143;
            $col5 = 160;
            $col6 = 177;
            $col7 = 195;

            $pdf->setXY($col1, $YDim);
            $pdf->Cell($col2 - $col1, ConstantsEnum::PDF_CELL_HEIGHT,
                $deliveryNote->getId(),
                0, 0, 'L', false);
            $pdf->Cell($col3 - $col2, ConstantsEnum::PDF_CELL_HEIGHT,
                $deliveryNote->getServiceDescription(),
                0, 0, 'L', false);
            $pdf->Ln();
            /** @var SaleDeliveryNoteLine $deliveryNoteLine */
            foreach ($deliveryNote->getSaleDeliveryNoteLines() as $deliveryNoteLine) {
                $pdf->SetAbsX($col2 + 10);
                $pdf->Cell($col3 - $col2, ConstantsEnum::PDF_CELL_HEIGHT,
                    $deliveryNoteLine->getSaleItem(),
                0, 0, 'L', false);
                $pdf->Cell($col4 - $col3, ConstantsEnum::PDF_CELL_HEIGHT,
                    $deliveryNoteLine->getUnits(),
                    0, 0, 'L', false);
                $pdf->Cell($col5 - $col4, ConstantsEnum::PDF_CELL_HEIGHT,
                    $deliveryNoteLine->getPriceUnit(),
                    0, 0, 'L', false);
                $pdf->Cell($col6 - $col5, ConstantsEnum::PDF_CELL_HEIGHT,
                    $deliveryNoteLine->getDiscount().' %',
                    0, 0, 'L', false);
                $pdf->Cell($col7 - $col6, ConstantsEnum::PDF_CELL_HEIGHT,
                    $deliveryNoteLine->getTotal().' €',
                    0, 0, 'L', false);
                $pdf->Ln();
            }
            $YDim = $pdf->GetY() + 5;
        }

        //Footer
        //Datos fiscales
        $xVar = 23;
        $yVarStart = 255;
        $cellWidth = 60;
        $this->pdfEngineService->setStyleSize('', 8);
        $pdf->setXY($xVar, $yVarStart);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            $saleInvoice->getPartner()->getName(),
            0, 0, 'L', false, '', 1);
        $pdf->Ln(5);
        $pdf->setX($xVar);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            $saleInvoice->getPartner()->getMainAddress(),
            0, 0, 'L', false, '', 1);
        $pdf->Ln(5);
        $pdf->setX($xVar);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            $saleInvoice->getPartner()->getMainCity().' '.$saleInvoice->getPartner()->getMainCityName(),
            0, 0, 'L', false, '', 1);
        $pdf->Ln(5);
        $pdf->setX($xVar);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            $saleInvoice->getPartner()->getMainCity()->getProvince(),
            0, 0, 'L', false, '', 1);


        //Forma de pago
        $xVar2 = 91;
        $pdf->setXY($xVar2, $yVarStart);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            $saleInvoice->getPartner()->getCollectionDocumentType() ? $saleInvoice->getPartner()->getCollectionDocumentType()->getName() : '',
            0, 0, 'L', false);
        $pdf->Ln(5);
        $pdf->setX($xVar2);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            $saleInvoice->getPartner()->getIban(),
            0, 0, 'L', false);
        $pdf->Ln(5);
        $pdf->setX($xVar2);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            $saleInvoice->getPartner()->getSwift(),
            0, 0, 'L', false);
        $pdf->Ln(5);
        $pdf->setX($xVar2);
        foreach($saleInvoice->getSaleInvoiceDueDates() as $dueDate){
            $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
                $dueDate.' €',
                0, 0, 'L', false);
            $pdf->Ln(5);
        }


        //Final amount
        $xVar3 = 160;
        $pdf->setXY($xVar3, $yVarStart - 3);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            'Base imponible: '.$saleInvoice->getBaseTotal().'€',
            0, 0, 'L', false);
        $pdf->Ln();
        $pdf->setX($xVar3);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            'IVA 21%: '.$saleInvoice->getIva().'€',
            0, 0, 'L', false);
        $pdf->Ln();
        if ($saleInvoice->getIrpf()) {
            $pdf->setX($xVar3);
            $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
                'IRPF 15%: '.$saleInvoice->getIrpf().'€',
                0, 0, 'L', false);
            $pdf->Ln();
        }
        $this->pdfEngineService->setStyleSize('b', 10);
        $pdf->setX($xVar3);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            'TOTAL: '.$saleInvoice->getTotal().'€',
            0, 0, 'L', false);

        //page number
        $this->pdfEngineService->setStyleSize('', 9);
        $pdf->setXY(40, 281);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT,
            $pdf->getAliasNumPage().' de '.$pdf->getAliasNbPages(),
            0, 0, 'C', false);
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
