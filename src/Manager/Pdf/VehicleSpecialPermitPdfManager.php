<?php

namespace App\Manager\Pdf;

use App\Entity\Vehicle\VehicleSpecialPermit;
use App\Enum\ConstantsEnum;
use App\Service\PdfEngineService;
use TCPDF;

/**
 * Class VehicleSpecialPermitPdfManager.
 *
 * @category Manager
 */
class VehicleSpecialPermitPdfManager
{
    private PdfEngineService $pdfEngineService;

    /**
     * Methods.
     */
    public function __construct(PdfEngineService $pdfEngineService)
    {
        $this->pdfEngineService = $pdfEngineService;
    }

    public function buildSingle($vehicleSpecialPermits): TCPDF
    {
        $this->pdfEngineService->initDefaultPageEngineWithTitle('Permisos especiales');
        $pdf = $this->pdfEngineService->getEngine();

        return $this->buildVehicleSpecialPermitsPdf($vehicleSpecialPermits, $pdf);
    }

    public function outputSingle($vehicleSpecialPermits): string
    {
        $pdf = $this->buildSingle($vehicleSpecialPermits);

        return $pdf->Output('Permisos especiales'.'.pdf', 'I');
    }

    private function buildVehicleSpecialPermitsPdf($vehicleSpecialPermits, TCPDF $pdf): TCPDF
    {
        // add start page
        $this->startPage($pdf);
        $minWidth = 20;
        $pdf->SetFont(ConstantsEnum::PDF_DEFAULT_FONT, '', 8);
        /** @var VehicleSpecialPermit $vehicleSpecialPermit */
        foreach($vehicleSpecialPermits as $vehicleSpecialPermit) {
            if($pdf->GetY()>220){
                $this->startPage($pdf);
            }
            $pdf->Cell($minWidth * 3, ConstantsEnum::PDF_CELL_HEIGHT,
                $vehicleSpecialPermit->getVehicle(),
                1, 0, 'L', false);
            $pdf->Cell($minWidth * 3, ConstantsEnum::PDF_CELL_HEIGHT,
                $vehicleSpecialPermit->getAdditionalVehicle(),
                1, 0, 'L', false);
            $pdf->Cell($minWidth, ConstantsEnum::PDF_CELL_HEIGHT,
                $vehicleSpecialPermit->getExpedientNumber(),
                1, 0, 'L', false);
            $pdf->Cell($minWidth, ConstantsEnum::PDF_CELL_HEIGHT,
                date_format($vehicleSpecialPermit->getExpiryDate(), 'd/m/Y'),
                1, 0, 'L', false);
            $pdf->Cell($minWidth, ConstantsEnum::PDF_CELL_HEIGHT,
                $vehicleSpecialPermit->getLoadContent(),
                1, 0, 'L', false);
            $pdf->Cell($minWidth * 2, ConstantsEnum::PDF_CELL_HEIGHT,
                $vehicleSpecialPermit->getTotalLength() . 'x' . $vehicleSpecialPermit->getTotalWidth() . 'x' . $vehicleSpecialPermit->getTotalHeight() . 'x' . $vehicleSpecialPermit->getMaximumWeight(),
                1, 0, 'L', false);
            $pdf->Cell($minWidth * 3, ConstantsEnum::PDF_CELL_HEIGHT,
                $vehicleSpecialPermit->getRoute(),
                1, 0, 'L', false);
            $pdf->Ln();
        }

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
     * @return int
     */
    private function startPage(TCPDF $pdf): int
    {
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

        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT,
            'PERMISOS ESPECIALES',
            0, 0, 'L', false);
        $pdf->Ln();
        $minWidth = 20;
        $pdf->Cell($minWidth * 3, ConstantsEnum::PDF_CELL_HEIGHT,
            'Vehículo',
            1, 0, 'L', false);
        $pdf->Cell($minWidth * 3, ConstantsEnum::PDF_CELL_HEIGHT,
            'Vehículo adicional',
            1, 0, 'L', false);
        $pdf->Cell($minWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            'Expediente',
            1, 0, 'L', false);
        $pdf->Cell($minWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            'Caduca',
            1, 0, 'L', false);
        $pdf->Cell($minWidth, ConstantsEnum::PDF_CELL_HEIGHT,
            'Carga',
            1, 0, 'L', false);
        $pdf->Cell($minWidth * 2, ConstantsEnum::PDF_CELL_HEIGHT,
            'LxANxALxP',
            1, 0, 'L', false);
        $pdf->Cell($minWidth * 3, ConstantsEnum::PDF_CELL_HEIGHT,
            'Itinerario',
            1, 0, 'L', false);
        $pdf->Ln();
        return $minWidth;
    }
}
