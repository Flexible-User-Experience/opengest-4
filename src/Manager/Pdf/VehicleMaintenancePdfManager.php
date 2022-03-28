<?php

namespace App\Manager\Pdf;

use App\Entity\Vehicle\Vehicle;
use App\Entity\Vehicle\VehicleMaintenance;
use App\Enum\ConstantsEnum;
use App\Service\PdfEngineService;
use TCPDF;

/**
 * Class VehicleMaintenancePdfManager.
 *
 * @category Manager
 */
class VehicleMaintenancePdfManager
{
    private PdfEngineService $pdfEngineService;

    /**
     * Methods.
     */
    public function __construct(PdfEngineService $pdfEngineService)
    {
        $this->pdfEngineService = $pdfEngineService;
    }

    public function buildSingle($vehicleMaintenances): TCPDF
    {
        $this->pdfEngineService->initDefaultPageEngineWithTitle('Mantenimiento vehiculos ');
        $pdf = $this->pdfEngineService->getEngine();

        return $this->buildVehicleMaintenancePdf($vehicleMaintenances, $pdf);
    }

    public function outputSingle($vehicleMaintenances): string
    {
        $pdf = $this->buildSingle($vehicleMaintenances);

        return $pdf->Output('Mantenimiento vehiculos'.'.pdf', 'I');
    }

    private function buildVehicleMaintenancePdf($vehicleMaintenances, TCPDF $pdf): TCPDF
    {
        // add start page
        $pdf->setMargins(ConstantsEnum::PDF_PAGE_A4_MARGIN_LEFT, ConstantsEnum::PDF_PAGE_A4_MARGIN_TOP, ConstantsEnum::PDF_PAGE_A4_MARGIN_RIGHT, true);
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

        // logo
        $pdf->Image($this->pdfEngineService->getSmartAssetsHelper()->getAbsoluteAssetFilePath('/build/img/logo_empresa.png'), ConstantsEnum::PDF_PAGE_A4_MARGIN_LEFT, 5, 30); // TODO replace by enterprise image if defined

        //TODO get the list of operators
        $vehicles = [];
        /** @var Vehicle $vehicles */
        foreach ($vehicles as $vehicle) {
            //Header
            $pdf->setXY(30, 40);
            $pdf->setX(ConstantsEnum::PDF_PAGE_A4_MARGIN_LEFT);
            $this->pdfEngineService->setStyleSize('b', 10);
            $pdf->Cell(130, ConstantsEnum::PDF_CELL_HEIGHT,
                'Vehículo - '.$vehicle->getName(),
                true, 0, 'L', true);
            $pdf->Cell(40, ConstantsEnum::PDF_CELL_HEIGHT,
                'Matrícula - '.$vehicle->getVehicleRegistrationNumber(),
                true, 0, 'L', true);
            $pdf->Ln();
            //subheader
            $pdf->setX(ConstantsEnum::PDF_PAGE_A4_MARGIN_LEFT);
            $this->pdfEngineService->setStyleSize('', 9);
            $pdf->Cell(60, ConstantsEnum::PDF_CELL_HEIGHT,
                'Mantenimiento',
                true, 0, 'L', true);
            $pdf->Cell(70, ConstantsEnum::PDF_CELL_HEIGHT,
                'Descripción',
                true, 0, 'L', true);
            $pdf->Cell(20, ConstantsEnum::PDF_CELL_HEIGHT,
                'Horas restantes',
                true, 0, 'L', true);
            $pdf->Cell(20, ConstantsEnum::PDF_CELL_HEIGHT,
                'Fecha efectuada',
                true, 0, 'L', true);
            //info
            /** @var VehicleMaintenance $vehicleMaintenance */
            foreach ($vehicleMaintenances as $vehicleMaintenance) {
                $pdf->setX(ConstantsEnum::PDF_PAGE_A4_MARGIN_LEFT);
                $this->pdfEngineService->setStyleSize('', 9);
                $pdf->Cell(60, ConstantsEnum::PDF_CELL_HEIGHT,
                    $vehicleMaintenance->getVehicleMaintenanceTask(),
                    true, 0, 'L', false);
                $pdf->Cell(70, ConstantsEnum::PDF_CELL_HEIGHT,
                    $vehicleMaintenance->getDescription(),
                    true, 0, 'L', false);
                //TODO calculate missing hours
                $pdf->Cell(20, ConstantsEnum::PDF_CELL_HEIGHT,
                    '',
                    true, 0, 'L', false);
                $pdf->Cell(20, ConstantsEnum::PDF_CELL_HEIGHT,
                    '',
                    true, 0, 'L', false);
            }
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
}
