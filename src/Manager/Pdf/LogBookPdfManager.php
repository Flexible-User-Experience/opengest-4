<?php

namespace App\Manager\Pdf;

use App\Entity\Payslip\Payslip;
use App\Entity\Payslip\PayslipLine;
use App\Entity\Purchase\PurchaseInvoiceLine;
use App\Entity\Setting\CostCenter;
use App\Entity\Vehicle\Vehicle;
use App\Enum\ConstantsEnum;
use App\Manager\RepositoriesManager;
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
    public function __construct(
        PdfEngineService $pdfEngineService,
        private readonly RepositoriesManager $rm,
    )
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
    public function buildCollection($vehicles): TCPDF
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
    public function outputCollection($vehicles): string
    {
        $pdf = $this->buildCollection($vehicles);

        return $pdf->Output('libro-historial.pdf', 'I');
    }

    private function buildOnePayslipPerPage(Vehicle $vehicle, TCPDF $pdf): TCPDF
    {
        // add start page
        $pdf->setMargins(ConstantsEnum::PDF_PAGE_A4_MARGIN_LEFT, ConstantsEnum::PDF_PAGE_A4_MARGIN_TOP, ConstantsEnum::PDF_PAGE_A4_MARGIN_RIGHT, true);
        $pdf->AddPage(ConstantsEnum::PDF_PORTRAIT_PAGE_ORIENTATION, ConstantsEnum::PDF_PAGE_A4);
        $pdf->SetFont(ConstantsEnum::PDF_DEFAULT_FONT, '', 9);
        $width = ConstantsEnum::PDF_PAGE_A4_WIDTH_PORTRAIT- ConstantsEnum::PDF_PAGE_A4_MARGIN_RIGHT - ConstantsEnum::PDF_PAGE_A4_MARGIN_LEFT;

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
        $pdf->Image($this->pdfEngineService->getSmartAssetsHelper()->getAbsoluteAssetFilePath('/build/img/logo_empresa.png'), ConstantsEnum::PDF_PAGE_A4_MARGIN_LEFT, 5, 30); // TODO replace by enterprise image if defined
        $today = date('d/m/Y');
        $pdf->SetFont(ConstantsEnum::PDF_DEFAULT_FONT, 'b', 11);
        $pdf->setXY(50, ConstantsEnum::PDF_PAGE_A4_MARGIN_TOP);
        $pdf->Cell('', ConstantsEnum::PDF_CELL_HEIGHT,
            'Libro historial del vehículo a fecha '.$today);
        $pdf->setXY(50, 15);
        $pdf->SetFont(ConstantsEnum::PDF_DEFAULT_FONT, '', 10);
        $pdf->Multicell($width, ConstantsEnum::PDF_CELL_HEIGHT,
            'Vehículo: '.$vehicle->getName(), 0, 'L', false,1);
        $pdf->setX(50);
        $pdf->Multicell($width, ConstantsEnum::PDF_CELL_HEIGHT,
            'Tonelaje: '.$vehicle->getTonnage(), 0, 'L', false,1);

        $pdf->setY(50);
        $width = $width + 35;
        $pdf->SetFont(ConstantsEnum::PDF_DEFAULT_FONT, 'b', 10);
        $pdf->Multicell($width, ConstantsEnum::PDF_CELL_HEIGHT,
            'DATOS DEL VEHÍCULO ', 1, 'C', true, 1);
        $pdf->SetFont(ConstantsEnum::PDF_DEFAULT_FONT, '', 10);
        $numberOfLinesMatr = $pdf->getNumLines('Matrícula: '.$vehicle->getVehicleRegistrationNumber(), $width*3/12);
        $numberOfLinesChasis = $pdf->getNumLines('Marca chasis: '.$vehicle->getChassisBrand(), $width*3/12);
        $numberOfLinesBast = $pdf->getNumLines('Número bastidor: '.$vehicle->getChassisNumber(), $width*6/12);
        $maxLines = max($numberOfLinesMatr, $numberOfLinesChasis,$numberOfLinesBast);
        $pdf->Multicell($width*3/12, ConstantsEnum::PDF_CELL_HEIGHT*$maxLines,
            'Matrícula: '.$vehicle->getVehicleRegistrationNumber(), 0, 'L', false, 0);
        $pdf->Multicell($width*3/12, ConstantsEnum::PDF_CELL_HEIGHT*$maxLines,
            'Marca chasis: '.$vehicle->getChassisBrand(), 0, 'L', false, 0);
        $pdf->Multicell($width*6/12, ConstantsEnum::PDF_CELL_HEIGHT*$maxLines,
            'Número bastidor: '.$vehicle->getChassisNumber(), 0, 'L', false, 1);
        $numberOfLinesMarca = $pdf->getNumLines('Marca grua: '.$vehicle->getVehicleBrand(), $width*3/12);
        $numberOfLinesModelo = $pdf->getNumLines('Modelo grua: '.$vehicle->getVehicleModel(), $width*3/12);
        $numberOfLinesSerie = $pdf->getNumLines('Nº Serie grua: '.$vehicle->getSerialNumber(), $width*6/12);
        $maxLines = max($numberOfLinesMarca, $numberOfLinesModelo,$numberOfLinesSerie);
        $pdf->Multicell($width*3/12, ConstantsEnum::PDF_CELL_HEIGHT*$maxLines,
            'Marca grua: '.$vehicle->getVehicleBrand(), 'B', 'L', false, 0);
        $pdf->Multicell($width*3/12, ConstantsEnum::PDF_CELL_HEIGHT*$maxLines,
            'Modelo grua: '.$vehicle->getVehicleModel(), 'B', 'L', false, 0);
        $pdf->Multicell($width*6/12, ConstantsEnum::PDF_CELL_HEIGHT*$maxLines,
            'Nº Serie grua: '.$vehicle->getSerialNumber(), 'B', 'L', false, 1);

        $purchaseInvoiceLines = $this->rm->getPurchaseInvoiceLineRepository()->getOrderedByDateByVehicle($vehicle);
        $costCenters = [];
        foreach($purchaseInvoiceLines as $purchaseInvoiceLine){
            /** @var CostCenter $costCenter */
            $costCenter = $purchaseInvoiceLine->getCostCenter();
            if(!in_array($costCenter, $costCenters) && $costCenter->isShowInLogBook()){
                $costCenters[] = $costCenter;
            }
        }
        usort($costCenters, function(CostCenter $a, CostCenter $b){
            return $a->getOrderInLogBook() >= $b->getOrderInLogBook();
        });

        foreach($costCenters as $costCenter){
            if($pdf->getY() > 210){
                $this->drawFooter($pdf);
                $pdf->AddPage(ConstantsEnum::PDF_PORTRAIT_PAGE_ORIENTATION, ConstantsEnum::PDF_PAGE_A4);
                $pdf->setY(ConstantsEnum::PDF_PAGE_A4_MARGIN_TOP);
            }

            $pdf->Ln(10);
            $pdf->SetFont(ConstantsEnum::PDF_DEFAULT_FONT, 'b', 10);
            $pdf->Multicell($width, ConstantsEnum::PDF_CELL_HEIGHT,
                $costCenter->getName(), 1, 'C', true, 1);
            $pdf->Multicell($width*2/12, ConstantsEnum::PDF_CELL_HEIGHT,
                'Fecha', 1, 'L', false, 0);
            $pdf->Multicell($width*4/12, ConstantsEnum::PDF_CELL_HEIGHT,
                'Proveedor', 1, 'L', false, 0);
            $pdf->Multicell($width*6/12, ConstantsEnum::PDF_CELL_HEIGHT,
                'Descripción', 1, 'L', false, 1);

            $pdf->SetFont(ConstantsEnum::PDF_DEFAULT_FONT, '0', 10);
            /** @var PurchaseInvoiceLine $purchaseInvoiceLine */
            foreach($purchaseInvoiceLines as $purchaseInvoiceLine){
                if($purchaseInvoiceLine->getCostCenter()?->getId() === $costCenter->getId()){
                    $numberOfLinesName = $pdf->getNumLines($purchaseInvoiceLine->getPurchaseInvoice()->getPartnerName(), $width*4/12);
                    $numberOfLinesDesc = $pdf->getNumLines($purchaseInvoiceLine->getDescription(), $width*6/12);
                    $maxLines= max($numberOfLinesName, $numberOfLinesDesc);
                    $pdf->Multicell($width*2/12, ConstantsEnum::PDF_CELL_HEIGHT*$maxLines,
                        $purchaseInvoiceLine->getPurchaseInvoice()->getDateFormatted(), 1, 'L', false, 0);
                    $pdf->Multicell($width*4/12, ConstantsEnum::PDF_CELL_HEIGHT*$maxLines,
                        $purchaseInvoiceLine->getPurchaseInvoice()->getPartnerName(), 1, 'L', false, 0);
                    $pdf->Multicell($width*6/12, ConstantsEnum::PDF_CELL_HEIGHT*$maxLines,
                        $purchaseInvoiceLine->getDescription(), 1, 'L', false, 1);
                }
            }
        }
        $this->drawFooter($pdf);



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

    private function drawFooter(TCPDF $pdf){
        $pdf->setY(280);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT,
            $pdf->getAliasNumPage().'/'.$pdf->getAliasNbPages(),
            0, 0, 'R', true);
        $pdf->Ln(15);
    }
}
