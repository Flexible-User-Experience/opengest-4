<?php

namespace App\Manager\Pdf;

use App\Entity\Sale\SaleDeliveryNote;
use App\Entity\Sale\SaleDeliveryNoteLine;
use App\Enum\ConstantsEnum;
use App\Service\PdfEngineService;
use Doctrine\Common\Collections\ArrayCollection;
use TCPDF;

/**
 * Class SaleDeliveryNotePdfManager.
 *
 * @category Manager
 */
class SaleDeliveryNotePdfManager
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
    public function buildSingle(SaleDeliveryNote $saleDeliveryNote)
    {
        $this->pdfEngineService->initDefaultPageEngineWithTitle('Albarán '.$saleDeliveryNote);
        $pdf = $this->pdfEngineService->getEngine();

        return $this->buildOneSaleRequestPerPage($saleDeliveryNote, $pdf);
    }

    /**
     * @return string
     */
    public function outputSingle(SaleDeliveryNote $saleDeliveryNote)
    {
        $pdf = $this->buildSingle($saleDeliveryNote);

        return $pdf->Output('albaran'.$saleDeliveryNote->getId().'.pdf', 'I');
    }

    /**
     * @param $saleDeliveryNotes
     * @return TCPDF
     */
    public function buildDeliveryNotesByClient($saleDeliveryNotes): TCPDF
    {
        $this->pdfEngineService->initDefaultPageEngineWithTitle('Albaranes por cliente');
        $pdf = $this->pdfEngineService->getEngine();

        return $this->buildListByClient($saleDeliveryNotes, $pdf);
    }

    /**
     * @return string
     */
    public function outputDeliveryNotesByClient($saleDeliveryNotes): string
    {
        $pdf = $this->buildDeliveryNotesByClient($saleDeliveryNotes);

        return $pdf->Output('albaranesPorCliente'.'.pdf', 'I');
    }

    /**
     * @param $saleDeliveryNotes
     * @param TCPDF $pdf
     * @return TCPDF
     */
    public function buildListByClient($saleDeliveryNotes, TCPDF $pdf): TCPDF
    {
        // add start page
        $pdf->AddPage(ConstantsEnum::PDF_LANDSCAPE_PAGE_ORIENTATION, ConstantsEnum::PDF_PAGE_A4);
        $pdf->SetFont(ConstantsEnum::PDF_DEFAULT_FONT, '', 9);
        $width = ConstantsEnum::PDF_PAGE_A4_WIDTH_LANDSCAPE;

        // logo
        $pdf->Image($this->pdfEngineService->getSmartAssetsHelper()->getAbsoluteAssetFilePath('/build/img/logo_empresa.png'), ConstantsEnum::PDF_PAGE_A4_MARGIN_LEFT, 5, 30); // TODO replace by enterprise image if defined

        // today date
        $this->pdfEngineService->setStyleSize('', 18);
        $pdf->SetXY(50,20);
        $today = date('d/m/Y');
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT,
            $today,
            0, 0, 'L', false);

        // header
        //TODO add partner and from to info
        $this->pdfEngineService->setStyleSize('', 12);
        /** @var SaleDeliveryNote $lastSaleDeliveryNote */
        $lastSaleDeliveryNote = $saleDeliveryNotes[count($saleDeliveryNotes) - 1];
        /** @var SaleDeliveryNote $firstDeliveryNote */
        $firstDeliveryNote = $saleDeliveryNotes[0];
        $pdf->SetXY(50,30);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT,
            'Listado de albaranes del cliente '.$lastSaleDeliveryNote->getPartner()->getName().
            ' desde '.$lastSaleDeliveryNote->getDateToString().' hasta '.$firstDeliveryNote->getDateToString(),
            0, 0, 'L', false);
        $pdf->SetXY(40,45);
        //table headers
        $this->pdfEngineService->setStyleSize('', 8);
        $pdf->SetXY(ConstantsEnum::PDF_PAGE_A4_MARGIN_LEFT,50);
        $colWidth1 = 22;
        $colWidth2 = 52;
        $colWidth3 = 42;
        $pdf->Cell($colWidth1, ConstantsEnum::PDF_CELL_HEIGHT,
            'Nº albarán',
            1, 0, 'C', true);
        $pdf->Cell($colWidth1, ConstantsEnum::PDF_CELL_HEIGHT,
            'Fecha',
            1, 0, 'C', true);
        $pdf->Cell($colWidth1, ConstantsEnum::PDF_CELL_HEIGHT,
            'Nº factura',
            1, 0, 'C', true);
        $pdf->Cell($colWidth2, ConstantsEnum::PDF_CELL_HEIGHT,
            'Obra',
            1, 0, 'C', true);
        $pdf->Cell($colWidth3, ConstantsEnum::PDF_CELL_HEIGHT,
            'Pedido',
            1, 0, 'C', true);
        $pdf->Cell($colWidth1, ConstantsEnum::PDF_CELL_HEIGHT,
            'Horas',
            1, 0, 'C', true);
        $pdf->Cell($colWidth1, ConstantsEnum::PDF_CELL_HEIGHT,
            'Precio/Hora',
            1, 0, 'C', true);
        $pdf->Cell($colWidth1, ConstantsEnum::PDF_CELL_HEIGHT,
            'Base Imponible',
            1, 0, 'C', true);
        $pdf->Cell($colWidth1, ConstantsEnum::PDF_CELL_HEIGHT,
            'TOTAL',
            1, 0, 'C', true);
        $pdf->Ln();
        /** @var SaleDeliveryNote $saleDeliveryNote */
        foreach($saleDeliveryNotes as $saleDeliveryNote){
            $totalHours = 0;
            /** @var SaleDeliveryNoteLine $saleDeliveryNoteLine */
            foreach($saleDeliveryNote->getSaleDeliveryNoteLines() as $saleDeliveryNoteLine){
                if($saleDeliveryNoteLine->getSaleItem()->getId() == '1'
                    || $saleDeliveryNoteLine->getSaleItem()->getId() == '2'
                    || $saleDeliveryNoteLine->getSaleItem()->getId() == '3'){
                    $totalHours = $totalHours + $saleDeliveryNoteLine->getUnits();
                }
            }
            $pdf->SetX(ConstantsEnum::PDF_PAGE_A4_MARGIN_LEFT);
            $pdf->Cell($colWidth1, ConstantsEnum::PDF_CELL_HEIGHT,
            $saleDeliveryNote->getId(),
            1, 0, 'C', false);
            $pdf->Cell($colWidth1, ConstantsEnum::PDF_CELL_HEIGHT,
            $saleDeliveryNote->getDateToString(),
            1, 0, 'C', false);
            if(!$saleDeliveryNote->getSaleInvoice()){
                $pdf->Cell($colWidth1, ConstantsEnum::PDF_CELL_HEIGHT,
                    '',
                    1, 0, 'C', false);
            } else {
                $pdf->Cell($colWidth1, ConstantsEnum::PDF_CELL_HEIGHT,
                    $saleDeliveryNote->getSaleInvoice()->getInvoiceNumber(),
                    1, 0, 'C', false);
            }
            $pdf->Cell($colWidth2, ConstantsEnum::PDF_CELL_HEIGHT,
            $saleDeliveryNote->getBuildingSite(),
            1, 0, 'L', false,'',1);
            $pdf->Cell($colWidth3, ConstantsEnum::PDF_CELL_HEIGHT,
            $saleDeliveryNote->getOrder(),
            1, 0, 'L', false,'',1);
            $pdf->Cell($colWidth1, ConstantsEnum::PDF_CELL_HEIGHT,
            $totalHours,
            1, 0, 'C', false);
            if($totalHours > 0){
                $pdf->Cell($colWidth1, ConstantsEnum::PDF_CELL_HEIGHT,
                    number_format($saleDeliveryNote->getBaseAmount()/$totalHours,2,',','.').'€',
                    1, 0, 'C', false);
            } else {
                $pdf->Cell($colWidth1, ConstantsEnum::PDF_CELL_HEIGHT,
                    '0',
                    1, 0, 'C', false);
            }
            $pdf->Cell($colWidth1, ConstantsEnum::PDF_CELL_HEIGHT,
            number_format($saleDeliveryNote->getBaseAmount(),2,',','.').'€',
            1, 0, 'C', false);
            $pdf->Cell($colWidth1, ConstantsEnum::PDF_CELL_HEIGHT,
            number_format($saleDeliveryNote->getFinalTotal(),2,',','.').'€',
            1, 0, 'C', false);
            $pdf->Ln();
        }


        return $pdf;
    }

    /**
     * @param $saleDeliveryNotes
     * @return TCPDF
     */
    public function buildDeliveryNotesList($saleDeliveryNotes): TCPDF
    {
        $this->pdfEngineService->initDefaultPageEngineWithTitle('Albaranes');
        $pdf = $this->pdfEngineService->getEngine();

        return $this->buildList($saleDeliveryNotes, $pdf);
    }

    /**
     * @param $saleDeliveryNotes
     * @return string
     */
    public function outputDeliveryNotesList($saleDeliveryNotes): string
    {
        $pdf = $this->buildDeliveryNotesList($saleDeliveryNotes);

        return $pdf->Output('albaranes'.'.pdf', 'I');
    }

    /**
     * @param $saleDeliveryNotes
     * @param TCPDF $pdf
     * @return TCPDF
     */
    public function buildList($saleDeliveryNotes, TCPDF $pdf): TCPDF
    {
        // add start page
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
        $this->pdfEngineService->setStyleSize('', 10);
        $today = date('d/m/Y');
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT,
            'Listado de servicios - Grúas Romaní - '.$today,
            1, 0, 'L', true);

        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT,
            $pdf->getAliasNumPage().'/'.$pdf->getAliasNbPages(),
            0, 0, 'R', true);
        $pdf->Ln(10);

                //Start table
        $this->pdfEngineService->setStyleSize('b', 10);
        $col1 = 50;
        $col2 = 60;
        $col3 = 55;
        $col4 = 15;
        $col6 = 35;
        $col7 = 40;
        $pdf->Cell($col1, ConstantsEnum::PDF_CELL_HEIGHT,
            'Vehículo',
            1, 0, 'L', true);
        $pdf->Cell($col2, ConstantsEnum::PDF_CELL_HEIGHT,
            'Cliente',
            1, 0, 'C', true);
        $pdf->Cell($col3, ConstantsEnum::PDF_CELL_HEIGHT,
            'Lugar',
            1, 0, 'C', true);
        $pdf->Cell($col4, ConstantsEnum::PDF_CELL_HEIGHT,
            'TM',
            1, 0, 'C', true);
        $pdf->Cell($col6, ConstantsEnum::PDF_CELL_HEIGHT,
            'Operario',
            1, 0, 'C', true);
        $pdf->Cell($col7, ConstantsEnum::PDF_CELL_HEIGHT,
            'Albaranes',
            1, 0, 'C', true);

        $pdf->Ln();
        $this->pdfEngineService->setStyleSize('', 9);

        /** @var SaleDeliveryNote $saleDeliveryNote */
        foreach($saleDeliveryNotes as $saleDeliveryNote){
            //deliverynotes info
            //VEhicle
            $pdf->Cell($col1, ConstantsEnum::PDF_CELL_HEIGHT,
                $saleDeliveryNote->getVehicle(),
                1, 0, 'L', false,'',1);
            //Cliente
            $pdf->Cell($col2, ConstantsEnum::PDF_CELL_HEIGHT,
                $saleDeliveryNote->getPartner()->getName(),
                1, 0, 'L', false,'',1);
            //LUgar
            $pdf->Cell($col3, ConstantsEnum::PDF_CELL_HEIGHT,
                $saleDeliveryNote->getPlace(),
                1, 0, 'L', false,'',1);
            //TM
            $pdf->Cell($col4, ConstantsEnum::PDF_CELL_HEIGHT,
                $saleDeliveryNote->getSaleServiceTariff(),
                1, 0, 'C', false,'',1);
            //Operario
            if($saleDeliveryNote->getOperator()){
                $pdf->Cell($col6, ConstantsEnum::PDF_CELL_HEIGHT,
                    $saleDeliveryNote->getOperator()->getName(),
                    1, 0, 'L', false,'',1);
            } else {
                $pdf->Cell($col6, ConstantsEnum::PDF_CELL_HEIGHT,
                    '',
                    1, 0, 'L', false,'',1);
            }
            //Albaran
            $pdf->Cell($col7, ConstantsEnum::PDF_CELL_HEIGHT,
                'Alb. '.$saleDeliveryNote->getId().' '.
                number_format($saleDeliveryNote->getFinalTotal(),2,',','.').'€',
                1, 0, 'L', false,'',1);

            $pdf->Ln();
        }


        return $pdf;
    }

    /**
     * @param SaleDeliveryNote[]|ArrayCollection|array $saleDeliveryNotes
     *
     * @return TCPDF
     */
    public function buildCollection($saleDeliveryNotes, $documentStyle)
    {
        $this->pdfEngineService->initDefaultPageEngineWithTitle('Grupo de albaranes');
        $pdf = $this->pdfEngineService->getEngine();
        /** @var SaleDeliveryNote $saleDeliveryNote */
        foreach ($saleDeliveryNotes as $saleDeliveryNote) {
            if ('printStandard' == $documentStyle) {
                $pdf = $this->buildOneSaleRequestPerPage($saleDeliveryNote, $pdf, false);
            } elseif ('printDriver' == $documentStyle) {
                $pdf = $this->buildOneSaleRequestPerPageDriverModel($saleDeliveryNote, $pdf, false);
            } elseif ('emailStandard' == $documentStyle) {
                $pdf = $this->buildOneSaleRequestPerPage($saleDeliveryNote, $pdf, true);
            } elseif ('emailDriver' == $documentStyle) {
                $pdf = $this->buildOneSaleRequestPerPageDriverModel($saleDeliveryNote, $pdf, true);
            }
        }

        return $pdf;
    }

    /**
     * @param SaleDeliveryNote[]|ArrayCollection|array $saleDeliveryNotes
     *
     * @return string
     */
    public function outputCollection($saleDeliveryNotes)
    {
        $pdf = $this->buildCollection($saleDeliveryNotes, 'printStandard');

        return $pdf->Output('grupo_albaranes_servicio.pdf', 'I');
    }

    /**
     * @param SaleDeliveryNote[]|ArrayCollection|array $saleDeliveryNotes
     *
     * @return string
     */
    public function outputCollectionDriverPrint($saleDeliveryNotes)
    {
        $pdf = $this->buildCollection($saleDeliveryNotes, 'printDriver');

        return $pdf->Output('grupo_albaranes_servicio_transportista.pdf', 'I');
    }

    /**
     * @param SaleDeliveryNote[]|ArrayCollection|array $saleDeliveryNotes
     *
     * @return string
     */
    public function outputCollectionStandardMail($saleDeliveryNotes)
    {
        $pdf = $this->buildCollection($saleDeliveryNotes, 'emailStandard');

        return $pdf->Output('grupo_albaranes_servicio_mail.pdf', 'I');
    }

    /**
     * @param SaleDeliveryNote[]|ArrayCollection|array $saleDeliveryNotes
     *
     * @return string
     */
    public function outputCollectionDriverMail($saleDeliveryNotes)
    {
        $pdf = $this->buildCollection($saleDeliveryNotes, 'emailDriver');

        return $pdf->Output('grupo_albaranes_servicio_mail_transportista.pdf', 'I');
    }

    /**
     * @return TCPDF
     */
    private function buildOneSaleRequestPerPage(SaleDeliveryNote $saleDeliveryNote, TCPDF $pdf, $withBackground)
    {
        // add start page
        $pdf->AddPage(ConstantsEnum::PDF_LANDSCAPE_PAGE_ORIENTATION, ConstantsEnum::PDF_PAGE_A4);
        $pdf->SetFont(ConstantsEnum::PDF_DEFAULT_FONT, '', 9);
        $width = 70;
        $total = $width + ConstantsEnum::PDF_PAGE_A4_MARGIN_LEFT;
        $availableHoritzontalSpace = 140 - (ConstantsEnum::PDF_PAGE_A4_MARGIN_LEFT * 2);

        // -- set new background ---
        // get the current page break margin
        $bMargin = $pdf->getBreakMargin();
        // get current auto-page-break mode
        $auto_page_break = $pdf->getAutoPageBreak();
        // disable auto-page-break
        $pdf->SetAutoPageBreak(false, 0);
        if ($withBackground) {
            // logo
            $pdf->Image($this->pdfEngineService->getSmartAssetsHelper()->getAbsoluteAssetFilePath('/build/img/logo_empresa.png'), ConstantsEnum::PDF_PAGE_A4_MARGIN_LEFT, 5, 30); // TODO replace by enterprise image if defined

            // set background image
            $img_file = $this->pdfEngineService->getSmartAssetsHelper()->getAbsoluteAssetFilePath('/build/img/delivery_note_template.png');
            $pdf->Image($img_file, 0, 0, 297, 210, '', '', '', false, 300, '', false, false, 0);
            // restore auto-page-break status
            $pdf->SetAutoPageBreak($auto_page_break, $bMargin);
        }
        // set the starting point for the page content
        $pdf->setPageMark();

        // left side
        $xDim = 28;
        $this->fillA5deliveryNote($saleDeliveryNote,$pdf,$total,$availableHoritzontalSpace,$xDim);
        // right side
        $xDim = 168;
        $this->fillA5deliveryNote($saleDeliveryNote,$pdf,$total,$availableHoritzontalSpace,$xDim);

        //add back page

        if ($withBackground) {
            $pdf->AddPage(ConstantsEnum::PDF_LANDSCAPE_PAGE_ORIENTATION, ConstantsEnum::PDF_PAGE_A4);

            // -- set new background ---
            // get the current page break margin
            $bMargin = $pdf->getBreakMargin();
            // get current auto-page-break mode
            $auto_page_break = $pdf->getAutoPageBreak();
            // disable auto-page-break
            $pdf->SetAutoPageBreak(false, 0);
            // set background image
            $img_file = $this->pdfEngineService->getSmartAssetsHelper()->getAbsoluteAssetFilePath('/build/img/delivery_note_template_back.png');
            $pdf->Image($img_file, 0, 0, 297, 210, '', '', '', false, 300, '', false, false, 0);
            // restore auto-page-break status
            $pdf->SetAutoPageBreak($auto_page_break, $bMargin);
        }

        return $pdf;
    }

    private function fillA5deliveryNote(SaleDeliveryNote $saleDeliveryNote, $pdf, $total, $availableHoritzontalSpace, $xDim){
        $yStart = 46;
        $yInterval = 5.7;
        $pdf->setXY($total, $yStart);
        $this->pdfEngineService->setStyleSize('', 9);
        $pdf->setX($xDim);
        //CLIENT
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getPartner()->getName(), 0, 0, 'L', false);
        //CIF
        $pdf->setXY($xDim + 10, $yStart + $yInterval);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getPartner()->getCifNif(), 0, 0, 'L', false);
        //Telf
        $pdf->setXY($xDim + 80, $yStart + $yInterval);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getPartner()->getPhoneNumber1(), 0, 0, 'L', false);
        //Dirección
        $pdf->setXY($xDim+5, $yStart + $yInterval*2);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getPartner()->getMainAddress(), 0, 0, 'L', false);
        //Población
        $pdf->setXY($xDim+5, $yStart + $yInterval*3);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, substr($saleDeliveryNote->getPartner()->getMainCity(), 0, 30), 0, 0, 'L', false);

        //Provincia
        $pdf->setXY($xDim+5, $yStart + $yInterval*4);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, substr($saleDeliveryNote->getPartner()->getMainCity()->getProvince(), 0, 30), 0, 0, 'L', false);

        //Forma de pago
        $pdf->setXY($xDim+90, $yStart + $yInterval*4);
        $pdf->Cell(25, ConstantsEnum::PDF_CELL_HEIGHT,
            $saleDeliveryNote->getPartner()->getCollectionDocumentType() ? strtoupper($saleDeliveryNote->getPartner()->getCollectionDocumentType()->getDescription()) : '',
            0, 0, 'L', false);

        //Vehículo
        $pdf->setXY($xDim+5, $yStart + $yInterval*5);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, strtoupper($saleDeliveryNote->getVehicle()), 0, 0, 'L', false);

        //Operario
        $pdf->setXY($xDim+80, $yStart + $yInterval*5);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getOperator() ? $saleDeliveryNote->getOperator()->getShortFullName() : '', 0, 0, 'L', false);

        //Fecha de servicio
        $pdf->setXY($xDim+15, $yStart + $yInterval*6);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getDateToString(), 0, 0, 'L', false);

        //Hora
        $pdf->setXY($xDim+80, $yStart + $yInterval*6);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getSaleRequest()->getServiceTimeString(), 0, 0, 'L', false);

        //Persona de contacto
        $pdf->setXY($xDim+18, $yStart + $yInterval*7);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getSaleRequest()->getContactPersonName(), 0, 0, 'L', false);

        //Móvil
        $pdf->setXY($xDim+80, $yStart + $yInterval*7);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getSaleRequest()->getContactPersonPhone(), 0, 0, 'L', false);

        //Servicio a realizar
        $pdf->setXY($xDim+15, $yStart + $yInterval*8);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT,
            substr($saleDeliveryNote->getServiceDescription(),0,85), 0, 0, 'L', false,'', 1);

        //Lugar de trabajo
        $pdf->setXY($xDim+15, $yStart + $yInterval*9);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT,
            substr($saleDeliveryNote->getPlace(),0,85), 0, 0, 'L', false);

        //Observaciones
        $pdf->setXY($xDim+15, $yStart + $yInterval*10);
        $pdf->MultiCell($availableHoritzontalSpace, ConstantsEnum::PDF_CELL_HEIGHT,
            substr($saleDeliveryNote->getObservations(),0,240), 0, 'L', false);

        //Fecha impresión
        $pdf->setXY($xDim+40, 176);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, 'AMPOSTA', 0, 0, 'L', false);
        $pdf->setXY($xDim+69, 176);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, date('d'), 0, 0, 'L', false);
        $pdf->setXY($xDim+87, 176);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, date('m'), 0, 0, 'L', false);
        $pdf->setXY($xDim+110, 176);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, date('y'), 0, 0, 'L', false);

        //Nº Albarán
        $this->pdfEngineService->setStyleSize('b', 15);

        $pdf->setXY($xDim+10, 192);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getId(), 0, 0, 'L', false);

    }

    /**
     * @return TCPDF
     */
    private function buildOneSaleRequestPerPageDriverModel(SaleDeliveryNote $saleDeliveryNote, TCPDF $pdf, $withBackground)
    {
        // add start page
        $pdf->AddPage(ConstantsEnum::PDF_LANDSCAPE_PAGE_ORIENTATION, ConstantsEnum::PDF_PAGE_A4);
        $pdf->SetFont(ConstantsEnum::PDF_DEFAULT_FONT, '', 9);
        $width = 70;
        $total = $width + ConstantsEnum::PDF_PAGE_A4_MARGIN_LEFT;
        $availableHoritzontalSpace = 149 - (ConstantsEnum::PDF_PAGE_A4_MARGIN_LEFT * 2);

        // -- set new background ---
        // get the current page break margin
        $bMargin = $pdf->getBreakMargin();
        // get current auto-page-break mode
        $auto_page_break = $pdf->getAutoPageBreak();
        // disable auto-page-break
        $pdf->SetAutoPageBreak(false, 0);
        if ($withBackground) {
            // logo
            $pdf->Image($this->pdfEngineService->getSmartAssetsHelper()->getAbsoluteAssetFilePath('/build/img/logo_empresa.png'), ConstantsEnum::PDF_PAGE_A4_MARGIN_LEFT, 5, 30); // TODO replace by enterprise image if defined

            // set background image
            $img_file = $this->pdfEngineService->getSmartAssetsHelper()->getAbsoluteAssetFilePath('/build/img/delivery_note_template_driver.png');
            $pdf->Image($img_file, 0, 0, 297, 210, '', '', '', false, 300, '', false, false, 0);
            // restore auto-page-break status
            $pdf->SetAutoPageBreak($auto_page_break, $bMargin);
        }
        // set the starting point for the page content
        $pdf->setPageMark();

        // left side
        $xDim = 30;
        $this->fillA5deliveryNoteDriverModel($saleDeliveryNote,$pdf,$total,$availableHoritzontalSpace,$xDim);
        // right side
        $xDim = 172;
        $this->fillA5deliveryNoteDriverModel($saleDeliveryNote,$pdf,$total,$availableHoritzontalSpace,$xDim);

        //add back page

        if ($withBackground) {
            $pdf->AddPage(ConstantsEnum::PDF_LANDSCAPE_PAGE_ORIENTATION, ConstantsEnum::PDF_PAGE_A4);

            // -- set new background ---
            // get the current page break margin
            $bMargin = $pdf->getBreakMargin();
            // get current auto-page-break mode
            $auto_page_break = $pdf->getAutoPageBreak();
            // disable auto-page-break
            $pdf->SetAutoPageBreak(false, 0);
            // set background image
            $img_file = $this->pdfEngineService->getSmartAssetsHelper()->getAbsoluteAssetFilePath('/build/img/delivery_note_template_back_driver.png');
            $pdf->Image($img_file, 0, 0, 297, 210, '', '', '', false, 300, '', false, false, 0);
            // restore auto-page-break status
            $pdf->SetAutoPageBreak($auto_page_break, $bMargin);
        }
        return $pdf;
    }
    /** @var SaleDeliveryNote $saleDeliveryNote */
    private function fillA5deliveryNoteDriverModel(SaleDeliveryNote $saleDeliveryNote, $pdf, $total, $availableHoritzontalSpace, $xDim){
        $yStart = 44;
        $yInterval = 5.7;
        $pdf->setXY($total, $yStart);
        $this->pdfEngineService->setStyleSize('', 9);
        $pdf->setX($xDim);
        //CLIENT
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getPartner()->getName(), 0, 0, 'L', false);
        //CIF
        $pdf->setXY($xDim + 10, $yStart + $yInterval);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getPartner()->getCifNif(), 0, 0, 'L', false);
        //Telf
        $pdf->setXY($xDim + 80, $yStart + $yInterval);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getPartner()->getPhoneNumber1(), 0, 0, 'L', false);
        //Dirección
        $pdf->setXY($xDim+5, $yStart + $yInterval*2);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getPartner()->getMainAddress(), 0, 0, 'L', false);
        //Población
        $pdf->setXY($xDim+5, $yStart + $yInterval*3);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, substr($saleDeliveryNote->getPartner()->getMainCity(), 0, 30), 0, 0, 'L', false);

        //Fecha de servicio
        $pdf->setXY($xDim+10, $yStart + $yInterval*4);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getDateToString(), 0, 0, 'L', false);

        //Hora
        $pdf->setXY($xDim+65, $yStart + $yInterval*4);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getSaleRequest()->getServiceTimeString(), 0, 0, 'L', false);

        //Persona de contacto
        $pdf->setXY($xDim+13, $yStart + $yInterval*5);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getPartner()->getMainContactName(), 0, 0, 'L', false);

        //Operario
        $pdf->setXY($xDim+70, $yStart + $yInterval*5);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getOperator() ? $saleDeliveryNote->getOperator()->getShortFullName() : '', 0, 0, 'L', false);

        //Matrícula tractor
        $pdf->setXY($xDim+8, $yStart + $yInterval*6);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, strtoupper($saleDeliveryNote->getVehicle()->getChassisNumber()), 0, 0, 'L', false);

        //Matrícula remolque
        $pdf->setXY($xDim+85, $yStart + $yInterval*6);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, strtoupper($saleDeliveryNote->getVehicle()->getVehicleRegistrationNumber()), 0, 0, 'L', false);

        //Fecha impresión
        $pdf->setXY($xDim+36, 174);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, 'AMPOSTA', 0, 0, 'L', false);
        $pdf->setXY($xDim+67, 174);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, date('d'), 0, 0, 'L', false);
        $pdf->setXY($xDim+86, 174);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, date('m'), 0, 0, 'L', false);
        $pdf->setXY($xDim+107, 174);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, date('y'), 0, 0, 'L', false);

        //Nº Albarán
        $this->pdfEngineService->setStyleSize('b', 15);

        $pdf->setXY($xDim+7, 190);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getId(), 0, 0, 'L', false);

    }



}
