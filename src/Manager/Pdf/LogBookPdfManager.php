<?php

namespace App\Manager\Pdf;

use App\Entity\Payslip\Payslip;
use App\Entity\Payslip\PayslipLine;
use App\Entity\Vehicle\Vehicle;
use App\Enum\ConstantsEnum;
use App\Service\Format\NumberFormatService;
use App\Service\PdfEngineService;
use Doctrine\Common\Collections\ArrayCollection;
use TCPDF;

/**
 * Class LogBookPdfManager.
 *
 * @category Manager
 */
class LogBookPdfManager
{
    private PdfEngineService $pdfEngineService;

    /**
     * Methods.
     */
    public function __construct(PdfEngineService $pdfEngineService)
    {
        $this->pdfEngineService = $pdfEngineService;
    }

    public function buildSingle(Vehicle $vehicle): TCPDF
    {
        $this->pdfEngineService->initDefaultPageEngineWithTitle('Libro historial vehículo '.$vehicle);
        $pdf = $this->pdfEngineService->getEngine();

        return $this->buildOnePayslipPerPage($vehicle, $pdf);
    }

    public function outputSingle(Vehicle $vehicle): string
    {
        $pdf = $this->buildSingle($vehicle);

        return $pdf->Output('libro-historial-vehiculo-'.$vehicle->getId().'.pdf', 'I');
    }

    /**
     * @param Vehicle[]|ArrayCollection|array $vehicles
     *
     * @return TCPDF
     */
    public function buildCollection($vehicles)
    {
        $this->pdfEngineService->initDefaultPageEngineWithTitle('Libro historial vehículo');
        $pdf = $this->pdfEngineService->getEngine();
        foreach ($vehicles as $vehicle) {
            $pdf = $this->buildOnePayslipPerPage($vehicle, $pdf);
        }

        return $pdf;
    }

    /**
     * @param Vehicle[]|ArrayCollection|array $vehicles
     *
     * @return string
     */
    public function outputCollection($vehicles)
    {
        $pdf = $this->buildCollection($vehicles);

        return $pdf->Output('libro-historial.pdf', 'I');
    }

    private function buildOnePayslipPerPage(Vehicle $vehicle, TCPDF $pdf): TCPDF
    {
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
//        $this->pdfEngineService->setStyleSize('', 9);
        $today = date('d/m/Y');
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT,
            'Informe nómina general - Grúas Romaní - '.$today,
            1, 0, 'L', true);

        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT,
            $pdf->getAliasNumPage().'/'.$pdf->getAliasNbPages(),
            0, 0, 'R', true);
        $pdf->Ln(15);
        $pdf->Ln();

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
