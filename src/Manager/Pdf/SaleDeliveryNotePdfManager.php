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
    public function buildCollection($saleDeliveryNotes)
    {
        $this->pdfEngineService->initDefaultPageEngineWithTitle('Grupo de albaranes');
        $pdf = $this->pdfEngineService->getEngine();
        /** @var SaleDeliveryNote $saleDeliveryNote */
        foreach ($saleDeliveryNotes as $saleDeliveryNote) {
            $pdf = $this->buildOneSaleRequestPerPage($saleDeliveryNote, $pdf);
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
        $pdf = $this->buildCollection($saleDeliveryNotes);

        return $pdf->Output('grupo_albaranes_servicio.pdf', 'I');
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
        // set background image
        $img_file = $this->pdfEngineService->getSmartAssetsHelper()->getAbsoluteAssetFilePath('/build/img/PlantillaAlbara.png');
        //dd($img_file);
        $pdf->Image($img_file, 0, 0, 148, 210, '', '', '', false, 300, '', false, false, 0);
        // restore auto-page-break status
        $pdf->SetAutoPageBreak($auto_page_break, $bMargin);
        // set the starting point for the page content
        $pdf->setPageMark();

        // customer section

        /*     $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT);
             $this->pdfEngineService->setStyleSize('B', 9);
             $pdf->Cell(21, ConstantsEnum::PDF_CELL_HEIGHT, 'FECHA', 0, 0, 'L', true);
             $this->pdfEngineService->setStyleSize('', 9);
             $pdf->Cell(108, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getSaleRequest()->getServiceDateString(), 0, 0, 'L', true);
             $pdf->Cell(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT, ConstantsEnum::PDF_CELL_HEIGHT, '', 0, 1, 'L', true);*/
        $pdf->setXY($total, 40);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 10);
        //$this->pdfEngineService->setStyleSize('B', 9);
        //$pdf->Cell(21, ConstantsEnum::PDF_CELL_HEIGHT, 'EMPRESA', 0, 0, 'L', true);
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
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getOperator()->getShortFullName(), 0, 0, 'L', false);

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

        //Hora entrada (precio/h normal)
        $pdf->setXY($total, 120);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 24);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getSaleRequest()->getServiceTimeString(), 0, 0, 'L', false);

        //Hora salida (precio/h normal)
        $pdf->setXY($total, 120);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 46);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getSaleRequest()->getServiceTimeString(), 0, 0, 'L', false);

        //Total horas (precio/h normal)
        $pdf->setXY($total, 120);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 63);
        $pdf->Cell(20, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getSaleRequest()->getServiceTimeString(), 0, 0, 'C', true);

        //Precio/hora (precio/h normal)
        $pdf->setXY($total, 120);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 86);
        $pdf->Cell(20, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getSaleRequest()->getServiceTimeString(), 0, 0, 'C', true);

        //Total (precio/h normal)
        $pdf->setXY($total, 120);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 110);
        $pdf->Cell(20, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getSaleRequest()->getServiceTimeString(), 0, 0, 'C', true);

        //Hora entrada (precio/h laboral)
        $pdf->setXY($total, 128);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 24);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getSaleRequest()->getServiceTimeString(), 0, 0, 'L', false);

        //Hora salida (precio/h laboral)
        $pdf->setXY($total, 128);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 46);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getSaleRequest()->getServiceTimeString(), 0, 0, 'L', false);

        //Total horas (precio/h laboral)
        $pdf->setXY($total, 128);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 63);
        $pdf->Cell(20, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getSaleRequest()->getServiceTimeString(), 0, 0, 'C', true);

        //Precio/hora (precio/h laboral)
        $pdf->setXY($total, 128);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 86);
        $pdf->Cell(20, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getSaleRequest()->getServiceTimeString(), 0, 0, 'C', true);

        //Total (precio/h laboral)
        $pdf->setXY($total, 128);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 110);
        $pdf->Cell(20, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getSaleRequest()->getServiceTimeString(), 0, 0, 'C', true);

        //Hora entrada (precio/h extra)
        $pdf->setXY($total, 136);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 24);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getSaleRequest()->getServiceTimeString(), 0, 0, 'L', false);

        //Hora salida (precio/h extra)
        $pdf->setXY($total, 136);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 46);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getSaleRequest()->getServiceTimeString(), 0, 0, 'L', false);

        //Total horas (precio/h extra)
        $pdf->setXY($total, 136);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 63);
        $pdf->Cell(20, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getSaleRequest()->getServiceTimeString(), 0, 0, 'C', true);

        //Precio/hora (precio/h extra)
        $pdf->setXY($total, 136);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 86);
        $pdf->Cell(20, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getSaleRequest()->getServiceTimeString(), 0, 0, 'C', true);

        //Total (precio/h extra)
        $pdf->setXY($total, 136);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 110);
        $pdf->Cell(20, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getSaleRequest()->getServiceTimeString(), 0, 0, 'C', true);

        //Desplazamiento horas
        $pdf->setXY($total, 144);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 63);
        $pdf->Cell(20, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getSaleRequest()->getServiceTimeString(), 0, 0, 'C', true);

        //Desplazamiento precio/hora
        $pdf->setXY($total, 144);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 86);
        $pdf->Cell(20, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getSaleRequest()->getServiceTimeString(), 0, 0, 'C', true);

        //Desplazamiento total
        $pdf->setXY($total, 144);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 110);
        $pdf->Cell(20, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getSaleRequest()->getServiceTimeString(), 0, 0, 'C', true);

        //Suma horas
        $pdf->setXY($total, 152);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 63);
        $pdf->Cell(20, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getSaleRequest()->getServiceTimeString(), 0, 0, 'C', true);

        //Suma total
        $pdf->setXY($total, 152);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 110);
        $pdf->Cell(20, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getBaseAmount(), 0, 0, 'C', true);

        //IVA
        $pdf->setXY($total, 161);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 110);
        $pdf->Cell(20, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getSaleRequest()->getServiceTimeString(), 0, 0, 'C', true);

        //Total final
        $pdf->setXY($total, 169);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 110);
        $pdf->Cell(20, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getSaleRequest()->getServiceTimeString(), 0, 0, 'C', true);

        //Nº Albarán
        $pdf->setXY($total, 200);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + 10);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getId(), 0, 0, 'L', false);

        /*$pdf->setX($pdf->GetX() + 2);
        $this->pdfEngineService->setStyleSize('B', 9);
        $pdf->Cell(10, ConstantsEnum::PDF_CELL_HEIGHT, 'SR.', 0, 0, 'L', true);
        $this->pdfEngineService->setStyleSize('', 9);
        $pdf->Cell(29, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getSaleRequest()->getPartner()->getMainContactName(), 0, 0, 'L', true);
        $pdf->Cell(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT, ConstantsEnum::PDF_CELL_HEIGHT, '', 0, 1, 'L', true);

        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT);
        $this->pdfEngineService->setStyleSize('B', 9);
        $pdf->Cell(21, ConstantsEnum::PDF_CELL_HEIGHT, 'POBLACIÓN', 0, 0, 'L', true);
        $this->pdfEngineService->setStyleSize('', 9);
        $pdf->Cell(67, ConstantsEnum::PDF_CELL_HEIGHT, substr($saleDeliveryNote->getSaleRequest()->getPartner()->getMainCityName(), 0, 30), 0, 0, 'L', true);
        $pdf->setX($pdf->GetX() + 2);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + $width + 20);
        $this->pdfEngineService->setStyleSize('B', 9);
        $pdf->Cell(10, ConstantsEnum::PDF_CELL_HEIGHT, 'TELF.', 0, 0, 'L', true);
        $this->pdfEngineService->setStyleSize('', 9);
        $pdf->Cell(29, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getSaleRequest()->getPartner()->getPhoneNumber1(), 0, 0, 'L', true);
        $pdf->Cell(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT, ConstantsEnum::PDF_CELL_HEIGHT, '', 0, 1, 'L', true);

        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT);
        $this->pdfEngineService->setStyleSize('B', 9);
        $pdf->Cell(21, ConstantsEnum::PDF_CELL_HEIGHT, 'DIRECCIÓN', 0, 0, 'L', true);
        $this->pdfEngineService->setStyleSize('', 9);
        $pdf->Cell(108, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getSaleRequest()->getPartner()->getMainAddress(), 0, 0, 'L', true);
        $pdf->Cell(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT, ConstantsEnum::PDF_CELL_HEIGHT, '', 0, 1, 'L', true);

        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT);
        $this->pdfEngineService->setStyleSize('B', 9);
        $pdf->Cell(21, ConstantsEnum::PDF_CELL_HEIGHT, 'C.I.F.', 0, 0, 'L', true);
        $this->pdfEngineService->setStyleSize('', 9);
        $pdf->Cell(67, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getSaleRequest()->getPartner()->getCifNif(), 0, 0, 'L', true);
        $pdf->setX($pdf->GetX() + 2);
        $this->pdfEngineService->setStyleSize('B', 9);
        $pdf->Cell(14, ConstantsEnum::PDF_CELL_HEIGHT, 'F.PAGO', 0, 0, 'L', true);
        $this->pdfEngineService->setStyleSize('', 9);
        $pdf->Cell(25, ConstantsEnum::PDF_CELL_HEIGHT, strtoupper($saleDeliveryNote->getSaleRequest()->getPartner()->getClass()->getName()), 0, 0, 'L', true); // TODO not reading properly attribute
        $pdf->Cell(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT, ConstantsEnum::PDF_CELL_HEIGHT, '', 0, 1, 'L', true);


        // operator section
        $pdf->setXY($total, 5);
        $this->pdfEngineService->setStyleSize('B', 9);
        $pdf->Cell(15, ConstantsEnum::PDF_CELL_HEIGHT, 'GRUA Nº', 0, 0, 'L', true);
        $this->pdfEngineService->setStyleSize('', 9);
        $pdf->Cell(44, ConstantsEnum::PDF_CELL_HEIGHT, strtoupper($saleDeliveryNote->getSaleRequest()->getVehicle()), 0, 0, 'L', true);
        $pdf->Cell(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT, ConstantsEnum::PDF_CELL_HEIGHT, '', 0, 1, 'L', true);

        $pdf->setX($total);
        $this->pdfEngineService->setStyleSize('B', 9);
        $pdf->Cell(15, ConstantsEnum::PDF_CELL_HEIGHT, 'TM', 0, 0, 'L', true);
        $this->pdfEngineService->setStyleSize('', 9);
        $pdf->Cell(44, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getSaleRequest()->getService()->getDescription(), 0, 0, 'L', true);
        $pdf->Cell(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT, ConstantsEnum::PDF_CELL_HEIGHT, '', 0, 1, 'L', true);

        $pdf->setX($total);
        $this->pdfEngineService->setStyleSize('B', 9);
        $pdf->Cell(15, ConstantsEnum::PDF_CELL_HEIGHT, 'CHOFER', 0, 0, 'L', true);
        $this->pdfEngineService->setStyleSize('', 9);
        $pdf->Cell(44, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getSaleRequest()->getOperator()->getShortFullName(), 0, 0, 'L', true);
        $pdf->Cell(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT, ConstantsEnum::PDF_CELL_HEIGHT, '', 0, 1, 'L', true);

        // heading title
        $pdf->Ln(ConstantsEnum::PDF_CELL_HEIGHT);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT);
        $this->pdfEngineService->setStyleSize('B', 14);
        $pdf->Cell($availableHoritzontalSpace, ConstantsEnum::PDF_CELL_HEIGHT, 'PETICIÓN DE SERVICIO', 0, 1, 'L', true);
        $pdf->Ln(ConstantsEnum::PDF_CELL_HEIGHT);



        // draw horitzontal line separator
        $this->drawHoritzontalLineSeparator($pdf, $availableHoritzontalSpace);

        // work section
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT);
        $this->pdfEngineService->setStyleSize('B', 9);
        $pdf->Cell($availableHoritzontalSpace, ConstantsEnum::PDF_CELL_HEIGHT, 'TRABAJO A REALIZAR', 0, 1, 'L', true);

        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT);
        $this->pdfEngineService->setStyleSize('', 9);
        $pdf->MultiCell($availableHoritzontalSpace, 6 * ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getSaleRequest()->getServiceDescription(), 0, 'L', true, 1, '', '', true, 0, false, true, 6 * ConstantsEnum::PDF_CELL_HEIGHT);

        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT);
        $this->pdfEngineService->setStyleSize('B', 9);
        $pdf->Cell(15, ConstantsEnum::PDF_CELL_HEIGHT, 'ALTURA', 0, 0, 'L', true);
        $this->pdfEngineService->setStyleSize('', 9);
        $pdf->Cell(29, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getSaleRequest()->getHeightString(), 0, 0, 'L', true);
        $pdf->setX($pdf->GetX() + 2);
        $this->pdfEngineService->setStyleSize('B', 9);
        $pdf->Cell(19, ConstantsEnum::PDF_CELL_HEIGHT, 'DISTANCIA', 0, 0, 'L', true);
        $this->pdfEngineService->setStyleSize('', 9);
        $pdf->Cell(23, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getSaleRequest()->getDistanceString(), 0, 0, 'L', true);
        $pdf->setX($pdf->GetX() + 2);
        $this->pdfEngineService->setStyleSize('B', 9);
        $pdf->Cell(11, ConstantsEnum::PDF_CELL_HEIGHT, 'PESO', 0, 0, 'L', true);
        $this->pdfEngineService->setStyleSize('', 9);
        $pdf->Cell(28, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getSaleRequest()->getWeightString(), 0, 0, 'L', true);
        $pdf->Cell(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT, ConstantsEnum::PDF_CELL_HEIGHT, '', 0, 1, 'L', true);

        // draw horitzontal line separator
        $this->drawHoritzontalLineSeparator($pdf, $availableHoritzontalSpace);

        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT);
        $this->pdfEngineService->setStyleSize('B', 9);
        $pdf->Cell($availableHoritzontalSpace, ConstantsEnum::PDF_CELL_HEIGHT, 'LUGAR DE TRABAJO', 0, 1, 'L', true);

        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT);
        $this->pdfEngineService->setStyleSize('', 9);
        $pdf->MultiCell($availableHoritzontalSpace, 4 * ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getSaleRequest()->getPlace(), 0, 'L', true, 1, '', '', true, 0, false, true, 4 * ConstantsEnum::PDF_CELL_HEIGHT);

        // draw horitzontal line separator
        $this->drawHoritzontalLineSeparator($pdf, $availableHoritzontalSpace);

        // final settings section
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT);
        $this->pdfEngineService->setStyleSize('B', 9);
        $pdf->Cell(11, ConstantsEnum::PDF_CELL_HEIGHT, 'HORA', 0, 0, 'L', true);
        $this->pdfEngineService->setStyleSize('', 9);
        $pdf->Cell(19, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getSaleRequest()->getServiceTimeString(), 0, 0, 'L', true);
        $pdf->setX($pdf->GetX() + 2);
        $this->pdfEngineService->setStyleSize('B', 9);
        $pdf->Cell(7, ConstantsEnum::PDF_CELL_HEIGHT, 'DIA', 0, 0, 'L', true);
        $this->pdfEngineService->setStyleSize('', 9);
        $pdf->Cell(22, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getSaleRequest()->getServiceDateString(), 0, 0, 'L', true);
        $pdf->setX($pdf->GetX() + 2);
        $this->pdfEngineService->setStyleSize('B', 9);
        $pdf->Cell(17, ConstantsEnum::PDF_CELL_HEIGHT, 'MÍNIMO H.', 0, 0, 'L', true);
        $this->pdfEngineService->setStyleSize('', 9);
        $pdf->Cell(16, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getSaleRequest()->getMiniumHours(), 0, 0, 'L', true);
        $pdf->setX($pdf->GetX() + 2);
        $this->pdfEngineService->setStyleSize('B', 9);
        $pdf->Cell(18, ConstantsEnum::PDF_CELL_HEIGHT, 'PRECIO H.', 0, 0, 'L', true);
        $this->pdfEngineService->setStyleSize('', 9);
        $pdf->Cell(13, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getSaleRequest()->getHourPrice(), 0, 0, 'L', true);
        $pdf->Cell(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT, ConstantsEnum::PDF_CELL_HEIGHT, '', 0, 1, 'L', true);

        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT);
        $this->pdfEngineService->setStyleSize('B', 9);
        $pdf->Cell(32, ConstantsEnum::PDF_CELL_HEIGHT, 'DESPLAZAMIENTO', 0, 0, 'L', true);
        $this->pdfEngineService->setStyleSize('', 9);
        $pdf->Cell(38, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getSaleRequest()->getDisplacement(), 0, 0, 'L', true);
        $pdf->setX($pdf->GetX() + 2);
        $this->pdfEngineService->setStyleSize('B', 9);
        $pdf->Cell(26, ConstantsEnum::PDF_CELL_HEIGHT, 'ATENDIDO POR', 0, 0, 'L', true);
        $user = $saleDeliveryNote->getSaleRequest()->getAttendedBy();
        $this->pdfEngineService->setStyleSize('', 9);
        $pdf->Cell(31, ConstantsEnum::PDF_CELL_HEIGHT, strtoupper($user->getUsername()), 0, 0, 'L', true);
        $pdf->Cell(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT, ConstantsEnum::PDF_CELL_HEIGHT, '', 0, 1, 'L', true);

        // final observations section
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT);
        $this->pdfEngineService->setStyleSize('B', 9);
        $pdf->Cell($availableHoritzontalSpace, ConstantsEnum::PDF_CELL_HEIGHT, 'OBSERVACIONES', 0, 1, 'L', true);

        $this->pdfEngineService->setStyleSize('', 9);
        if ($saleDeliveryNote->getSaleRequest()->getUtensils()) {
            $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT);
            $pdf->Cell($availableHoritzontalSpace, ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getSaleRequest()->getUtensils(), 0, 1, 'L', true);
        }

        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT);
        $pdf->MultiCell($availableHoritzontalSpace, 2 * ConstantsEnum::PDF_CELL_HEIGHT, $saleDeliveryNote->getSaleRequest()->getObservations(), 0, 'L', true, 1, '', '', true, 0, false, true, 2 * ConstantsEnum::PDF_CELL_HEIGHT);
*/

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
