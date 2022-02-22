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
        $this->setNewPage($pdf, $withBackground);
        $this->setHeading($pdf, $saleInvoice);

        //deliveryNoteInfo
        $YDim = 110;
        $col1 = 32;
        $col2 = 46;
        $col3 = 122;
        $col4 = 140;
        $col5 = 160;
        $col6 = 168;
        $col7 = 194;
        /** @var SaleDeliveryNote $deliveryNote */
        foreach ($saleInvoice->getDeliveryNotes() as $deliveryNote) {
            $pdf->setXY($col1, $YDim);
            $pdf->Cell($col2 - $col1, ConstantsEnum::PDF_CELL_HEIGHT,
                $deliveryNote->getId(),
            0, 0, 'L', false);
            if($deliveryNote->getDeliveryNoteReference()){
                $pdf->MultiCell($col3 - $col2, ConstantsEnum::PDF_CELL_HEIGHT,
                $deliveryNote->getDateToString().' - Referencia: '.$deliveryNote->getDeliveryNoteReference(),
                0,  'L', false);
            } else {
                $pdf->MultiCell($col3 - $col2, ConstantsEnum::PDF_CELL_HEIGHT,
                    $deliveryNote->getDateToString(),
                    0, 'L', false);
            }
            $pdf->SetAbsX($col2);
            $pdf->MultiCell($col3 - $col2,ConstantsEnum::PDF_CELL_HEIGHT,
            $deliveryNote->getServiceDescription(),
                0, 'L', false);

            /** @var SaleDeliveryNoteLine $deliveryNoteLine */
            foreach ($deliveryNote->getSaleDeliveryNoteLines() as $deliveryNoteLine) {
                if($pdf->GetY() > 210) {
                    $this->setParcialFooter($pdf, $saleInvoice);
                    $this->setNewPage($pdf, $withBackground);
                    $this->setHeading($pdf, $saleInvoice);
                    $YDim = 110;
                    $pdf->setXY($col1, $YDim);
                }
                $pdf->SetAbsX($col2 + 5);
                $pdf->setCellPaddings(1, 1, 5, 1);
                $pdf->MultiCell($col3 - $col2, ConstantsEnum::PDF_CELL_HEIGHT,
                    substr($deliveryNoteLine->getSaleItem(),strpos($deliveryNoteLine->getSaleItem(),'-')+1).($deliveryNoteLine->getDescription() ? ': '.$deliveryNoteLine->getDescription() : ''),
                0, 'L', false,0);
                $pdf->setCellPaddings(1, 1, 1, 1);
                $pdf->MultiCell($col4 - $col3, ConstantsEnum::PDF_CELL_HEIGHT,
                    $deliveryNoteLine->getUnits(),
                    0,  'C', false,0);
                $pdf->MultiCell($col5 - $col4, ConstantsEnum::PDF_CELL_HEIGHT,
                    number_format($deliveryNoteLine->getPriceUnit(),2,',','.').' €/u',
                    0,  'C', false,0);
                if($deliveryNote->getDiscount()){
                    $pdf->MultiCell($col6 - $col5, ConstantsEnum::PDF_CELL_HEIGHT,
                        $deliveryNoteLine->getDiscount().' %',
                        0,  'C', false,0);
                } else {
                    $pdf->MultiCell($col6 - $col5, ConstantsEnum::PDF_CELL_HEIGHT,
                        '',
                        0,  'C', false,0);
                }
                $pdf->MultiCell($col7 - $col6, ConstantsEnum::PDF_CELL_HEIGHT,
                    number_format($deliveryNoteLine->getTotal(),2,',','.').' €',
                    0,  'C', false);
                $pdf->Ln(3);
            }
            $YDim = $pdf->GetY() + 5;
        }
        $this->writeDataTreatmentText($pdf);

        $this->setFooter($pdf, $saleInvoice);

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
     * @param $withBackground
     * @return void
     */
    private function setNewPage(TCPDF $pdf, $withBackground): void
    {
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
    }

    /**
     * @param TCPDF $pdf
     * @param SaleInvoice $saleInvoice
     * @return void
     */
    private function setFooter(TCPDF $pdf, SaleInvoice $saleInvoice): void
    {
//Footer
        //Datos fiscales
        $pdf->SetFont(ConstantsEnum::PDF_DEFAULT_FONT, '', 9);

        $xVar = 26;
        $yVarStart = 249;
        $cellWidth = 60;
        $this->pdfEngineService->setStyleSize('', 8);
        $pdf->setXY($xVar, $yVarStart);
        $this->pdfEngineService->setStyleSize('', 8);
        $pdf->setXY($xVar, $yVarStart);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            $saleInvoice->getPartner()->getName(),
            0, 0, 'L', false, '', 1);
        $pdf->Ln(4);
        $pdf->setX($xVar);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            $saleInvoice->getPartner()->getMainAddress(),
            0, 0, 'L', false, '', 1);
        $pdf->Ln(4);
        $pdf->setX($xVar);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            $saleInvoice->getPartner()->getMainCity(),
            0, 0, 'L', false, '', 1);
        $pdf->Ln(4);
        $pdf->setX($xVar);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            $saleInvoice->getPartner()->getMainCity()->getProvince(),
            0, 0, 'L', false, '', 1);
        $pdf->Ln(4);
        $pdf->setX($xVar);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT,
            $saleInvoice->getPartner()->getMainCity()->getProvince()->getCountryName(),
            0, 0, 'L', false);


        //Forma de pago
        $xVar2 = 91;
        $pdf->setXY($xVar2, $yVarStart);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            ($saleInvoice->getCollectionDocumentType() ? $saleInvoice->getCollectionDocumentType()->getName() : '') .
            ($saleInvoice->getPartner()->getCollectionTerm1() ? ' a ' . $saleInvoice->getPartner()->getCollectionTerm1() . (
                $saleInvoice->getPartner()->getCollectionTerm2() ? '+' . $saleInvoice->getPartner()->getCollectionTerm2() . (
                    $saleInvoice->getPartner()->getCollectionTerm3() ? '+' . $saleInvoice->getPartner()->getCollectionTerm3() : ''
                    ) : ''
                ) . ' días' : ''),
            0, 0, 'L', false);
        if($saleInvoice->getCollectionDocumentType()){
            if(str_contains(strtolower($saleInvoice->getCollectionDocumentType()->getName()),'transferencia')){
                $pdf->Ln(5);
                $pdf->setX($xVar2);
                $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
                    $saleInvoice->getPartner()->getTransferAccount()->getName(),
                    0, 0, 'L', false);
                $pdf->Ln(3);
                $pdf->setX($xVar2);
                $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
                    $saleInvoice->getPartner()->getTransferAccount()->getIban().' '.
                    $saleInvoice->getPartner()->getTransferAccount()->getBankCode().' '.
                    $saleInvoice->getPartner()->getTransferAccount()->getOfficeNumber().' '.
                    $saleInvoice->getPartner()->getTransferAccount()->getControlDigit().' '.
                    $saleInvoice->getPartner()->getTransferAccount()->getAccountNumber(),
                    0, 0, 'L', false);
            } elseif (str_contains(strtolower($saleInvoice->getCollectionDocumentType()->getName()),'recibo')) {
                $pdf->Ln(5);
                $pdf->setX($xVar2);
                $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
                    'IBAN: '.$saleInvoice->getPartner()->getIban(),
                    0, 0, 'L', false);
                $pdf->Ln(3);
                $pdf->setX($xVar2);
                $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
                    'SWIFT: '.$saleInvoice->getPartner()->getSwift(),
                    0, 0, 'L', false);
            }
        }
        $pdf->Ln(3);
        $pdf->setX($xVar2);
        foreach ($saleInvoice->getSaleInvoiceDueDates() as $dueDate) {
            if ($dueDate) {
                $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
                    $dueDate . ' €',
                    0, 0, 'L', false);
                $pdf->Ln(3);
                $pdf->setX($xVar2);
            }
        }

        //Final amount
        $xVar3 = 156;
        $pdf->setXY($xVar3, $yVarStart - 3);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            'Base imponible: ' . number_format($saleInvoice->getBaseTotal(), 2, ',', '.') . ' €',
            0, 0, 'L', false);
        $pdf->Ln();
        $pdf->setX($xVar3);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            'IVA 21%: ' . number_format($saleInvoice->getIva(), 2, ',', '.') . ' €',
            0, 0, 'L', false);
        $pdf->Ln();
        if ($saleInvoice->getIrpf()) {
            $pdf->setX($xVar3);
            $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
                'IRPF 15%: ' . number_format($saleInvoice->getIrpf(), 2, ',', '.') . ' €',
                0, 0, 'L', false);
            $pdf->Ln();
        }
        $this->pdfEngineService->setStyleSize('b', 10);
        $pdf->setX($xVar3);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            'TOTAL: ' . number_format($saleInvoice->getTotal(), 2, ',', '.') . ' €',
            0, 0, 'L', false);

        //page number
        $this->pdfEngineService->setStyleSize('', 9);
        $pdf->setXY(40, 275);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT,
            $pdf->getAliasNumPage() . ' de ' . $pdf->getAliasNbPages(),
            0, 0, 'C', false);
        $pdf->Ln();
    }
    /**
     * @param TCPDF $pdf
     * @param SaleInvoice $saleInvoice
     * @return void
     */
    private function setParcialFooter(TCPDF $pdf, SaleInvoice $saleInvoice): void
    {
//Footer
        //Datos fiscales
        $pdf->SetFont(ConstantsEnum::PDF_DEFAULT_FONT, '', 9);
        $xVar = 26;
        $yVarStart = 249;
        $cellWidth = 60;
        $this->pdfEngineService->setStyleSize('', 8);
        $pdf->setXY($xVar, $yVarStart);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            $saleInvoice->getPartner()->getName(),
            0, 0, 'L', false, '', 1);
        $pdf->Ln(4);
        $pdf->setX($xVar);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            $saleInvoice->getPartner()->getMainAddress(),
            0, 0, 'L', false, '', 1);
        $pdf->Ln(4);
        $pdf->setX($xVar);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            $saleInvoice->getPartner()->getMainCity(),
            0, 0, 'L', false, '', 1);
        $pdf->Ln(4);
        $pdf->setX($xVar);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            $saleInvoice->getPartner()->getMainCity()->getProvince(),
            0, 0, 'L', false, '', 1);
        $pdf->Ln(4);
        $pdf->setX($xVar);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT,
            $saleInvoice->getPartner()->getMainCity()->getProvince()->getCountryName(),
            0, 0, 'L', false);
        //Final amount
        $pdf->SetFont(ConstantsEnum::PDF_DEFAULT_FONT, '', 10);
        $xVar3 = 156;
        $pdf->setXY($xVar3, $yVarStart+10);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            'Suma y sigue',
            0, 0, 'L', false);


        //page number
        $this->pdfEngineService->setStyleSize('', 9);
        $pdf->setXY(40, 275);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT,
            $pdf->getAliasNumPage() . ' de ' . $pdf->getAliasNbPages(),
            0, 0, 'C', false);
        $pdf->Ln();
    }

    /**
     * @param TCPDF $pdf
     * @return void
     */
    private function writeDataTreatmentText(TCPDF $pdf): void
    {
        $pdf->SetFont(ConstantsEnum::PDF_DEFAULT_FONT, 'I', 8);
        $pdf->SetAbsX(20);
        $pdf->MultiCell(180, ConstantsEnum::PDF_CELL_HEIGHT,
            'GRÚAS ROMANÍ, S.A. es el Responsable de Tratamiento de sus datos de acuerdo a lo dispuesto en el RGPD y la LOPDGDD y los tratan con la finalidad de mantener una relación comercial con usted. Los datos se conservarán mientras se mantenga dicha relación y una vez acabada, durante 4,5,6 y 10 años debidamente bloqueados en cumplimiento de la normativa de aplicación. Así mismo, le informamos que tiene derecho a solicitar el acceso, rectificación, portabilidad y supresión de sus datos y la limitación y oposición a su tratamiento dirigiéndose a CTRA. SANTA BARBARA KM. 1,5 AMPOSTA (TARRAGONA) o enviando un correo electrónico a info@gruasromani.com, junto con una fotocopia de su DNI o documento análogo en derecho, indicando el tipo de derecho que quiere ejercer. Para cualquier reclamación puede acudir ante la AEPD desde el sitio web www.aepd.es.'
            , 0, 'C', false);
    }

    /**
     * @param TCPDF $pdf
     * @param SaleInvoice $saleInvoice
     * @return void
     */
    private function setHeading(TCPDF $pdf, SaleInvoice $saleInvoice): void
    {
//Heading with sending address
        $xDim = 32;
        $pdf->setXY($xDim, 55);
        $this->pdfEngineService->setStyleSize('b', 10);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT,
            $saleInvoice->getPartner()->getName(),
            0, 0, 'L', false);
        $pdf->Ln(8);
        if ($saleInvoice->getDeliveryAddress()) {
            if ($saleInvoice->getDeliveryAddress()->getAddress()) {
                $pdf->setX($xDim);
                $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT,
                    $saleInvoice->getDeliveryAddress()->getAddress(),
                    0, 0, 'L', false);
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
        $xVar = 130;
        $xVar2 = 170;
        $yVarStart = 54;
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
    }

    /**
     * @param TCPDF $pdf
     * @param int $xDim
     * @param SaleInvoice $saleInvoice
     * @return void
     */
    private function printAddressFromPartner(TCPDF $pdf, int $xDim, SaleInvoice $saleInvoice): void
    {
        $pdf->setX($xDim);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT,
            $saleInvoice->getPartner()->getMainAddress(),
            0, 0, 'L', false);
        $pdf->Ln(5);
        $pdf->setX($xDim);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT,
            $saleInvoice->getPartner()->getMainCity(),
            0, 0, 'L', false);
        $pdf->Ln(5);
        $pdf->setX($xDim);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT,
            $saleInvoice->getPartner()->getMainCity()->getProvince(),
            0, 0, 'L', false);
        $pdf->Ln(5);
        $pdf->setX($xDim);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT,
            $saleInvoice->getPartner()->getMainCity()->getProvince()->getCountryName(),
            0, 0, 'L', false);
    }
}
