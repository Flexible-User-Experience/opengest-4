<?php

namespace App\Manager\Pdf;

use App\Entity\Vehicle\Vehicle;
use App\Entity\Vehicle\VehicleMaintenance;
use App\Enum\ConstantsEnum;
use App\Manager\RepositoriesManager;
use App\Manager\VehicleMaintenanceManager;
use App\Service\PdfEngineService;

/**
 * Class VehicleMaintenancePdfManager.
 *
 * @category Manager
 */
class VehicleMaintenancePdfManager
{
    private PdfEngineService $pdfEngineService;

    private RepositoriesManager $rm;

    private VehicleMaintenanceManager $vehicleMaintenanceManager;

    /**
     * Methods.
     */
    public function __construct(PdfEngineService $pdfEngineService, RepositoriesManager $rm, VehicleMaintenanceManager $vehicleMaintenanceManager)
    {
        $this->pdfEngineService = $pdfEngineService;
        $this->rm = $rm;
        $this->vehicleMaintenanceManager = $vehicleMaintenanceManager;
    }

    public function buildSingle($vehicleMaintenances): \TCPDF
    {
        $this->pdfEngineService->initDefaultPageEngineWithTitle('Mantenimiento vehiculos ');
        $pdf = $this->pdfEngineService->getEngine();

        return $this->buildVehicleMaintenancePdf($vehicleMaintenances, $pdf);
    }

    public function outputSingle($vehicleMaintenances): string
    {
        $pdf = $this->buildSingle($vehicleMaintenances);

        return $pdf->Output('Mantenimiento vehiculos.pdf', 'I');
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function buildVehicleMaintenancePdf($vehicleMaintenances, \TCPDF $pdf): \TCPDF
    {
        //        $this->addStartPage($pdf);
        $vehiclesFromVehicleMaintenances = [];
        /** @var VehicleMaintenance $vehicleMaintenance */
        foreach ($vehicleMaintenances as $vehicleMaintenance) {
            $vehiclesFromVehicleMaintenances[$vehicleMaintenance->getVehicle()->getId()][] = $vehicleMaintenance;
        }

        foreach ($vehiclesFromVehicleMaintenances as $vehicleId => $vehicleMaintenancesFromVehicle) {
            /** @var Vehicle $vehicle */
            $vehicle = $this->rm->getVehicleRepository()->find($vehicleId);
            //            if ($pdf->getY() > 210) {
            $this->addStartPage($pdf);
            //            }
            // Header
            $pdf->setXY(30, 40);
            $pdf->setX(ConstantsEnum::PDF_PAGE_A4_MARGIN_LEFT);
            $this->pdfEngineService->setStyleSize('b', 10);
            $pdf->Cell(110, ConstantsEnum::PDF_CELL_HEIGHT,
                'Vehículo - '.$vehicle->getName(),
                true, 0, 'L', true);
            $pdf->Cell(75, ConstantsEnum::PDF_CELL_HEIGHT,
                'Matrícula - '.$vehicle->getVehicleRegistrationNumber(),
                true, 0, 'L', true);
            $pdf->Ln();
            // subheader
            $pdf->setX(ConstantsEnum::PDF_PAGE_A4_MARGIN_LEFT);
            $this->pdfEngineService->setStyleSize('', 9);
            $pdf->Cell(60, ConstantsEnum::PDF_CELL_HEIGHT,
                'Mantenimiento',
                true, 0, 'L', true);
            $pdf->Cell(50, ConstantsEnum::PDF_CELL_HEIGHT,
                'Descripción',
                true, 0, 'L', true);
            $pdf->Cell(25, ConstantsEnum::PDF_CELL_HEIGHT,
                'Km restantes',
                true, 0, 'L', true);
            $pdf->Cell(25, ConstantsEnum::PDF_CELL_HEIGHT,
                'Horas restantes',
                true, 0, 'L', true);
            $pdf->Cell(25, ConstantsEnum::PDF_CELL_HEIGHT,
                'Fecha efectuada',
                true, 0, 'L', true);
            $pdf->Ln();
            // info
            /** @var VehicleMaintenance $vehicleMaintenance */
            foreach ($vehicleMaintenances as $vehicleMaintenance) {
                $pdf->setX(ConstantsEnum::PDF_PAGE_A4_MARGIN_LEFT);
                $this->pdfEngineService->setStyleSize('', 9);
                $pdf->Cell(60, ConstantsEnum::PDF_CELL_HEIGHT,
                    $vehicleMaintenance->getVehicleMaintenanceTask(),
                    true, 0, 'L', false, '', 1);
                $pdf->Cell(50, ConstantsEnum::PDF_CELL_HEIGHT,
                    $vehicleMaintenance->getDescription(),
                    true, 0, 'L', false, '', 1);
                $pdf->Cell(25, ConstantsEnum::PDF_CELL_HEIGHT,
                    $this->vehicleMaintenanceManager->remainingKm($vehicleMaintenance),
                    true, 0, 'L', false);
                $pdf->Cell(25, ConstantsEnum::PDF_CELL_HEIGHT,
                    $this->vehicleMaintenanceManager->remainingHours($vehicleMaintenance),
                    true, 0, 'L', false);
                $pdf->Cell(25, ConstantsEnum::PDF_CELL_HEIGHT,
                    '',
                    true, 0, 'L', false);
                $pdf->Ln();
            }
        }

        return $pdf;
    }

    /**
     * @param int $availableHoritzontalSpace
     */
    private function drawHoritzontalLineSeparator(\TCPDF $pdf, $availableHoritzontalSpace)
    {
        $pdf->ln(4);
        $pdf->Line(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT, $pdf->getY(), $availableHoritzontalSpace + ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT, $pdf->getY());
        $pdf->ln(4);
    }

    private function addStartPage(\TCPDF $pdf): void
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
        $today = date('d/m/Y');
        $pdf->SetFont(ConstantsEnum::PDF_DEFAULT_FONT, 'b', 11);
        $pdf->setXY(50, 30);
        $pdf->Cell('', ConstantsEnum::PDF_CELL_HEIGHT,
            'Listado de mantenimientos pendientes a '.$today);
    }
}
