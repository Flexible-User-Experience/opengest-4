<?php

namespace App\Manager\Pdf;

use App\Entity\Vehicle\VehicleChecking;
use App\Enum\ConstantsEnum;
use App\Manager\RepositoriesManager;
use App\Service\PdfEngineService;
use TCPDF;

/**
 * Class VehicleCheckingPdfManager.
 *
 * @category Manager
 */
class VehicleCheckingPdfManager
{
    private PdfEngineService $pdfEngineService;

    private RepositoriesManager $rm;

    /**
     * Methods.
     */
    public function __construct(PdfEngineService $pdfEngineService, RepositoriesManager $rm)
    {
        $this->pdfEngineService = $pdfEngineService;

        $this->rm = $rm;
    }

    public function buildSingle($vehicleCheckings): TCPDF
    {
        $this->pdfEngineService->initDefaultPageEngineWithTitle('Revisiones de vehiculos');
        $pdf = $this->pdfEngineService->getEngine();

        return $this->buildVehicleCheckingsPdf($vehicleCheckings, $pdf);
    }

    public function outputSingle($vehicleCheckings): string
    {
        $pdf = $this->buildSingle($vehicleCheckings);

        return $pdf->Output('Revisiones de vehiculos'.'.pdf', 'I');
    }

    private function buildVehicleCheckingsPdf($vehicleCheckings, TCPDF $pdf): TCPDF
    {
        // add start page
        $pdf->setMargins(ConstantsEnum::PDF_PAGE_A4_MARGIN_LEFT, ConstantsEnum::PDF_PAGE_A4_MARGIN_TOP, ConstantsEnum::PDF_PAGE_A4_MARGIN_RIGHT, true);
        $pdf->AddPage(ConstantsEnum::PDF_PORTRAIT_PAGE_ORIENTATION, ConstantsEnum::PDF_PAGE_A4);
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

        // logo
        $pdf->Image($this->pdfEngineService->getSmartAssetsHelper()->getAbsoluteAssetFilePath('/build/img/logo_empresa.png'), ConstantsEnum::PDF_PAGE_A4_MARGIN_LEFT, 5, 30); // TODO replace by enterprise image if defined

        $vehiclesFromVehicleCheckings = [];
        /** @var VehicleChecking $vehicleChecking */
        foreach ($vehicleCheckings as $vehicleChecking) {
            $vehiclesFromVehicleCheckings[$vehicleChecking->getVehicle()->getId()][] = $vehicleChecking;
        }
        $pdf->setXY(30, 40);
        foreach ($vehiclesFromVehicleCheckings as $vehicleId => $vehicleCheckingsFromVehicle) {
            /** @var Vehicle $vehicle */
            $vehicle = $this->rm->getVehicleRepository()->find($vehicleId);

            //Header
            $pdf->setX(ConstantsEnum::PDF_PAGE_A4_MARGIN_LEFT);
            $this->pdfEngineService->setStyleSize('b', 10);
            $pdf->Cell(90, ConstantsEnum::PDF_CELL_HEIGHT,
                $vehicle->getName(),
                true, 0, 'L', true);
            $pdf->Cell(80, ConstantsEnum::PDF_CELL_HEIGHT,
                'Matrícula - '.$vehicle->getVehicleRegistrationNumber(),
                true, 0, 'L', true);
            $pdf->Ln();
            //subheader
            $pdf->setX(ConstantsEnum::PDF_PAGE_A4_MARGIN_LEFT);
            $this->pdfEngineService->setStyleSize('', 9);
            $pdf->Cell(50, ConstantsEnum::PDF_CELL_HEIGHT,
                'Revisión',
                true, 0, 'C', true);
            $pdf->Cell(40, ConstantsEnum::PDF_CELL_HEIGHT,
                'Caduca',
                true, 0, 'C', true);
            $pdf->Cell(50, ConstantsEnum::PDF_CELL_HEIGHT,
                'Cita previa',
                true, 0, 'C', true);
            $pdf->Cell(30, ConstantsEnum::PDF_CELL_HEIGHT,
                'Revisado',
                true, 0, 'C', true);
            $pdf->Ln();
            //info
            /** @var VehicleChecking $vehicleChecking */
            foreach ($vehicleCheckingsFromVehicle as $vehicleChecking) {
                    $pdf->setX(ConstantsEnum::PDF_PAGE_A4_MARGIN_LEFT);
                    $this->pdfEngineService->setStyleSize('', 9);
                    $pdf->Cell(50, ConstantsEnum::PDF_CELL_HEIGHT,
                        $vehicleChecking->getType(),
                        true, 0, 'L', false);
                    $pdf->Cell(40, ConstantsEnum::PDF_CELL_HEIGHT,
                        $vehicleChecking->getEnd()->format('d/m/Y'),
                        true, 0, 'L', false);
                    $pdf->Cell(50, ConstantsEnum::PDF_CELL_HEIGHT,
                        '',
                        true, 0, 'L', false);
                    $pdf->Cell(30, ConstantsEnum::PDF_CELL_HEIGHT,
                        '',
                        true, 0, 'L', false);
                    $pdf->Ln();
            }
            $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT,
                '',
                false, 0, 'L', false);
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
}
