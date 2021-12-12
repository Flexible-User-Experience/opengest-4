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
        $withBackground = true;
        if ($withBackground) {
            // set background image
            $img_file = $this->pdfEngineService->getSmartAssetsHelper()->getAbsoluteAssetFilePath('/build/img/exempleFactura.pdf');
            $pdf->Image($img_file, 0, 0, 148, 210, '', '', '', false, 300, '', false, false, 0);
            // restore auto-page-break status
            $pdf->SetAutoPageBreak($auto_page_break, $bMargin);
        }
        // set the starting point for the page content
        $pdf->setPageMark();
        // set cell padding
        $pdf->setCellPaddings(1, 1, 1, 1);

        //Heading with sending adress
        $pdf->setXY(30, 30);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT,
            $saleInvoice->getPartner()->getName(),
            0, 0, 'L', false);
        $pdf->Ln();
        $pdf->setX(30);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT,
            $saleInvoice->getPartner()->getMainAddress(),
            0, 0, 'L', false);
        $pdf->Ln();
        $pdf->setX(30);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT,
            $saleInvoice->getPartner()->getMainCityName(),
            0, 0, 'L', false);

        //Heading with date, invoice number, etc.
        $xVar = 130;
        $xVar2 = 170;
        $yVarStart = 20;
        $incrY = 15;
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
            '?',
            0, 0, 'L', false);

        //deliveryNoteInfo
        $YDim = 80;
        /** @var SaleDeliveryNote $deliveryNote */
        foreach ($saleInvoice->getDeliveryNotes() as $deliveryNote) {
            $col1 = 30;
            $col2 = 40;
            $col3 = 120;
            $col4 = 130;
            $col5 = 145;
            $col6 = 150;
            $col7 = 160;

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
                    $deliveryNoteLine->getDiscount(),
                    0, 0, 'L', false);
                $pdf->Cell($col7 - $col6, ConstantsEnum::PDF_CELL_HEIGHT,
                    $deliveryNoteLine->getTotal(),
                    0, 0, 'L', false);
                $pdf->Ln();
            }
            $YDim = $pdf->GetY() + 5;
        }

        //Footer
        //Datos fiscales
        $xVar = 30;
        $yVarStart = 260;
        $cellWidth = 50;
        $this->pdfEngineService->setStyleSize('', 8);
        $pdf->setXY($xVar, $yVarStart);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            $saleInvoice->getPartner()->getName(),
            0, 0, 'L', false, '', 1);
        $pdf->Ln();
        $pdf->setX($xVar);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            $saleInvoice->getPartner()->getMainAddress(),
            0, 0, 'L', false, '', 1);
        $pdf->Ln();
        $pdf->setX($xVar);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            $saleInvoice->getPartner()->getMainCity().' '.$saleInvoice->getPartner()->getMainCityName(),
            0, 0, 'L', false, '', 1);
        $pdf->Ln();
        $pdf->setX($xVar);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            'provincia',
            0, 0, 'L', false, '', 1);
        $pdf->Ln();
        $pdf->setX($xVar);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            'pais',
            0, 0, 'L', false);

        //Forma de pago
        $xVar2 = 100;
        $pdf->setXY($xVar2, $yVarStart);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            'forma pago',
            0, 0, 'L', false);
        $pdf->Ln();
        $pdf->setX($xVar2);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            $saleInvoice->getPartner()->getIban(),
            0, 0, 'L', false);
        $pdf->Ln();
        $pdf->setX($xVar2);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            $saleInvoice->getPartner()->getSwift(),
            0, 0, 'L', false);
        $pdf->Ln();
        $pdf->setX($xVar2);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            'fecha vencimiento + importe',
            0, 0, 'L', false);

        //Final amount
        $xVar3 = 160;
        $pdf->setXY($xVar3, $yVarStart);
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
        $pdf->setXY(40, 285);
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
