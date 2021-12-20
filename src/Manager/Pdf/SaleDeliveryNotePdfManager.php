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
        $pdf->SetXY(40,20);
        $today = date('d/m/Y');
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT,
            $today,
            0, 0, 'L', false);

        // header
        //TODO add partner and from to info
        $this->pdfEngineService->setStyleSize('', 12);
        $pdf->SetXY(40,40);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT,
            'Listado de albaranes del cliente '.'X'.' desde '.'Y'.' hasta '.'Z',
            0, 0, 'L', false);
        $pdf->SetXY(40,45);
        $this->drawHoritzontalLineSeparator($pdf,$width);
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
        $pdf->AddPage(ConstantsEnum::PDF_PORTRAIT_PAGE_ORIENTATION, ConstantsEnum::PDF_PAGE_A5);
        $pdf->SetFont(ConstantsEnum::PDF_DEFAULT_FONT, '', 9);
        $width = 70;
        $total = $width + ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT;
        $availableHoritzontalSpace = 149 - (ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT * 2);

        // logo
        $pdf->Image($this->pdfEngineService->getSmartAssetsHelper()->getAbsoluteAssetFilePath('/build/img/logo_empresa.png'), ConstantsEnum::PDF_PAGE_A4_MARGIN_LEFT, 5, 30); // TODO replace by enterprise image if defined
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
            $img_file = $this->pdfEngineService->getSmartAssetsHelper()->getAbsoluteAssetFilePath('/build/img/delivery_note_template.png');
            $pdf->Image($img_file, 0, 0, 297, 210, '', '', '', false, 300, '', false, false, 0);
            // restore auto-page-break status
            $pdf->SetAutoPageBreak($auto_page_break, $bMargin);
        }
        // set the starting point for the page content
        $pdf->setPageMark();

        // left side
        $xDim = 20;
        $this->fillA5deliveryNote($saleDeliveryNote,$pdf,$total,$availableHoritzontalSpace,$xDim);
        // right side
        $xDim = 170;
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

    private function fillA5deliveryNote($saleDeliveryNote, $pdf, $total, $availableHoritzontalSpace, $xDim){
        $yStart = 42;
        $yInterval = 6;
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
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, substr($saleDeliveryNote->getPartner()->getMainCityName(), 0, 30), 0, 0, 'L', false);

        //Forma de pago
        $pdf->setXY($xDim+90, $yStart + $yInterval*4);
        $pdf->Cell(25, ConstantsEnum::PDF_CELL_HEIGHT, strtoupper($saleDeliveryNote->getPartner()->getClass()->getName()), 0, 0, 'L', false); // TODO not reading properly attribute

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
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getPartner()->getMainContactName(), 0, 0, 'L', false);

        //Móvil
        $pdf->setXY($xDim+80, $yStart + $yInterval*7);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getPartner()->getPhoneNumber2(), 0, 0, 'L', false);

        //Servicio a realizar
        $pdf->setXY($xDim+18, $yStart + $yInterval*8);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getServiceDescription(), 0, 0, 'L', false);

        //Lugar de trabajo
        $pdf->setXY($xDim+15, $yStart + $yInterval*9);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getPlace(), 0, 0, 'L', false);

        //Observaciones
        $pdf->setXY($xDim+15, $yStart + $yInterval*10);
        $pdf->MultiCell($availableHoritzontalSpace, 2 * ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getObservations(), 0, 'L', false, 1, '', '', true, 0, false, true, 2 * ConstantsEnum::PDF_CELL_HEIGHT);

        //Nº Albarán
        $this->pdfEngineService->setStyleSize('b', 15);

        $pdf->setXY($xDim+10, 198);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getId(), 0, 0, 'L', false);

    }

    /**
     * @return TCPDF
     */
    private function buildOneSaleRequestPerPageDriverModel(SaleDeliveryNote $saleDeliveryNote, TCPDF $pdf, $withBackground)
    {
        // add start page
        $pdf->AddPage(ConstantsEnum::PDF_PORTRAIT_PAGE_ORIENTATION, ConstantsEnum::PDF_PAGE_A5);
        $pdf->SetFont(ConstantsEnum::PDF_DEFAULT_FONT, '', 9);
        $width = 70;
        $total = $width + ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT;
        $availableHoritzontalSpace = 149 - (ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT * 2);

        // logo
        $pdf->Image($this->pdfEngineService->getSmartAssetsHelper()->getAbsoluteAssetFilePath('/bundles/app/img/logo_romani.png'), ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT, 5, 30); // TODO replace by enterprise image if defined

        // -- set new background ---
        // get the current page break margin
        $bMargin = $pdf->getBreakMargin();
        // get current auto-page-break mode
        $auto_page_break = $pdf->getAutoPageBreak();
        // disable auto-page-break
        $pdf->SetAutoPageBreak(false, 0);
        if ($withBackground) {
            // set background image
            $img_file = $this->pdfEngineService->getSmartAssetsHelper()->getAbsoluteAssetFilePath('/build/img/PlantillaAlbara.png');
            $pdf->Image($img_file, 0, 0, 148, 210, '', '', '', false, 300, '', false, false, 0);
            // restore auto-page-break status
            $pdf->SetAutoPageBreak($auto_page_break, $bMargin);
        }
        // set the starting point for the page content
        $pdf->setPageMark();

        // customer section
        //CLIENT-LOADER
        $pdf->setXY($total, 37);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 10);
        $this->pdfEngineService->setStyleSize('', 9);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getPartner()->getName(), 0, 0, 'L', false);
        //CIF
        $pdf->setXY($total, 43);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 18);
        $this->pdfEngineService->setStyleSize('', 9);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getPartner()->getCifNif(), 0, 0, 'L', false);
        //Telf
        $pdf->setXY($total, 43);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 85);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getPartner()->getPhoneNumber1(), 0, 0, 'L', false);
        //Dirección
        $pdf->setXY($total, 49);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 14);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getPartner()->getMainAddress(), 0, 0, 'L', false);
        //Población
        $pdf->setXY($total, 55);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 14);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, substr($saleDeliveryNote->getPartner()->getMainCity(), 0, 30), 0, 0, 'L', false);

        //Fecha de servicio
        $pdf->setXY($total, 61);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 24);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getDateToString(), 0, 0, 'L', false);

        //Hora
        $pdf->setXY($total, 61);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 82);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getSaleRequest()->getServiceTimeString(), 0, 0, 'L', false);

        //Persona de contacto
        $pdf->setXY($total, 67);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 28);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getPartner()->getMainContactName(), 0, 0, 'L', false);

        //Operario
        $pdf->setXY($total, 67);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 85);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getOperator() ? $saleDeliveryNote->getOperator()->getShortFullName() : '', 0, 0, 'L', false);

        //Matrícula tractor
        $pdf->setXY($total, 73);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 14);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, strtoupper($saleDeliveryNote->getVehicle()->getVehicleRegistrationNumber()), 0, 0, 'L', false);

        //Matrícula remolque
//        $pdf->setXY($total, 73);
//        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 95);
//        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, strtoupper($saleDeliveryNote->getVehicle()->getChassisNumber()), 0, 0, 'L', false);

//        //Company
//        $pdf->setXY($total, 84);
//        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 10);
//        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, strtoupper($saleDeliveryNote->getEnterprise()), 0, 0, 'L', false);
//
//        //Origin
//        $pdf->setXY($total, 84);
//        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 80);
//        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, strtoupper($saleDeliveryNote->getEnterprise()->getAddress()), 0, 0, 'L', false);
//
//        //Client
//        $pdf->setXY($total, 89);
//        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 10);
//        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getPartner()->getName(), 0, 0, 'L', false);
//
//        //Destination
//        $pdf->setXY($total, 89);
//        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 80);
//        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getPlace(), 0, 0, 'L', false);
//
//
//        //Load
//        $pdf->setXY($total, 95);
//        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 24);
//        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getServiceDescription(), 0, 0, 'L', false);
//
//
//        //Observaciones
//        $pdf->setXY($total, 103);
//        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 22);
//        $pdf->MultiCell($availableHoritzontalSpace, 2 * ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getObservations(), 0, 'L', false, 1, '', '', true, 0, false, true, 2 * ConstantsEnum::PDF_CELL_HEIGHT);

        //Nº Albarán
        $pdf->setXY($total, 200);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 10);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getId(), 0, 0, 'L', false);

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
