<?php

namespace App\Manager\Pdf;

use App\Entity\Sale\SaleDeliveryNote;
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
            if ($documentStyle = 'printStandard') {
                $pdf = $this->buildOneSaleRequestPerPage($saleDeliveryNote, $pdf);
            } elseif ($documentStyle = 'printDriver') {
                $pdf = $this->buildOneSaleRequestPerPageToPrintDriverModel($saleDeliveryNote, $pdf);
            } elseif ($documentStyle = 'emailStandard') {
                $pdf = $this->buildOneSaleRequestPerPageEmailModel($saleDeliveryNote, $pdf);
            } elseif ($documentStyle = 'emailDriver') {
                $pdf = $this->buildOneSaleRequestPerPageEmailDriverModel($saleDeliveryNote, $pdf);
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
    private function buildOneSaleRequestPerPage(SaleDeliveryNote $saleDeliveryNote, TCPDF $pdf)
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
        // restore auto-page-break status
        $pdf->SetAutoPageBreak($auto_page_break, $bMargin);
        // set the starting point for the page content
        $pdf->setPageMark();

        // loader
        $pdf->setXY($total, 34);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 10);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getPartner()->getName(), 0, 0, 'L', false);

        // customer section
        $pdf->setXY($total, 42);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 10);
        //CLIENT
        $this->pdfEngineService->setStyleSize('', 9);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getPartner()->getName(), 0, 0, 'L', false);
        //CIF
        $pdf->setXY($total, 46);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 18);
        $this->pdfEngineService->setStyleSize('', 9);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getPartner()->getCifNif(), 0, 0, 'L', false);
        //Telf
        $pdf->setXY($total, 46);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 85);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getPartner()->getPhoneNumber1(), 0, 0, 'L', false);
        //Dirección
        $pdf->setXY($total, 52);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 14);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getPartner()->getMainAddress(), 0, 0, 'L', false);
        //Población
        $pdf->setXY($total, 59);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 14);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, substr($saleDeliveryNote->getPartner()->getMainCity(), 0, 30), 0, 0, 'L', false);

        //Provincia
        $pdf->setXY($total, 65);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 14);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, substr($saleDeliveryNote->getPartner()->getMainCityName(), 0, 30), 0, 0, 'L', false);

        //Forma de pago
        $pdf->setXY($total, 65);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 95);
        $pdf->Cell(25, ConstantsEnum::PDF_CELL_HEIGHT, strtoupper($saleDeliveryNote->getPartner()->getClass()->getName()), 0, 0, 'L', false); // TODO not reading properly attribute

        //Vehículo
        $pdf->setXY($total, 71);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 14);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, strtoupper($saleDeliveryNote->getVehicle()), 0, 0, 'L', false);

        //Operario
        $pdf->setXY($total, 71);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 85);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getOperator() ? $saleDeliveryNote->getOperator()->getShortFullName() : '', 0, 0, 'L', false);

        //Fecha de servicio
        $pdf->setXY($total, 77);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 24);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getDateToString(), 0, 0, 'L', false);

        //Hora
        $pdf->setXY($total, 77);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 82);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getSaleRequest()->getServiceTimeString(), 0, 0, 'L', false);

        //Persona de contacto
        $pdf->setXY($total, 83);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 28);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getPartner()->getMainContactName(), 0, 0, 'L', false);

        //Móvil
        $pdf->setXY($total, 83);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 82);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getPartner()->getPhoneNumber2(), 0, 0, 'L', false);

        //Servicio a realizar
        $pdf->setXY($total, 89);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 24);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getServiceDescription(), 0, 0, 'L', false);

        //Lugar de trabajo
        $pdf->setXY($total, 96);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 23);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getPlace(), 0, 0, 'L', false);

        //Observaciones
        $pdf->setXY($total, 103);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 22);
        $pdf->MultiCell($availableHoritzontalSpace, 2 * ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getObservations(), 0, 'L', false, 1, '', '', true, 0, false, true, 2 * ConstantsEnum::PDF_CELL_HEIGHT);

        //Nº Albarán
        $pdf->setXY($total, 200);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 10);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getId(), 0, 0, 'L', false);

        return $pdf;
    }

    /**
     * @return TCPDF
     */
    private function buildOneSaleRequestPerPageToPrintDriverModel(SaleDeliveryNote $saleDeliveryNote, TCPDF $pdf)
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
        // set background image
        $img_file = $this->pdfEngineService->getSmartAssetsHelper()->getAbsoluteAssetFilePath('/build/img/PlantillaAlbara.png');
        //dd($img_file);
        $pdf->Image($img_file, 0, 0, 148, 210, '', '', '', false, 300, '', false, false, 0);
        // restore auto-page-break status
        $pdf->SetAutoPageBreak($auto_page_break, $bMargin);
        // set the starting point for the page content
        $pdf->setPageMark();

        // customer section

        $pdf->setXY($total, 40);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 10);
        //CLIENT
        $this->pdfEngineService->setStyleSize('', 9);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getPartner()->getName(), 0, 0, 'L', false);
        //CIF
        $pdf->setXY($total, 46);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 18);
        $this->pdfEngineService->setStyleSize('', 9);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getPartner()->getCifNif(), 0, 0, 'L', false);
        //Telf
        $pdf->setXY($total, 46);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 85);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getPartner()->getPhoneNumber1(), 0, 0, 'L', false);
        //Dirección
        $pdf->setXY($total, 52);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 14);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getPartner()->getMainAddress(), 0, 0, 'L', false);
        //Población
        $pdf->setXY($total, 59);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 14);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, substr($saleDeliveryNote->getPartner()->getMainCity(), 0, 30), 0, 0, 'L', false);

        //Provincia
        $pdf->setXY($total, 65);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 14);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, substr($saleDeliveryNote->getPartner()->getMainCityName(), 0, 30), 0, 0, 'L', false);

        //Forma de pago
        $pdf->setXY($total, 65);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 95);
        $pdf->Cell(25, ConstantsEnum::PDF_CELL_HEIGHT, strtoupper($saleDeliveryNote->getPartner()->getClass()->getName()), 0, 0, 'L', false); // TODO not reading properly attribute

        //Vehículo
        $pdf->setXY($total, 71);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 14);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, strtoupper($saleDeliveryNote->getVehicle()), 0, 0, 'L', false);

        //Operario
        $pdf->setXY($total, 71);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 85);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getOperator() ? $saleDeliveryNote->getOperator()->getShortFullName() : '', 0, 0, 'L', false);

        //Fecha de servicio
        $pdf->setXY($total, 77);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 24);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getDateToString(), 0, 0, 'L', false);

        //Hora
        $pdf->setXY($total, 77);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 82);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getSaleRequest()->getServiceTimeString(), 0, 0, 'L', false);

        //Persona de contacto
        $pdf->setXY($total, 83);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 28);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getPartner()->getMainContactName(), 0, 0, 'L', false);

        //Móvil
        $pdf->setXY($total, 83);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 82);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getPartner()->getPhoneNumber2(), 0, 0, 'L', false);

        //Servicio a realizar
        $pdf->setXY($total, 89);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 24);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getServiceDescription(), 0, 0, 'L', false);

        //Lugar de trabajo
        $pdf->setXY($total, 96);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 23);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getPlace(), 0, 0, 'L', false);

        //Observaciones
        $pdf->setXY($total, 103);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 22);
        $pdf->MultiCell($availableHoritzontalSpace, 2 * ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getObservations(), 0, 'L', false, 1, '', '', true, 0, false, true, 2 * ConstantsEnum::PDF_CELL_HEIGHT);

        //Nº Albarán
        $pdf->setXY($total, 200);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 10);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getId(), 0, 0, 'L', false);

        return $pdf;
    }

    /**
     * @return TCPDF
     */
    private function buildOneSaleRequestPerPageEmailModel(SaleDeliveryNote $saleDeliveryNote, TCPDF $pdf)
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
        // set background image
        $img_file = $this->pdfEngineService->getSmartAssetsHelper()->getAbsoluteAssetFilePath('/build/img/PlantillaAlbara.png');
        //dd($img_file);
        $pdf->Image($img_file, 0, 0, 148, 210, '', '', '', false, 300, '', false, false, 0);
        // restore auto-page-break status
        $pdf->SetAutoPageBreak($auto_page_break, $bMargin);
        // set the starting point for the page content
        $pdf->setPageMark();

        // customer section

        $pdf->setXY($total, 40);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 10);
        //CLIENT
        $this->pdfEngineService->setStyleSize('', 9);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getPartner()->getName(), 0, 0, 'L', false);
        //CIF
        $pdf->setXY($total, 46);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 18);
        $this->pdfEngineService->setStyleSize('', 9);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getPartner()->getCifNif(), 0, 0, 'L', false);
        //Telf
        $pdf->setXY($total, 46);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 85);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getPartner()->getPhoneNumber1(), 0, 0, 'L', false);
        //Dirección
        $pdf->setXY($total, 52);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 14);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getPartner()->getMainAddress(), 0, 0, 'L', false);
        //Población
        $pdf->setXY($total, 59);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 14);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, substr($saleDeliveryNote->getPartner()->getMainCity(), 0, 30), 0, 0, 'L', false);

        //Provincia
        $pdf->setXY($total, 65);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 14);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, substr($saleDeliveryNote->getPartner()->getMainCityName(), 0, 30), 0, 0, 'L', false);

        //Forma de pago
        $pdf->setXY($total, 65);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 95);
        $pdf->Cell(25, ConstantsEnum::PDF_CELL_HEIGHT, strtoupper($saleDeliveryNote->getPartner()->getClass()->getName()), 0, 0, 'L', false); // TODO not reading properly attribute

        //Vehículo
        $pdf->setXY($total, 71);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 14);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, strtoupper($saleDeliveryNote->getVehicle()), 0, 0, 'L', false);

        //Operario
        $pdf->setXY($total, 71);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 85);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getOperator() ? $saleDeliveryNote->getOperator()->getShortFullName() : '', 0, 0, 'L', false);

        //Fecha de servicio
        $pdf->setXY($total, 77);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 24);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getDateToString(), 0, 0, 'L', false);

        //Hora
        $pdf->setXY($total, 77);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 82);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getSaleRequest()->getServiceTimeString(), 0, 0, 'L', false);

        //Persona de contacto
        $pdf->setXY($total, 83);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 28);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getPartner()->getMainContactName(), 0, 0, 'L', false);

        //Móvil
        $pdf->setXY($total, 83);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 82);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getPartner()->getPhoneNumber2(), 0, 0, 'L', false);

        //Servicio a realizar
        $pdf->setXY($total, 89);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 24);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getServiceDescription(), 0, 0, 'L', false);

        //Lugar de trabajo
        $pdf->setXY($total, 96);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 23);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getPlace(), 0, 0, 'L', false);

        //Observaciones
        $pdf->setXY($total, 103);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 22);
        $pdf->MultiCell($availableHoritzontalSpace, 2 * ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getObservations(), 0, 'L', false, 1, '', '', true, 0, false, true, 2 * ConstantsEnum::PDF_CELL_HEIGHT);

        //Nº Albarán
        $pdf->setXY($total, 200);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 10);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getId(), 0, 0, 'L', false);

        return $pdf;
    }

    /**
     * @return TCPDF
     */
    private function buildOneSaleRequestPerPageEmailDriverModel(SaleDeliveryNote $saleDeliveryNote, TCPDF $pdf)
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
        // set background image
        $img_file = $this->pdfEngineService->getSmartAssetsHelper()->getAbsoluteAssetFilePath('/build/img/PlantillaAlbara.png');
        //dd($img_file);
        $pdf->Image($img_file, 0, 0, 148, 210, '', '', '', false, 300, '', false, false, 0);
        // restore auto-page-break status
        $pdf->SetAutoPageBreak($auto_page_break, $bMargin);
        // set the starting point for the page content
        $pdf->setPageMark();

        // customer section

        $pdf->setXY($total, 40);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 10);
        //CLIENT
        $this->pdfEngineService->setStyleSize('', 9);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getPartner()->getName(), 0, 0, 'L', false);
        //CIF
        $pdf->setXY($total, 46);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 18);
        $this->pdfEngineService->setStyleSize('', 9);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getPartner()->getCifNif(), 0, 0, 'L', false);
        //Telf
        $pdf->setXY($total, 46);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 85);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getPartner()->getPhoneNumber1(), 0, 0, 'L', false);
        //Dirección
        $pdf->setXY($total, 52);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 14);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getPartner()->getMainAddress(), 0, 0, 'L', false);
        //Población
        $pdf->setXY($total, 59);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 14);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, substr($saleDeliveryNote->getPartner()->getMainCity(), 0, 30), 0, 0, 'L', false);

        //Provincia
        $pdf->setXY($total, 65);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 14);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, substr($saleDeliveryNote->getPartner()->getMainCityName(), 0, 30), 0, 0, 'L', false);

        //Forma de pago
        $pdf->setXY($total, 65);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 95);
        $pdf->Cell(25, ConstantsEnum::PDF_CELL_HEIGHT, strtoupper($saleDeliveryNote->getPartner()->getClass()->getName()), 0, 0, 'L', false); // TODO not reading properly attribute

        //Vehículo
        $pdf->setXY($total, 71);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 14);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, strtoupper($saleDeliveryNote->getVehicle()), 0, 0, 'L', false);

        //Operario
        $pdf->setXY($total, 71);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 85);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getOperator() ? $saleDeliveryNote->getOperator()->getShortFullName() : '', 0, 0, 'L', false);

        //Fecha de servicio
        $pdf->setXY($total, 77);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 24);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getDateToString(), 0, 0, 'L', false);

        //Hora
        $pdf->setXY($total, 77);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 82);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getSaleRequest()->getServiceTimeString(), 0, 0, 'L', false);

        //Persona de contacto
        $pdf->setXY($total, 83);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 28);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getPartner()->getMainContactName(), 0, 0, 'L', false);

        //Móvil
        $pdf->setXY($total, 83);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 82);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getPartner()->getPhoneNumber2(), 0, 0, 'L', false);

        //Servicio a realizar
        $pdf->setXY($total, 89);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 24);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getServiceDescription(), 0, 0, 'L', false);

        //Lugar de trabajo
        $pdf->setXY($total, 96);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 23);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getPlace(), 0, 0, 'L', false);

        //Observaciones
        $pdf->setXY($total, 103);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 22);
        $pdf->MultiCell($availableHoritzontalSpace, 2 * ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getObservations(), 0, 'L', false, 1, '', '', true, 0, false, true, 2 * ConstantsEnum::PDF_CELL_HEIGHT);

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
