<?php

namespace App\Manager\Pdf;

use App\Entity\Sale\SaleRequest;
use App\Enum\ConstantsEnum;
use App\Service\PdfEngineService;
use Doctrine\Common\Collections\ArrayCollection;
use TCPDF;

/**
 * Class SaleRequestPdfManager.
 *
 * @category Manager
 */
class SaleRequestPdfManager
{
    /**
     * @var PdfEngineService
     */
    private PdfEngineService $pdfEngineService;

    /**
     * Methods.
     */

    /**
     * @param PdfEngineService $pdfEngineService
     */
    public function __construct(PdfEngineService $pdfEngineService)
    {
        $this->pdfEngineService = $pdfEngineService;
    }

    /**
     * @param SaleRequest $saleRequest
     *
     * @return TCPDF
     */
    public function buildSingle(SaleRequest $saleRequest)
    {
        $this->pdfEngineService->initDefaultPageEngineWithTitle('Petición de servicio '.$saleRequest);
        $pdf = $this->pdfEngineService->getEngine();

        return $this->buildOneSaleRequestPerPage($saleRequest, $pdf);
    }

    /**
     * @param SaleRequest $saleRequest
     *
     * @return string
     */
    public function outputSingle(SaleRequest $saleRequest)
    {
        $pdf = $this->buildSingle($saleRequest);

        return $pdf->Output('peticion_'.$saleRequest->getId().'.pdf', 'I');
    }

    /**
     * @param SaleRequest[]|ArrayCollection|array $saleRequests
     *
     * @return TCPDF
     */
    public function buildCollection($saleRequests)
    {
        $this->pdfEngineService->initDefaultPageEngineWithTitle('Grupo de peticiones de servicio');
        $pdf = $this->pdfEngineService->getEngine();
        /** @var SaleRequest $saleRequest */
        foreach ($saleRequests as $saleRequest) {
            $pdf = $this->buildOneSaleRequestPerPage($saleRequest, $pdf);
        }

        return $pdf;
    }

    /**
     * @param SaleRequest[]|ArrayCollection|array $saleRequests
     *
     * @return string
     */
    public function outputCollection($saleRequests)
    {
        $pdf = $this->buildCollection($saleRequests);

        return $pdf->Output('grupo_peticiones_servicio.pdf', 'I');
    }

    /**
     * @param SaleRequest $saleRequest
     * @param TCPDF       $pdf
     *
     * @return TCPDF
     */
    private function buildOneSaleRequestPerPage(SaleRequest $saleRequest, TCPDF $pdf)
    {
        // add start page
        $pdf->AddPage(ConstantsEnum::PDF_PORTRAIT_PAGE_ORIENTATION, ConstantsEnum::PDF_PAGE_A5);
        $pdf->SetFont(ConstantsEnum::PDF_DEFAULT_FONT, '', 9);
        $width = 70;
        $total = $width + ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT;
        $availableHoritzontalSpace = 149 - (ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT * 2);

        // logo
        $pdf->Image($this->pdfEngineService->getSmartAssetsHelper()->getAbsoluteAssetFilePath('/bundles/app/img/logo_romani.png'), ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT, 5, 30); // TODO replace by enterprise image if defined

        // operator section
        $pdf->setXY($total, 5);
        $this->pdfEngineService->setStyleSize('B', 9);
        $pdf->Cell(15, ConstantsEnum::PDF_CELL_HEIGHT, 'GRUA Nº', 0, 0, 'L', true);
        $this->pdfEngineService->setStyleSize('', 9);
        $pdf->Cell(44, ConstantsEnum::PDF_CELL_HEIGHT, strtoupper($saleRequest->getVehicle()), 0, 0, 'L', true);
        $pdf->Cell(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT, ConstantsEnum::PDF_CELL_HEIGHT, '', 0, 1, 'L', true);

        $pdf->setX($total);
        $this->pdfEngineService->setStyleSize('B', 9);
        $pdf->Cell(15, ConstantsEnum::PDF_CELL_HEIGHT, 'TM', 0, 0, 'L', true);
        $this->pdfEngineService->setStyleSize('', 9);
        $pdf->Cell(44, ConstantsEnum::PDF_CELL_HEIGHT, $saleRequest->getService()->getDescription(), 0, 0, 'L', true);
        $pdf->Cell(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT, ConstantsEnum::PDF_CELL_HEIGHT, '', 0, 1, 'L', true);

        $pdf->setX($total);
        $this->pdfEngineService->setStyleSize('B', 9);
        $pdf->Cell(15, ConstantsEnum::PDF_CELL_HEIGHT, 'CHOFER', 0, 0, 'L', true);
        $this->pdfEngineService->setStyleSize('', 9);
        $pdf->Cell(44, ConstantsEnum::PDF_CELL_HEIGHT, $saleRequest->getOperator()->getShortFullName(), 0, 0, 'L', true);
        $pdf->Cell(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT, ConstantsEnum::PDF_CELL_HEIGHT, '', 0, 1, 'L', true);

        // heading title
        $pdf->Ln(ConstantsEnum::PDF_CELL_HEIGHT);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT);
        $this->pdfEngineService->setStyleSize('B', 14);
        $pdf->Cell($availableHoritzontalSpace, ConstantsEnum::PDF_CELL_HEIGHT, 'PETICIÓN DE SERVICIO', 0, 1, 'L', true);
        $pdf->Ln(ConstantsEnum::PDF_CELL_HEIGHT);

        // customer section
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT);
        $this->pdfEngineService->setStyleSize('B', 9);
        $pdf->Cell(21, ConstantsEnum::PDF_CELL_HEIGHT, 'FECHA', 0, 0, 'L', true);
        $this->pdfEngineService->setStyleSize('', 9);
        $pdf->Cell(108, ConstantsEnum::PDF_CELL_HEIGHT, $saleRequest->getServiceDateString(), 0, 0, 'L', true);
        $pdf->Cell(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT, ConstantsEnum::PDF_CELL_HEIGHT, '', 0, 1, 'L', true);

        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT);
        $this->pdfEngineService->setStyleSize('B', 9);
        $pdf->Cell(21, ConstantsEnum::PDF_CELL_HEIGHT, 'EMPRESA', 0, 0, 'L', true);
        $this->pdfEngineService->setStyleSize('', 9);
        $pdf->Cell(67, ConstantsEnum::PDF_CELL_HEIGHT, $saleRequest->getPartner()->getName(), 0, 0, 'L', true);
        $pdf->setX($pdf->GetX() + 2);
        $this->pdfEngineService->setStyleSize('B', 9);
        $pdf->Cell(10, ConstantsEnum::PDF_CELL_HEIGHT, 'SR.', 0, 0, 'L', true);
        $this->pdfEngineService->setStyleSize('', 9);
        $pdf->Cell(29, ConstantsEnum::PDF_CELL_HEIGHT, $saleRequest->getPartner()->getMainContactName(), 0, 0, 'L', true);
        $pdf->Cell(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT, ConstantsEnum::PDF_CELL_HEIGHT, '', 0, 1, 'L', true);

        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT);
        $this->pdfEngineService->setStyleSize('B', 9);
        $pdf->Cell(21, ConstantsEnum::PDF_CELL_HEIGHT, 'POBLACIÓN', 0, 0, 'L', true);
        $this->pdfEngineService->setStyleSize('', 9);
        $pdf->Cell(67, ConstantsEnum::PDF_CELL_HEIGHT, substr($saleRequest->getPartner()->getMainCityName(), 0, 30), 0, 0, 'L', true);
        $pdf->setX($pdf->GetX() + 2);
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT + $width + 20);
        $this->pdfEngineService->setStyleSize('B', 9);
        $pdf->Cell(10, ConstantsEnum::PDF_CELL_HEIGHT, 'TELF.', 0, 0, 'L', true);
        $this->pdfEngineService->setStyleSize('', 9);
        $pdf->Cell(29, ConstantsEnum::PDF_CELL_HEIGHT, $saleRequest->getPartner()->getPhoneNumber1(), 0, 0, 'L', true);
        $pdf->Cell(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT, ConstantsEnum::PDF_CELL_HEIGHT, '', 0, 1, 'L', true);

        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT);
        $this->pdfEngineService->setStyleSize('B', 9);
        $pdf->Cell(21, ConstantsEnum::PDF_CELL_HEIGHT, 'DIRECCIÓN', 0, 0, 'L', true);
        $this->pdfEngineService->setStyleSize('', 9);
        $pdf->Cell(108, ConstantsEnum::PDF_CELL_HEIGHT, $saleRequest->getPartner()->getMainAddress(), 0, 0, 'L', true);
        $pdf->Cell(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT, ConstantsEnum::PDF_CELL_HEIGHT, '', 0, 1, 'L', true);

        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT);
        $this->pdfEngineService->setStyleSize('B', 9);
        $pdf->Cell(21, ConstantsEnum::PDF_CELL_HEIGHT, 'C.I.F.', 0, 0, 'L', true);
        $this->pdfEngineService->setStyleSize('', 9);
        $pdf->Cell(67, ConstantsEnum::PDF_CELL_HEIGHT, $saleRequest->getPartner()->getCifNif(), 0, 0, 'L', true);
        $pdf->setX($pdf->GetX() + 2);
        $this->pdfEngineService->setStyleSize('B', 9);
        $pdf->Cell(14, ConstantsEnum::PDF_CELL_HEIGHT, 'F.PAGO', 0, 0, 'L', true);
        $this->pdfEngineService->setStyleSize('', 9);
        $pdf->Cell(25, ConstantsEnum::PDF_CELL_HEIGHT, strtoupper($saleRequest->getPartner()->getClass()->getName()), 0, 0, 'L', true); // TODO not reading properly attribute
        $pdf->Cell(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT, ConstantsEnum::PDF_CELL_HEIGHT, '', 0, 1, 'L', true);

        // draw horitzontal line separator
        $this->drawHoritzontalLineSeparator($pdf, $availableHoritzontalSpace);

        // work section
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT);
        $this->pdfEngineService->setStyleSize('B', 9);
        $pdf->Cell($availableHoritzontalSpace, ConstantsEnum::PDF_CELL_HEIGHT, 'TRABAJO A REALIZAR', 0, 1, 'L', true);

        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT);
        $this->pdfEngineService->setStyleSize('', 9);
        $pdf->MultiCell($availableHoritzontalSpace, 6 * ConstantsEnum::PDF_CELL_HEIGHT, $saleRequest->getServiceDescription(), 0, 'L', true, 1, '', '', true, 0, false, true, 6 * ConstantsEnum::PDF_CELL_HEIGHT);

        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT);
        $this->pdfEngineService->setStyleSize('B', 9);
        $pdf->Cell(15, ConstantsEnum::PDF_CELL_HEIGHT, 'ALTURA', 0, 0, 'L', true);
        $this->pdfEngineService->setStyleSize('', 9);
        $pdf->Cell(29, ConstantsEnum::PDF_CELL_HEIGHT, $saleRequest->getHeightString(), 0, 0, 'L', true);
        $pdf->setX($pdf->GetX() + 2);
        $this->pdfEngineService->setStyleSize('B', 9);
        $pdf->Cell(19, ConstantsEnum::PDF_CELL_HEIGHT, 'DISTANCIA', 0, 0, 'L', true);
        $this->pdfEngineService->setStyleSize('', 9);
        $pdf->Cell(23, ConstantsEnum::PDF_CELL_HEIGHT, $saleRequest->getDistanceString(), 0, 0, 'L', true);
        $pdf->setX($pdf->GetX() + 2);
        $this->pdfEngineService->setStyleSize('B', 9);
        $pdf->Cell(11, ConstantsEnum::PDF_CELL_HEIGHT, 'PESO', 0, 0, 'L', true);
        $this->pdfEngineService->setStyleSize('', 9);
        $pdf->Cell(28, ConstantsEnum::PDF_CELL_HEIGHT, $saleRequest->getWeightString(), 0, 0, 'L', true);
        $pdf->Cell(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT, ConstantsEnum::PDF_CELL_HEIGHT, '', 0, 1, 'L', true);

        // draw horitzontal line separator
        $this->drawHoritzontalLineSeparator($pdf, $availableHoritzontalSpace);

        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT);
        $this->pdfEngineService->setStyleSize('B', 9);
        $pdf->Cell($availableHoritzontalSpace, ConstantsEnum::PDF_CELL_HEIGHT, 'LUGAR DE TRABAJO', 0, 1, 'L', true);

        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT);
        $this->pdfEngineService->setStyleSize('', 9);
        $pdf->MultiCell($availableHoritzontalSpace, 4 * ConstantsEnum::PDF_CELL_HEIGHT, $saleRequest->getPlace(), 0, 'L', true, 1, '', '', true, 0, false, true, 4 * ConstantsEnum::PDF_CELL_HEIGHT);

        // draw horitzontal line separator
        $this->drawHoritzontalLineSeparator($pdf, $availableHoritzontalSpace);

        // final settings section
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT);
        $this->pdfEngineService->setStyleSize('B', 9);
        $pdf->Cell(11, ConstantsEnum::PDF_CELL_HEIGHT, 'HORA', 0, 0, 'L', true);
        $this->pdfEngineService->setStyleSize('', 9);
        $pdf->Cell(19, ConstantsEnum::PDF_CELL_HEIGHT, $saleRequest->getServiceTimeString(), 0, 0, 'L', true);
        $pdf->setX($pdf->GetX() + 2);
        $this->pdfEngineService->setStyleSize('B', 9);
        $pdf->Cell(7, ConstantsEnum::PDF_CELL_HEIGHT, 'DIA', 0, 0, 'L', true);
        $this->pdfEngineService->setStyleSize('', 9);
        $pdf->Cell(22, ConstantsEnum::PDF_CELL_HEIGHT, $saleRequest->getServiceDateString(), 0, 0, 'L', true);
        $pdf->setX($pdf->GetX() + 2);
        $this->pdfEngineService->setStyleSize('B', 9);
        $pdf->Cell(17, ConstantsEnum::PDF_CELL_HEIGHT, 'MÍNIMO H.', 0, 0, 'L', true);
        $this->pdfEngineService->setStyleSize('', 9);
        $pdf->Cell(16, ConstantsEnum::PDF_CELL_HEIGHT, $saleRequest->getMiniumHours(), 0, 0, 'L', true);
        $pdf->setX($pdf->GetX() + 2);
        $this->pdfEngineService->setStyleSize('B', 9);
        $pdf->Cell(18, ConstantsEnum::PDF_CELL_HEIGHT, 'PRECIO H.', 0, 0, 'L', true);
        $this->pdfEngineService->setStyleSize('', 9);
        $pdf->Cell(13, ConstantsEnum::PDF_CELL_HEIGHT, $saleRequest->getHourPrice(), 0, 0, 'L', true);
        $pdf->Cell(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT, ConstantsEnum::PDF_CELL_HEIGHT, '', 0, 1, 'L', true);

        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT);
        $this->pdfEngineService->setStyleSize('B', 9);
        $pdf->Cell(32, ConstantsEnum::PDF_CELL_HEIGHT, 'DESPLAZAMIENTO', 0, 0, 'L', true);
        $this->pdfEngineService->setStyleSize('', 9);
        $pdf->Cell(38, ConstantsEnum::PDF_CELL_HEIGHT, $saleRequest->getDisplacement(), 0, 0, 'L', true);
        $pdf->setX($pdf->GetX() + 2);
        $this->pdfEngineService->setStyleSize('B', 9);
        $pdf->Cell(26, ConstantsEnum::PDF_CELL_HEIGHT, 'ATENDIDO POR', 0, 0, 'L', true);
        $user = $saleRequest->getAttendedBy();
        $this->pdfEngineService->setStyleSize('', 9);
        $pdf->Cell(31, ConstantsEnum::PDF_CELL_HEIGHT, strtoupper($user->getUsername()), 0, 0, 'L', true);
        $pdf->Cell(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT, ConstantsEnum::PDF_CELL_HEIGHT, '', 0, 1, 'L', true);

        // final observations section
        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT);
        $this->pdfEngineService->setStyleSize('B', 9);
        $pdf->Cell($availableHoritzontalSpace, ConstantsEnum::PDF_CELL_HEIGHT, 'OBSERVACIONES', 0, 1, 'L', true);

        $this->pdfEngineService->setStyleSize('', 9);
        if ($saleRequest->getUtensils()) {
            $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT);
            $pdf->Cell($availableHoritzontalSpace, ConstantsEnum::PDF_CELL_HEIGHT, $saleRequest->getUtensils(), 0, 1, 'L', true);
        }

        $pdf->setX(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT);
        $pdf->MultiCell($availableHoritzontalSpace, 2 * ConstantsEnum::PDF_CELL_HEIGHT, $saleRequest->getObservations(), 0, 'L', true, 1, '', '', true, 0, false, true, 2 * ConstantsEnum::PDF_CELL_HEIGHT);

        return $pdf;
    }

    /**
     * @param TCPDF $pdf
     * @param int   $availableHoritzontalSpace
     */
    private function drawHoritzontalLineSeparator(TCPDF $pdf, $availableHoritzontalSpace)
    {
        $pdf->ln(4);
        $pdf->Line(ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT, $pdf->getY(), $availableHoritzontalSpace + ConstantsEnum::PDF_PAGE_A5_MARGIN_LEFT, $pdf->getY());
        $pdf->ln(4);
    }
}
