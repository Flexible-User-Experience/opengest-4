<?php

namespace App\Manager\Pdf;

use App\Entity\Sale\SaleDeliveryNote;
use App\Entity\Sale\SaleDeliveryNoteLine;
use App\Entity\Sale\SaleInvoice;
use App\Entity\Sale\SaleInvoiceDueDate;
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
    public function buildSingle($saleInvoices, $from, $to)
    {
        $this->pdfEngineService->initDefaultPageEngineWithTitle('Facturas');
        $pdf = $this->pdfEngineService->getEngine();

        return $this->buildInvoiceList($saleInvoices, $from, $to, $pdf);
    }

    /**
     * @return string
     */
    public function outputSingle($saleInvoices, $from, $to)
    {
        $pdf = $this->buildSingle($saleInvoices, $from, $to);

        return $pdf->Output('facturas'.'.pdf', 'I');
    }

    /**
     * @param $saleInvoices
     */
    public function buildInvoiceList($saleInvoices, $from, $to, TCPDF $pdf): TCPDF
    {
        $partnersFromSaleInvoices = [];
        /* @var  SaleInvoice $saleInvoice */
        foreach ($saleInvoices as $allSaleInvoice) {
            $partnersFromSaleInvoices[$allSaleInvoice->getPartner()->getId()] = $allSaleInvoice->getPartner();
        }
        foreach ($partnersFromSaleInvoices as $partner) {
            $width = $this->addStartPage($pdf);
            list($colWidth1, $colWidth2, $colWidth3) = $this->printHeaders($pdf, $partner, $from, $to, $width);
            $totalBases = 0;
            $totalTotal = 0;
            $filteredSaleInvoicesByPartner = array_filter($saleInvoices, function ($x) use ($partner) {
                return $x->getPartner() == $partner;
            }, ARRAY_FILTER_USE_BOTH);

            /** @var SaleInvoice $saleInvoice */
            foreach ($filteredSaleInvoicesByPartner as $saleInvoice) {
                if ($pdf->getY() > 180) {
                    $this->addStartPage($pdf);
                    list($colWidth1, $colWidth2, $colWidth3) = $this->printHeaders($pdf, $partner, $from, $to, $width);
                }
                $totalBases = $saleInvoice->getBaseTotal() + $totalBases;
                $totalTotal = $saleInvoice->getTotal() + $totalTotal;
                $pdf->SetX(ConstantsEnum::PDF_PAGE_A4_MARGIN_LEFT);
                $pdf->Cell($colWidth1, ConstantsEnum::PDF_CELL_HEIGHT,
                    $saleInvoice->getInvoiceNumber(),
                    1, 0, 'C', false);
                $pdf->Cell($colWidth1, ConstantsEnum::PDF_CELL_HEIGHT,
                    $saleInvoice->getDateFormatted(),
                    1, 0, 'C', false);
                $pdf->Cell($colWidth2, ConstantsEnum::PDF_CELL_HEIGHT,
                    $saleInvoice->getDeliveryNotes()->first() ? ($saleInvoice->getDeliveryNotes()->first()->getBuildingSite() ? $saleInvoice->getDeliveryNotes()->first()->getBuildingSite() : '') : '',
                    1, 0, 'L', false, '', 1);
                $pdf->Cell($colWidth3, ConstantsEnum::PDF_CELL_HEIGHT,
                    $saleInvoice->getDeliveryNotes()->first() ? ($saleInvoice->getDeliveryNotes()->first()->getOrder() ? $saleInvoice->getDeliveryNotes()->first()->getOrder() : '') : '',
                    1, 0, 'L', false, '', 1);
                $pdf->Cell($colWidth1, ConstantsEnum::PDF_CELL_HEIGHT,
                    number_format($saleInvoice->getBaseTotal(), 2, ',', '.'),
                    1, 0, 'C', false);
                $pdf->Cell($colWidth1, ConstantsEnum::PDF_CELL_HEIGHT,
                    number_format($saleInvoice->getTotal(), 2, ',', '.').'€',
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
                0, 0, 'L', false, '', 1);
            $pdf->Cell($colWidth3, ConstantsEnum::PDF_CELL_HEIGHT,
                '',
                0, 0, 'L', false, '', 1);
            $pdf->Cell($colWidth1, ConstantsEnum::PDF_CELL_HEIGHT,
                number_format($totalBases, 2, ',', '.'),
                1, 0, 'C', false);
            $pdf->Cell($colWidth1, ConstantsEnum::PDF_CELL_HEIGHT,
                number_format($totalTotal, 2, ',', '.').'€',
                1, 0, 'C', false);
        }

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
        $pdf->startPageGroup();
        $this->setNewPage($pdf, $withBackground);
        $this->setHeading($pdf, $saleInvoice, $withBackground);


        //deliveryNoteInfo
        $hasIva0 = false;
        if($withBackground){
            $YDim = 110;
            $col1 = 32;
            $col2 = 46;
            $col3 = 122;
            $col4 = 140;
            $col5 = 160;
            $col6 = 168;
            $col7 = 194;
        } else {
            $YDim = 110-5;
            $col1 = 32-12;
            $col2 = 46-6;
            $col3 = 122+2;
            $col4 = 140+3;
            $col5 = 160+3;
            $col6 = 168+7;
            $col7 = 194+8;
        }
        $pdf->setXY($col2, $YDim);
        if ($saleInvoice->getDeliveryNotes()->first()) {
            if ($saleInvoice->getDeliveryNotes()->first()->getBuildingSite()) {
                $pdf->SetFont(ConstantsEnum::PDF_DEFAULT_FONT, 'b', 9);
                $pdf->MultiCell($col3 - $col2, ConstantsEnum::PDF_CELL_HEIGHT,
                    'OBRA: '.$saleInvoice->getDeliveryNotes()->first()->getBuildingSite(),
                    0, 'L', false, 1);
                $pdf->SetFont(ConstantsEnum::PDF_DEFAULT_FONT, '', 9);
            }
        }
        /** @var SaleDeliveryNote $deliveryNote */
        foreach ($saleInvoice->getDeliveryNotes() as $deliveryNote) {
            if ($pdf->GetY() > 220) {
                $this->setParcialFooter($pdf, $saleInvoice, $withBackground);
                $this->setNewPage($pdf, $withBackground);
                $this->setHeading($pdf, $saleInvoice, $withBackground);
                $YDim = 110;
                $pdf->setXY($col1, $YDim);
            }
            $pdf->setX($col1);
            $pdf->Cell($col2 - $col1, ConstantsEnum::PDF_CELL_HEIGHT,
                $deliveryNote->getId(),
            0, 0, 'L', false, 0);
            $pdf->MultiCell($col3 - $col2, ConstantsEnum::PDF_CELL_HEIGHT,
                $deliveryNote->getDateToString(),
                0, 'L', false, 0);
            $pdf->Ln(4);
            if ($deliveryNote->getDeliveryNoteReference()) {
                $pdf->setX($col2);
                $pdf->MultiCell($col3 - $col2, ConstantsEnum::PDF_CELL_HEIGHT,
                    'Referencia: '.$deliveryNote->getDeliveryNoteReference(),
                    0, 'L', false, 0);
                $pdf->Ln(4);
            }
            if ($deliveryNote->getServiceDescription()) {
                $pdf->SetX($col2);
                $pdf->MultiCell($col3 - $col2 + 5,ConstantsEnum::PDF_CELL_HEIGHT,
                    $deliveryNote->getServiceDescription(),
                    0, 'L', false, 1);
                $pdf->Ln(-2);
            }

            /** @var SaleDeliveryNoteLine $deliveryNoteLine */
            foreach ($deliveryNote->getSaleDeliveryNoteLines() as $deliveryNoteLine) {
                if (0 == $deliveryNoteLine->getIva()) {
                    $hasIva0 = true;
                }
                if ($deliveryNoteLine->getDescription()) {
                    $pdf->SetX($col2);
                    $pdf->MultiCell($col3 - $col2 + 5, ConstantsEnum::PDF_CELL_HEIGHT,
                        $deliveryNoteLine->getDescription(),
                        0, 'L', false);
                    $pdf->Ln(-2);
                }
                $pdf->SetX($col2 + 5);
                $pdf->MultiCell($col3 - $col2, ConstantsEnum::PDF_CELL_HEIGHT,
                    substr($deliveryNoteLine->getSaleItem(), strpos($deliveryNoteLine->getSaleItem(), '-') + 1)/*.($deliveryNoteLine->getDescription() ? ': '.$deliveryNoteLine->getDescription() : '')*/ ,
                0, 'L', false, 0);
                if (0 == $deliveryNoteLine->getUnits()) {
                    $pdf->MultiCell($col4 - $col3, ConstantsEnum::PDF_CELL_HEIGHT,
                        '',
                        0, 'C', false, 0);
                } else {
                    $pdf->MultiCell($col4 - $col3, ConstantsEnum::PDF_CELL_HEIGHT,
                        $deliveryNoteLine->getUnits(),
                        0, 'C', false, 0);
                }
                if (0 == $deliveryNoteLine->getPriceUnit()) {
                    $pdf->MultiCell($col5 - $col4, ConstantsEnum::PDF_CELL_HEIGHT,
                        '',
                        0, 'C', false, 0);
                } else {
                    $pdf->MultiCell($col5 - $col4, ConstantsEnum::PDF_CELL_HEIGHT,
                        number_format($deliveryNoteLine->getPriceUnit(), 2, ',', '.'),
                        0, 'C', false, 0);
                }
                $pdf->setCellPaddings(0, 1, 0, 1);
                if ($deliveryNoteLine->getDiscount()) {
                    $pdf->MultiCell($col6 - $col5 + 3, ConstantsEnum::PDF_CELL_HEIGHT,
                        $deliveryNoteLine->getDiscount().'%',
                        0, 'C', false, 0);
                } else {
                    $pdf->MultiCell($col6 - $col5, ConstantsEnum::PDF_CELL_HEIGHT,
                        '', 0, 'C', false, 0);
                }
                $pdf->setCellPaddings(1, 1, 1, 1);
                if (0 == $deliveryNoteLine->getTotal()) {
                    $pdf->MultiCell($col7 - $col6, ConstantsEnum::PDF_CELL_HEIGHT,
                        '',
                        0, 'C', false, 0);
                } else {
                    $pdf->MultiCell($col7 - $col6, ConstantsEnum::PDF_CELL_HEIGHT,
                        number_format($deliveryNoteLine->getTotal(), 2, ',', '.').' €',
                        0, 'C', false, 0);
                }
                $pdf->Ln(4);
            }
            // TODO include invoice discount to invoice
            $pdf->Ln(2);
        }

        $YDim = $pdf->GetY() + 2;

        if ($pdf->GetY() > 220) {
            $this->setParcialFooter($pdf, $saleInvoice, $withBackground);
            $this->setNewPage($pdf, $withBackground);
            $this->setHeading($pdf, $saleInvoice, $withBackground);
            $YDim = 110;
            $pdf->setXY($col1, $YDim);
        }
        $pdf->setXY($col1, $YDim);
        if ($saleInvoice->getObservations()) {
            $pdf->setXY($col2, $YDim);
            $pdf->MultiCell($col3 - $col2 + 6, ConstantsEnum::PDF_CELL_HEIGHT,
                $saleInvoice->getObservations(),
                0, 'L', false);
        }
        $this->writeDataTreatmentText($pdf);

        $this->setFooter($pdf, $saleInvoice, $hasIva0, $withBackground);

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
     * @param $withBackground
     */
    private function setNewPage(TCPDF $pdf, $withBackground): void
    {
        $pdf->AddPage(ConstantsEnum::PDF_PORTRAIT_PAGE_ORIENTATION, ConstantsEnum::PDF_PAGE_A4);
        $pdf->SetFont(ConstantsEnum::PDF_DEFAULT_FONT, '', 8.5);

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
    }

    private function setFooter(TCPDF $pdf, SaleInvoice $saleInvoice, $hasIva0, $withBackground): void
    {
        //Footer
        //Datos fiscales
        $pdf->SetFont(ConstantsEnum::PDF_DEFAULT_FONT, '', 8.5);
        if($withBackground) {
            $xVar = 26;
            $yVarStart = 249;
        } else {
            $xVar = 26-5;
            $yVarStart = 249+8;
        }
        $cellWidth = 60;
        $pdf->setXY($xVar, $yVarStart);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            $saleInvoice->getPartnerName(),
            0, 0, 'L', false, '', 1);
        $pdf->Ln(4);
        $pdf->setX($xVar);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            $saleInvoice->getPartnerMainAddress(),
            0, 0, 'L', false, '', 1);
        $pdf->Ln(4);
        $pdf->setX($xVar);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            $saleInvoice->getPartnerMainCity(),
            0, 0, 'L', false, '', 1);
        $pdf->Ln(4);
        $pdf->setX($xVar);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            $saleInvoice->getPartnerMainCity() ? $saleInvoice->getPartnerMainCity()->getProvince() : '',
            0, 0, 'L', false, '', 1);
        $pdf->Ln(4);
        $pdf->setX($xVar);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT,
            $saleInvoice->getPartnerMainCity() ? $saleInvoice->getPartnerMainCity()->getProvince()->getCountryName() : '',
            0, 0, 'L', false);

        //Forma de pago
        if($withBackground){
            $xVar2 = 90;
        } else {
            $xVar2 = 90+3;
        }
        $pdf->setXY($xVar2, $yVarStart);
        if ($saleInvoice->getDeliveryNotes()->first()) {
            $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
                ($saleInvoice->getCollectionDocumentType() ? $saleInvoice->getCollectionDocumentType()->getName() : '').
                ($saleInvoice->getDeliveryNotes()->first()->getCollectionTerm() ? ' a '.$saleInvoice->getDeliveryNotes()->first()->getCollectionTerm().(
                    $saleInvoice->getDeliveryNotes()->first()->getCollectionTerm2() ? '+'.$saleInvoice->getDeliveryNotes()->first()->getCollectionTerm2().(
                        $saleInvoice->getDeliveryNotes()->first()->getCollectionTerm3() ? '+'.$saleInvoice->getDeliveryNotes()->first()->getCollectionTerm3() : ''
                        ) : ''
                    ).' días' : ''),
                0, 0, 'L', false);
        } else {
            $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
                ($saleInvoice->getCollectionDocumentType() ? $saleInvoice->getCollectionDocumentType()->getName() : ''),
                0, 0, 'L', false);
        }
        if ($saleInvoice->getCollectionDocumentType()) {
            if (str_contains(strtolower($saleInvoice->getCollectionDocumentType()->getName()), 'transferencia')) {
                $pdf->Ln(5);
                $pdf->setX($xVar2);
                $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
                    $saleInvoice->getPartner()->getTransferAccount()->getIban().' '.
                    $saleInvoice->getPartner()->getTransferAccount()->getBankCode().' '.
                    $saleInvoice->getPartner()->getTransferAccount()->getOfficeNumber().' '.
                    $saleInvoice->getPartner()->getTransferAccount()->getControlDigit().' '.
                    $saleInvoice->getPartner()->getTransferAccount()->getAccountNumber(),
                    0, 0, 'L', false);
                $pdf->Ln(3);
                $pdf->setX($xVar2);
                $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
                    $saleInvoice->getPartner()->getTransferAccount()->getSwift(),
                    0, 0, 'L', false);
            } elseif (str_contains(strtolower($saleInvoice->getCollectionDocumentType()->getName()), 'recibo')) {
                $pdf->Ln(5);
                $pdf->setX($xVar2);
                $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
                    'IBAN: '.substr($saleInvoice->getPartnerIban(), 0, 4).' '.substr($saleInvoice->getPartnerIban(), 4, 4).
                    ' '.substr($saleInvoice->getPartnerIban(), 8, 4).' '.substr($saleInvoice->getPartnerIban(), 12, 4).' '.
                    substr($saleInvoice->getPartnerIban(), 16, 4).' '.substr($saleInvoice->getPartnerIban(), 20, 4),
                    0, 0, 'L', false);
                $pdf->Ln(3);
                $pdf->setX($xVar2);
                $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
                    'SWIFT: '.$saleInvoice->getPartnerSwift(),
                    0, 0, 'L', false);
            }
        }
        $pdf->Ln(3);
        $pdf->setX($xVar2);
        /** @var SaleInvoiceDueDate $dueDate */
        foreach ($saleInvoice->getSaleInvoiceDueDates() as $dueDate) {
            if ($dueDate) {
                $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
                    'Vencimiento: '.$dueDate->getDate()->format('d/m/y').' IMPORTE: '.number_format($dueDate->getAmount(), 2, ',', '.').'€',
                    0, 0, 'L', false);
                $pdf->Ln(3);
                $pdf->setX($xVar2);
            }
        }

        //Final amount
        if($withBackground){
            $xVar3 = 156;
        } else {
            $xVar3 = 156+3;
        }
        $cellWidth = 38;
        $pdf->setXY($xVar3, $yVarStart - 3);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            'Base imponible: '.number_format($saleInvoice->getBaseTotal(), 2, ',', '.').' €',
            0, 0, 'R', false);
        $pdf->Ln(4);
        if ($hasIva0) {
            $pdf->setX($xVar3);
            $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
                'IVA 0%: '.number_format($saleInvoice->getIva0(), 2, ',', '.').' €',
                0, 0, 'R', false);
            $pdf->Ln(4);
        }
        if ($saleInvoice->getIva4()) {
            $pdf->setX($xVar3);
            $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
                'IVA 4%: '.number_format($saleInvoice->getIva4(), 2, ',', '.').' €',
                0, 0, 'R', false);
            $pdf->Ln(4);
        }
        if ($saleInvoice->getIva10()) {
            $pdf->setX($xVar3);
            $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
                'IVA 10%: '.number_format($saleInvoice->getIva10(), 2, ',', '.').' €',
                0, 0, 'R', false);
            $pdf->Ln(4);
        }
        if ($saleInvoice->getIva21()) {
            $pdf->setX($xVar3);
            $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
                'IVA 21%: '.number_format($saleInvoice->getIva21(), 2, ',', '.').' €',
                0, 0, 'R', false);
            $pdf->Ln(4);
        }
        if ($saleInvoice->getIrpf()) {
            $pdf->setX($xVar3);
            $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
                'IRPF: '.number_format($saleInvoice->getIrpf(), 2, ',', '.').' €',
                0, 0, 'R', false);
            $pdf->Ln(4);
        }
        $pdf->Ln(1);
        $this->pdfEngineService->setStyleSize('b', 10);
        $pdf->setX($xVar3);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            'TOTAL: '.number_format($saleInvoice->getTotal(), 2, ',', '.').' €',
            0, 0, 'R', false);

        //page number
        $this->pdfEngineService->setStyleSize('', 9);
        if($withBackground){
            $pdf->setXY(40, 275);
        } else {
            $pdf->setXY(40, 275+10);
        }
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT,
            $pdf->getPageNumGroupAlias().' de '.$pdf->getPageGroupAlias(),
            0, 0, 'C', false);
        $pdf->Ln();
    }

    private function setParcialFooter(TCPDF $pdf, SaleInvoice $saleInvoice, $withBackground): void
    {
        //Footer
        //Datos fiscales
        $pdf->SetFont(ConstantsEnum::PDF_DEFAULT_FONT, '', 8.5);
        if($withBackground){
            $xVar = 26;
            $yVarStart = 249;
        } else {
            $xVar = 26-5;
            $yVarStart = 249+8;
        }
        $cellWidth = 60;
        $this->pdfEngineService->setStyleSize('', 8);
        $pdf->setXY($xVar, $yVarStart);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            $saleInvoice->getPartnerName(),
            0, 0, 'L', false, '', 1);
        $pdf->Ln(4);
        $pdf->setX($xVar);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            $saleInvoice->getPartnerMainAddress(),
            0, 0, 'L', false, '', 1);
        $pdf->Ln(4);
        $pdf->setX($xVar);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            $saleInvoice->getPartnerMainCity(),
            0, 0, 'L', false, '', 1);
        $pdf->Ln(4);
        $pdf->setX($xVar);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            $saleInvoice->getPartnerMainCity() ? $saleInvoice->getPartnerMainCity()->getProvince() : '',
            0, 0, 'L', false, '', 1);
        $pdf->Ln(4);
        $pdf->setX($xVar);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT,
            $saleInvoice->getPartnerMainCity() ? $saleInvoice->getPartnerMainCity()->getProvince()->getCountryName() : '',
            0, 0, 'L', false);
        //Final amount
        $pdf->SetFont(ConstantsEnum::PDF_DEFAULT_FONT, '', 10);
        if($withBackground){
            $xVar3 = 156;
        } else {
            $xVar3 = 156+3;
        }
        $pdf->setXY($xVar3, $yVarStart + 10);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            'Suma y sigue',
            0, 0, 'L', false);

        //page number
        $this->pdfEngineService->setStyleSize('', 9);
        if($withBackground){
            $pdf->setXY(40, 275);
        } else {
            $pdf->setXY(40, 275+10);
        }
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT,
            $pdf->getPageNumGroupAlias().' de '.$pdf->getPageGroupAlias(),
            0, 0, 'C', false);
        $pdf->Ln();
    }

    private function writeDataTreatmentText(TCPDF $pdf): void
    {
        $pdf->SetFont(ConstantsEnum::PDF_DEFAULT_FONT, 'I', 7);
        $pdf->SetAbsX(26-8);
        $pdf->MultiCell(185, ConstantsEnum::PDF_CELL_HEIGHT,
            'GRÚAS ROMANÍ, S.A. es el Responsable de Tratamiento de sus datos de acuerdo a lo dispuesto en el RGPD y la LOPDGDD y los tratan con la finalidad de mantener una relación comercial con usted. Los datos se conservarán mientras se mantenga dicha relación y una vez acabada, durante 4,5,6 y 10 años debidamente bloqueados en cumplimiento de la normativa de aplicación. Así mismo, le informamos que tiene derecho a solicitar el acceso, rectificación, portabilidad y supresión de sus datos y la limitación y oposición a su tratamiento dirigiéndose a CTRA. SANTA BARBARA KM. 1,5 AMPOSTA (TARRAGONA) o enviando un correo electrónico a info@gruasromani.com, junto con una fotocopia de su DNI o documento análogo en derecho, indicando el tipo de derecho que quiere ejercer. Para cualquier reclamación puede acudir ante la AEPD desde el sitio web www.aepd.es.', 0, 'C', false);
    }

    private function setHeading(TCPDF $pdf, SaleInvoice $saleInvoice, $withBackground): void
    {
        //Heading with sending address
        if($withBackground){
            $xDim = 32;
            $pdf->setXY($xDim, 55);
        } else {
            $xDim = 32-8;
            $pdf->setXY($xDim, 55-8);
        }
        $this->pdfEngineService->setStyleSize('b', 11);
        $pdf->Cell(85, ConstantsEnum::PDF_CELL_HEIGHT,
            $saleInvoice->getPartnerName(),
            0, 0, 'L', false, '', 1);
        if($saleInvoice->getPartner()->getReference()){
            $pdf->Ln(5);
            $pdf->setX($xDim);
            $pdf->Cell(85, ConstantsEnum::PDF_CELL_HEIGHT,
                $saleInvoice->getPartner()->getReference(),
                0, 0, 'L', false, '', 1);

        }
        $pdf->Ln(8);
        if ($saleInvoice->getDeliveryAddress()) {
            if (' ' !== $saleInvoice->getDeliveryAddress()->getAddress()) {
                $pdf->setX($xDim);
                $pdf->Cell(85, ConstantsEnum::PDF_CELL_HEIGHT,
                    $saleInvoice->getDeliveryAddress()->getAddress(),
                    0, 0, 'L', false, '', 1);
                $pdf->Ln(5);
                $pdf->setX($xDim);
                $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT,
                    $saleInvoice->getDeliveryAddress()->getCity(),
                    0, 0, 'L', false);
                $pdf->Ln(5);
                $pdf->setX($xDim);
                $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT,
                    $saleInvoice->getDeliveryAddress()->getCity()->getProvince(),
                    0, 0, 'L', false);
                $pdf->Ln(5);
                $pdf->setX($xDim);
                $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT,
                    $saleInvoice->getDeliveryAddress()->getCity()->getProvince()->getCountryName(),
                    0, 0, 'L', false);
            } else {
                $this->printAddressFromPartner($pdf, $xDim, $saleInvoice);
            }
        } else {
            $this->printAddressFromPartner($pdf, $xDim, $saleInvoice);
        }
        $this->pdfEngineService->setStyleSize('', 9);

        //Heading with date, invoice number, etc.
        if($withBackground){
            $xVar = 125;
            $xVar2 = 163;
            $yVarStart = 54;
            $incrY = 15;
            $cellwidth = 33;
        } else {
            $xVar = 125;
            $xVar2 = 163+3;
            $yVarStart = 54-8;
            $incrY = 15;
            $cellwidth = 33;
        }
        $pdf->setXY($xVar, $yVarStart);
        $pdf->Cell($cellwidth, ConstantsEnum::PDF_CELL_HEIGHT,
            $saleInvoice->getDateFormatted(),
            0, 0, 'C', false);
        if (1 === $saleInvoice->getSeries()->getId()) {
            $pdf->setXY($xVar2, $yVarStart);
            $pdf->Cell($cellwidth, ConstantsEnum::PDF_CELL_HEIGHT,
                $saleInvoice->getInvoiceNumber(),
                0, 0, 'C', false);
        } else {
            $pdf->setXY($xVar2 - 5, $yVarStart);
            $pdf->Cell($cellwidth, ConstantsEnum::PDF_CELL_HEIGHT,
                $saleInvoice->getSeries()->getPrefix().$saleInvoice->getInvoiceNumber(),
                0, 0, 'C', false);
        }
        $pdf->setXY($xVar, $yVarStart + $incrY);
        $pdf->Cell($cellwidth, ConstantsEnum::PDF_CELL_HEIGHT,
            $saleInvoice->getPartner()->getCode(),
            0, 0, 'C', false);
        $pdf->setXY($xVar2, $yVarStart + $incrY);
        $pdf->Cell($cellwidth, ConstantsEnum::PDF_CELL_HEIGHT,
            $saleInvoice->getPartnerCifNif(),
            0, 0, 'C', false);
        $pdf->setXY($xVar, $yVarStart + $incrY*2 );
        $pdf->MultiCell($cellwidth, ConstantsEnum::PDF_CELL_HEIGHT * 2,
            $saleInvoice->getPartner()->getProviderReference(),
            0, 'C', false);
        $pdf->setXY($xVar2 - 2, $yVarStart + $incrY*2 );
        if ($saleInvoice->getDeliveryNotes()->first()) {
            $pdf->MultiCell($cellwidth + 1, ConstantsEnum::PDF_CELL_HEIGHT * 2,
                $saleInvoice->getDeliveryNotes()->first()->getOrder(),
                0, 'C', false);
        }
    }

    private function printAddressFromPartner(TCPDF $pdf, int $xDim, SaleInvoice $saleInvoice): void
    {
        $pdf->setX($xDim);
        $pdf->Cell(85, ConstantsEnum::PDF_CELL_HEIGHT,
            $saleInvoice->getPartnerMainAddress(),
            0, 0, 'L', false, '', 1);
        $pdf->Ln(5);
        $pdf->setX($xDim);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT,
            $saleInvoice->getPartnerMainCity(),
            0, 0, 'L', false);
        $pdf->Ln(5);
        $pdf->setX($xDim);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT,
            $saleInvoice->getPartnerMainCity() ? $saleInvoice->getPartnerMainCity()->getProvince() : '',
            0, 0, 'L', false);
        $pdf->Ln(5);
        $pdf->setX($xDim);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT,
            $saleInvoice->getPartnerMainCity() ? $saleInvoice->getPartnerMainCity()->getProvince()->getCountryName() : '',
            0, 0, 'L', false);
    }

    private function addStartPage(TCPDF $pdf): int
    {
        // add start page
        $pdf->AddPage(ConstantsEnum::PDF_LANDSCAPE_PAGE_ORIENTATION, ConstantsEnum::PDF_PAGE_A4);
        $pdf->SetFont(ConstantsEnum::PDF_DEFAULT_FONT, '', 9);
        $width = ConstantsEnum::PDF_PAGE_A4_WIDTH_LANDSCAPE;
        // logo
        $pdf->Image($this->pdfEngineService->getSmartAssetsHelper()->getAbsoluteAssetFilePath('/build/img/logo_empresa.png'), ConstantsEnum::PDF_PAGE_A4_MARGIN_LEFT, 5, 30); // TODO replace by enterprise image if defined

        // today date
        $this->pdfEngineService->setStyleSize('', 18);
        $pdf->SetXY(50, 20);
        $today = date('d/m/Y');
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT,
            $today,
            0, 0, 'L', false);

        return $width;
    }

    /**
     * @param $partner
     * @param $from
     * @param $to
     *
     * @return int[]
     */
    private function printHeaders(TCPDF $pdf, $partner, $from, $to, int $width): array
    {
        // header
        $this->pdfEngineService->setStyleSize('', 12);
        $pdf->SetXY(50, 30);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT,
            'Listado de facturas del cliente '.$partner->getCode().'-'.$partner->getName(),
            0, 0, 'L', false);
        $pdf->SetXY(50, 35);
        $this->pdfEngineService->setStyleSize('', 11);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT,
            'Desde '.$from.' hasta '.$to,
            0, 0, 'L', false);
        $pdf->SetXY(50, 43);
        $this->drawHoritzontalLineSeparator($pdf, $width);
        //table headers
        $this->pdfEngineService->setStyleSize('', 8);
        $pdf->SetXY(ConstantsEnum::PDF_PAGE_A4_MARGIN_LEFT, 50);
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

        return [$colWidth1, $colWidth2, $colWidth3];
    }
}
