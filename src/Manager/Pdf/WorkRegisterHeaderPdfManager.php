<?php

namespace App\Manager\Pdf;

use App\Entity\Operator\Operator;
use App\Entity\Operator\OperatorWorkRegister;
use App\Entity\Operator\OperatorWorkRegisterHeader;
use App\Entity\Sale\SaleDeliveryNote;
use App\Enum\ConstantsEnum;
use App\Manager\RepositoriesManager;
use App\Service\Format\NumberFormatService;
use App\Service\PdfEngineService;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use TCPDF;

/**
 * Class WorkRegisterHeaderPdfManager.
 *
 * @category Manager
 */
class WorkRegisterHeaderPdfManager
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

    public function buildSingle(WorkRegisterHeader $workRegisterHeader): TCPDF
    {
        $this->pdfEngineService->initDefaultPageEngineWithTitle('Nómina detallado');
        $pdf = $this->pdfEngineService->getEngine();

        return $this->buildOnePayslipPerPage($workRegisterHeader, $pdf);
    }

    public function outputSingle(WorkRegisterHeader $workRegisterHeader): string
    {
        $pdf = $this->buildSingle($workRegisterHeader);

        return $pdf->Output('nómina_detallada'.'.pdf', 'I');
    }

    public function buildSingleTimeSum($workRegisterHeaders, $from, $to, $amount): TCPDF
    {
        $this->pdfEngineService->initDefaultPageEngineWithTitle('Plantilla horas');
        $pdf = $this->pdfEngineService->getEngine();
        $operatorsFromWorkRegisterHeaders = [];
        /** @var OperatorWorkRegisterHeader $workRegisterHeader */
        foreach ($workRegisterHeaders as $workRegisterHeader) {
            $operatorsFromWorkRegisterHeaders[$workRegisterHeader->getOperator()->getId()] = $workRegisterHeader->getOperator();
        }

        return $this->buildTimeSummary($operatorsFromWorkRegisterHeaders, $workRegisterHeaders, $from, $to, $amount, $pdf);
    }

    public function outputSingleTimeSum($workRegisterHeaders, $from, $to, $amount): string
    {
        $pdf = $this->buildSingleTimeSum($workRegisterHeaders, $from, $to, $amount);

        return $pdf->Output('plantilla_horas'.'.pdf', 'I');
    }

    /**
     * @param SaleDeliveryNote[]|ArrayCollection|array $saleDeliveryNotes
     *
     * @return TCPDF
     */
    public function buildCollection($workRegisterHeaders, $from, $to)
    {
        $this->pdfEngineService->initDefaultPageEngineWithTitle('Grupo de nóminas detalladas');
        $pdf = $this->pdfEngineService->getEngine();
        usort($workRegisterHeaders, function (OperatorWorkRegisterHeader $a, OperatorWorkRegisterHeader $b) {
            return strcasecmp($a->getOperator()->getSurname1(), $b->getOperator()->getSurname1());
        });
        $operatorsFromWorkRegisterHeaders = [];
        /** @var OperatorWorkRegisterHeader $workRegisterHeader */
        foreach ($workRegisterHeaders as $workRegisterHeader) {
            $operatorsFromWorkRegisterHeaders[$workRegisterHeader->getOperator()->getId()][] = $workRegisterHeader;
        }
        foreach ($operatorsFromWorkRegisterHeaders as $operatorId => $operatorWorkRegisterHeaders) {
            /** @var Operator $operator */
            $operator = $this->rm->getOperatorRepository()->find($operatorId);
            $pdf = $this->buildOneOperatorWorkRegisterPerPage($operatorWorkRegisterHeaders, $operator, $from, $to, $pdf);
        }

        return $pdf;
    }

    /**
     * @param SaleDeliveryNote[]|ArrayCollection|array $saleDeliveryNotes
     *
     * @return string
     */
    public function outputCollection($workRegisterHeaders, $from, $to)
    {
        $pdf = $this->buildCollection($workRegisterHeaders, $from, $to);

        return $pdf->Output('grupo_nominas_detallado.pdf', 'I');
    }

    private function buildOneOperatorWorkRegisterPerPage(array $workRegisterHeaders, $operator, $from, $to, TCPDF $pdf): TCPDF
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
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            $from,
            1, 0, 'L', true);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            $pdf->getAliasNumPage().'/'.$pdf->getAliasNbPages(),
            0, 0, 'R', true);
        $pdf->Ln();

        //Operator and period info
        $this->pdfEngineService->setStyleSize('B', 9);
        $pdf->Cell($width / 2, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            'OPERARIO: '.$operator,
            0, 0, 'L', false);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            'PERIODO: '.$from.' HASTA '.$to,
            0, 0, 'L', false);
        $pdf->Ln();

        //Start table
        $cellWidth = $width / 11;
        $pdf->Cell($cellWidth * 1, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            '',
            0, 0, 'L', false);

        $pdf->Cell($cellWidth * 3, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            'HORAS',
            1, 0, 'C', false);
        $pdf->Cell($cellWidth * 6, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            'DIETAS',
            1, 0, 'C', false);
        $pdf->Cell($cellWidth * 2, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            'EXTRAS',
            1, 0, 'C', false);
        $pdf->Ln();
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            'DÍA',
            0, 0, 'C', false);
//        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
//            'LAB.',
//            1, 0, 'L', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            'NORM.',
            1, 0, 'L', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            'EXTRA',
            1, 0, 'L', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            'NEG.',
            1, 0, 'L', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            'COMIDA',
            1, 0, 'L', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            'CENA',
            1, 0, 'L', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            'COM.I',
            1, 0, 'L', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            'CEN.I',
            1, 0, 'L', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            'DIETA',
            1, 0, 'L', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            'DIE.I',
            1, 0, 'L', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            'PERN.',
            1, 0, 'L', false);
//        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
//            'CARRET.',
//            1, 0, 'L', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            'SALIDA',
            1, 0, 'L', false);
        $pdf->ln();

        // start getting info
//        $fromDate = $payslip->getFromDate();
//        $toDate = $payslip->getToDate();
//        $workRegisterHeaders = $payslip->getOperator()->getWorkRegisterHeaders()->filter(function (OperatorWorkRegisterHeader $owrh) use ($fromDate, $toDate) {
//            return ($owrh->getDate() >= $fromDate) && ($owrh->getDate() <= $toDate);
//        });
        list($normalHourPrice, $extraHourPrice, $negativeHourPrice, $lunchPrice, $lunchIntPrice,
            $dinnerPrice, $dinnerIntPrice, $dietPrice, $dietIntPrice, $overNightPrice, $exitExtraPrice)
            = $this->getPricesForOperator($operator);

        // totals
        $totalNormalHours = 0;
        $totalExtraHours = 0;
        $totalNegativeHours = 0;
        $totalLunch = 0;
        $totalLunchInt = 0;
        $totalDinner = 0;
        $totalDinnerInt = 0;
        $totalDiet = 0;
        $totalDietInt = 0;
        $totalOverNight = 0;
//        $totalRoadExtra = 0;
        $totalExitExtra = 0;
        usort($workRegisterHeaders, function ($a, $b) {
            return $a->getDate()->getTimestamp() - $b->getDate()->getTimestamp();
        });
        /** @var OperatorWorkRegisterHeader $workRegisterHeader */
        foreach ($workRegisterHeaders as $workRegisterHeader) {
            list($normalHours, $extraHours, $negativeHours, $lunch, $lunchInt, $dinner, $dinnerInt, $diet,
                $dietInt, $overNight, $exitExtra, $workRegister, $totalNormalHours,
                $totalExtraHours, $totalNegativeHours, $totalLunch, $totalLunchInt, $totalDinner,
                $totalDinnerInt, $totalDiet, $totalDietInt, $totalOverNight, $totalExitExtra) =
                $this->getTotalsWorkRegisterHeader($workRegisterHeader, $totalNormalHours,
                    $totalExtraHours, $totalNegativeHours, $totalLunch, $totalLunchInt, $totalDinner,
                    $totalDinnerInt, $totalDiet, $totalDietInt, $totalOverNight, $totalExitExtra);
            // Draw each line, as every workReagister header refers to a date
            $this->pdfEngineService->setStyleSize('', 9);
            $pdf->setCellPaddings(1, 0, 1, 0);

            $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
                $workRegisterHeader->getDateFormatted(),
                1, 0, 'L', false);
            $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
                NumberFormatService::formatNumber($normalHours),
                1, 0, 'C', false);
            $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
                NumberFormatService::formatNumber($extraHours),
                1, 0, 'C', false);
            $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
                NumberFormatService::formatNumber($negativeHours),
                1, 0, 'C', false);
            $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
                NumberFormatService::formatNumber($lunch),
                1, 0, 'C', false);
            $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
                NumberFormatService::formatNumber($dinner),
                1, 0, 'C', false);
            $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
                NumberFormatService::formatNumber($lunchInt),
                1, 0, 'C', false);
            $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
                NumberFormatService::formatNumber($dinnerInt),
                1, 0, 'C', false);
            $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
                NumberFormatService::formatNumber($diet),
                1, 0, 'C', false);
            $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
                NumberFormatService::formatNumber($dietInt),
                1, 0, 'C', false);
            $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
                NumberFormatService::formatNumber($overNight),
                1, 0, 'C', false);
//            $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
//                $roadExtra,
//                1, 0, 'L', false);
            $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
                NumberFormatService::formatNumber($exitExtra),
                1, 0, 'C', false);
            $pdf->Ln();
        }
        // Draw sum per concept
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            'Suma',
            'B', 0, 'L', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            NumberFormatService::formatNumber($totalNormalHours),
            1, 0, 'C', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            NumberFormatService::formatNumber($totalExtraHours),
            1, 0, 'C', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            NumberFormatService::formatNumber($totalNegativeHours),
            1, 0, 'C', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            NumberFormatService::formatNumber($totalLunch),
            1, 0, 'C', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            NumberFormatService::formatNumber($totalDinner),
            1, 0, 'C', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            NumberFormatService::formatNumber($totalLunchInt),
            1, 0, 'C', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            NumberFormatService::formatNumber($totalDinnerInt),
            1, 0, 'C', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            NumberFormatService::formatNumber($totalDiet),
            1, 0, 'C', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            NumberFormatService::formatNumber($totalDietInt),
            1, 0, 'C', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            NumberFormatService::formatNumber($totalOverNight),
            1, 0, 'C', false);
//        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
//            $totalRoadExtra,
//            1, 0, 'L', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            NumberFormatService::formatNumber($totalExitExtra),
            1, 0, 'C', false);
        $pdf->Ln();

        // Draw price per concept
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            'Precio unit.',
            'B', 0, 'L', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            NumberFormatService::formatNumber($normalHourPrice),
            1, 0, 'C', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            NumberFormatService::formatNumber($extraHourPrice),
            1, 0, 'C', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            NumberFormatService::formatNumber($negativeHourPrice),
            1, 0, 'C', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            NumberFormatService::formatNumber($lunchPrice),
            1, 0, 'C', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            NumberFormatService::formatNumber($dinnerPrice),
            1, 0, 'C', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            NumberFormatService::formatNumber($lunchIntPrice),
            1, 0, 'C', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            NumberFormatService::formatNumber($dinnerIntPrice),
            1, 0, 'C', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            NumberFormatService::formatNumber($dietPrice),
            1, 0, 'C', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            NumberFormatService::formatNumber($dietIntPrice),
            1, 0, 'C', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            NumberFormatService::formatNumber($overNightPrice),
            1, 0, 'C', false);
//        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
//            $roadExtraPrice,
//            1, 0, 'L', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            NumberFormatService::formatNumber($exitExtraPrice),
            1, 0, 'C', false);
        $pdf->Ln();

        // Draw total per concept
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            'Total (€)',
            'B', 0, 'L', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            NumberFormatService::formatNumber($normalHourPrice * $totalNormalHours),
            1, 0, 'C', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            NumberFormatService::formatNumber($extraHourPrice * $totalExtraHours),
            1, 0, 'C', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            NumberFormatService::formatNumber($negativeHourPrice * $totalNegativeHours),
            1, 0, 'C', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            NumberFormatService::formatNumber($lunchPrice * $totalLunch),
            1, 0, 'C', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            NumberFormatService::formatNumber($dinnerPrice * $totalDinner),
            1, 0, 'C', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            NumberFormatService::formatNumber($lunchIntPrice * $totalLunchInt),
            1, 0, 'C', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            NumberFormatService::formatNumber($dinnerIntPrice * $totalDinnerInt),
            1, 0, 'C', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            NumberFormatService::formatNumber($dietPrice * $totalDiet),
            1, 0, 'C', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            NumberFormatService::formatNumber($dietIntPrice * $totalDietInt),
            1, 0, 'C', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            NumberFormatService::formatNumber($overNightPrice * $totalOverNight),
            1, 0, 'C', false);
//        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
//            $roadExtraPrice * $totalRoadExtra,
//            1, 0, 'L', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            NumberFormatService::formatNumber($exitExtraPrice * $totalExitExtra),
            1, 0, 'C', false);
        $pdf->Ln(10);
        $whereTableEnds = $pdf->getY();
        $finalSum = $this->getFinalSum($normalHourPrice, $totalNormalHours, $extraHourPrice, $totalExtraHours, $negativeHourPrice, $totalNegativeHours, $lunchPrice, $totalLunch, $dinnerPrice, $totalDinner, $lunchIntPrice, $totalLunchInt, $dinnerIntPrice, $totalDinnerInt, $dietPrice, $totalDiet, $dietIntPrice, $totalDietInt, $overNightPrice, $totalOverNight, $exitExtraPrice, $totalExitExtra);
        $finalDiets = $this->getFinalDiets($lunchPrice, $totalLunch, $dinnerPrice, $totalDinner, $lunchIntPrice, $totalLunchInt, $dinnerIntPrice, $totalDinnerInt, $dietPrice, $totalDiet, $dietIntPrice, $totalDietInt);
        $finalExtras = $this->getFinalExtras($normalHourPrice, $totalNormalHours, $extraHourPrice, $totalExtraHours, $negativeHourPrice, $totalNegativeHours, $overNightPrice, $totalOverNight, $exitExtraPrice, $totalExitExtra);
        //Other imports and final totals
        $this->pdfEngineService->setStyleSize('B', 9);
        $pdf->Cell($cellWidth * 6, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            'Importes varios',
            1, 0, 'C', false);
        $pdf->Ln();
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            'Fecha',
            1, 0, 'L', false);
        $pdf->Cell($cellWidth * 4, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            'Concepto',
            1, 0, 'L', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            'Total (€)',
            1, 0, 'L', false);
        $pdf->Ln();
        $totalOtherAmounts = 0;
        /** @var OperatorWorkRegisterHeader $workRegisterHeader */
        foreach ($workRegisterHeaders as $workRegisterHeader) {
            $otherAmounts = 0;
            foreach ($workRegisterHeader->getOperatorWorkRegisters() as $workRegister) {
                if (
                    !str_contains($workRegister->getDescription(), 'Hora laboral') &&
                    !str_contains($workRegister->getDescription(), 'Hora normal') &&
                    !str_contains($workRegister->getDescription(), 'Hora extra') &&
                    !str_contains($workRegister->getDescription(), 'Hora negativa') &&
                    !str_contains($workRegister->getDescription(), 'Comida') &&
                    !str_contains($workRegister->getDescription(), 'Cena') &&
                    !str_contains($workRegister->getDescription(), 'Comida internacional') &&
                    !str_contains($workRegister->getDescription(), 'Cena internacional') &&
                    !str_contains($workRegister->getDescription(), 'Dieta') &&
                    !str_contains($workRegister->getDescription(), 'Dieta internacional') &&
                    !str_contains($workRegister->getDescription(), 'Pernoctación') &&
                    !str_contains($workRegister->getDescription(), 'Plus carretera') &&
                    !str_contains($workRegister->getDescription(), 'Salida')
                ) {
                    $this->pdfEngineService->setStyleSize('', 9);

                    $otherAmounts += $workRegister->getAmount();
                    $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
                        $workRegisterHeader->getDateFormatted(),
                         1, 0, 'L', false);
                    $pdf->Cell($cellWidth * 4, ConstantsEnum::PDF_CELL_HEIGHT_SM,
                        $workRegister->getDescription(),
                        1, 0, 'L', false, '', 1);
                    $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
                        NumberFormatService::formatNumber($workRegister->getAmount()),
                         1, 0, 'C', false);
                    $pdf->Ln();
                }
            }
            $totalOtherAmounts += $otherAmounts;
        }
        $pdf->Ln();
        $pdf->SetY($whereTableEnds);
        $pdf->SetX(230);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            'Dietas:',
            0, 0, 'R', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            NumberFormatService::formatNumber($finalDiets).' €',
            0, 0, 'R', false);
        $pdf->Ln();
        $pdf->SetX(230);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            'Extras:',
            0, 0, 'R', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            NumberFormatService::formatNumber($finalExtras).' €',
            0, 0, 'R', false);
        $pdf->Ln();
        $pdf->SetX(230);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            'Importes varios',
            0, 0, 'R', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            NumberFormatService::formatNumber($totalOtherAmounts).' €',
            0, 0, 'R', false);
        $pdf->Ln();
        $pdf->SetX(230);
        $this->pdfEngineService->setStyleSize('B', 12);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            'Total (€)',
            'T', 0, 'R', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            NumberFormatService::formatNumber($totalOtherAmounts + $finalSum).' €',
            'T', 0, 'R', false);

        return $pdf;
    }

    private function buildTimeSummary($operators, $workRegisterHeaders, $from, $to, $amount, TCPDF $pdf): TCPDF
    {
        $width = $this->startPage($pdf);

        //Heading with date and page number
//        $this->pdfEngineService->setStyleSize('', 9);
        $today = new DateTime();
        $today = $today->format('d/m/Y');
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            'Plantilla horas - Grúas Romaní',
            1, 0, 'L', true);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            $pdf->getAliasNumPage().'/'.$pdf->getAliasNbPages(),
            0, 0, 'R', true);
        $pdf->Ln();

        //Period info
        $this->pdfEngineService->setStyleSize('B', 9);
        $pdf->Cell(0, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            'PERIODO: '.$from.' HASTA '.$to,
            0, 0, 'L', false);
        $pdf->Ln();

        //Start table
        $cellWidth = $width / 11;
        $this->pdfEngineService->setStyleSize('B', 9);

        $pdf->Cell($cellWidth * 5, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            'Operario',
            1, 0, 'C', false);

        $pdf->Cell($cellWidth * 2, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            'Total',
            1, 0, 'C', false);
        $pdf->Cell($cellWidth * 2, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            'Otros',
            1, 0, 'C', false);
        $pdf->Cell($cellWidth * 2, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            'Plus producción',
            1, 0, 'C', false);
        $pdf->Cell($cellWidth * 2 , ConstantsEnum::PDF_CELL_HEIGHT_SM,
            'Dietas',
            1, 0, 'C', false);
        $pdf->ln();

        $this->pdfEngineService->setStyleSize('', 9);
        usort($operators, function (Operator $a, Operator $b) {
            return strcasecmp($a->getSurname1(), $b->getSurname1());
        });
        /** @var Operator $operator */
        foreach ($operators as $operator) {
            //get prices
            list($normalHourPrice, $extraHourPrice, $negativeHourPrice, $lunchPrice, $lunchIntPrice,
                $dinnerPrice, $dinnerIntPrice, $dietPrice, $dietIntPrice, $overNightPrice, $exitExtraPrice)
                = $this->getPricesForOperator($operator);
            //get totals by workregisterheader
            $totalNormalHours = 0;
            $totalExtraHours = 0;
            $totalNegativeHours = 0;
            $totalLunch = 0;
            $totalLunchInt = 0;
            $totalDinner = 0;
            $totalDinnerInt = 0;
            $totalDiet = 0;
            $totalDietInt = 0;
            $totalOverNight = 0;
//        $totalRoadExtra = 0;
            $totalExitExtra = 0;
            $totalOtherAmounts = 0;
            $filteredWorkRegisterHedadersByOperator = array_filter($workRegisterHeaders, function ($x) use ($operator) {
                return $x->getOperator() == $operator;
            }, ARRAY_FILTER_USE_BOTH);
            /** @var OperatorWorkRegisterHeader $workRegisterHeader */
            foreach ($filteredWorkRegisterHedadersByOperator as $workRegisterHeader) {
                $otherAmounts = 0;
                list($normalHours, $extraHours, $negativeHours, $lunch, $lunchInt, $dinner, $dinnerInt, $diet,
                    $dietInt, $overNight, $exitExtra, $workRegister, $totalNormalHours,
                    $totalExtraHours, $totalNegativeHours, $totalLunch, $totalLunchInt, $totalDinner,
                    $totalDinnerInt, $totalDiet, $totalDietInt, $totalOverNight, $totalExitExtra) =
                    $this->getTotalsWorkRegisterHeader($workRegisterHeader, $totalNormalHours,
                        $totalExtraHours, $totalNegativeHours, $totalLunch, $totalLunchInt, $totalDinner,
                        $totalDinnerInt, $totalDiet, $totalDietInt, $totalOverNight, $totalExitExtra);
                $finalSum = $this->getFinalSum($normalHourPrice, $totalNormalHours, $extraHourPrice, $totalExtraHours, $negativeHourPrice, $totalNegativeHours, $lunchPrice, $totalLunch, $dinnerPrice, $totalDinner, $lunchIntPrice, $totalLunchInt, $dinnerIntPrice, $totalDinnerInt, $dietPrice, $totalDiet, $dietIntPrice, $totalDietInt, $overNightPrice, $totalOverNight, $exitExtraPrice, $totalExitExtra);
                foreach ($workRegisterHeader->getOperatorWorkRegisters() as $workRegister) {
                    if (
                        !str_contains($workRegister->getDescription(), 'Hora laboral') &&
                        !str_contains($workRegister->getDescription(), 'Hora normal') &&
                        !str_contains($workRegister->getDescription(), 'Hora extra') &&
                        !str_contains($workRegister->getDescription(), 'Hora negativa') &&
                        !str_contains($workRegister->getDescription(), 'Comida') &&
                        !str_contains($workRegister->getDescription(), 'Cena') &&
                        !str_contains($workRegister->getDescription(), 'Comida internacional') &&
                        !str_contains($workRegister->getDescription(), 'Cena internacional') &&
                        !str_contains($workRegister->getDescription(), 'Dieta') &&
                        !str_contains($workRegister->getDescription(), 'Dieta internacional') &&
                        !str_contains($workRegister->getDescription(), 'Pernoctación') &&
                        !str_contains($workRegister->getDescription(), 'Plus carretera') &&
                        !str_contains($workRegister->getDescription(), 'Salida')
                    ) {
                        $otherAmounts += $workRegister->getAmount();
                    }
                }
                $totalOtherAmounts += $otherAmounts;
            }
            $total = $finalSum + $totalOtherAmounts;
            $totaldiets = $totalLunch * $lunchPrice +
                $totalLunchInt * $lunchIntPrice +
                $totalDinner * $dinnerPrice +
                $totalDinnerInt * $dinnerIntPrice +
                $totalDiet * $dietPrice +
                $totalDietInt * $dietIntPrice;
            $calc = $total - $totaldiets;
            $others = round(($calc * $amount / 100) / 5) * 5;
//            $others = $calc*$amount/100;
            $plusprod = $calc - $others;

            //Print values
            $pdf->Cell($cellWidth * 5, ConstantsEnum::PDF_CELL_HEIGHT_SM,
                $operator,
                1, 0, 'L', false);
            $pdf->Cell($cellWidth * 2, ConstantsEnum::PDF_CELL_HEIGHT_SM,
                NumberFormatService::formatNumber($total),
                1, 0, 'C', false);
            $pdf->Cell($cellWidth * 2, ConstantsEnum::PDF_CELL_HEIGHT_SM,
                NumberFormatService::formatNumber($others),
                1, 0, 'C', false);
            $pdf->Cell($cellWidth * 2, ConstantsEnum::PDF_CELL_HEIGHT_SM,
                NumberFormatService::formatNumber($plusprod),
                1, 0, 'C', false);
            $pdf->Cell($cellWidth * 2, ConstantsEnum::PDF_CELL_HEIGHT_SM,
                NumberFormatService::formatNumber($totaldiets),
                1, 0, 'C', false);
            $pdf->ln();
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
     * @return float[]
     */
    private function getDetailedUnits(OperatorWorkRegister $workRegister, float $normalHours, float $extraHours, float $negativeHours, float $lunch, float $dinner, float $lunchInt, float $dinnerInt, float $diet, float $dietInt, float $overNight, float $exitExtra): array
    {
        if (str_contains($workRegister->getDescription(), 'Hora normal')) {
            $normalHours += $workRegister->getUnits();
        }
        if (str_contains($workRegister->getDescription(), 'Hora extra')) {
            $extraHours += $workRegister->getUnits();
        }
        if (str_contains($workRegister->getDescription(), 'Hora negativa')) {
            $negativeHours += $workRegister->getUnits();
        }
        if (str_contains($workRegister->getDescription(), 'Comida') && !str_contains($workRegister->getDescription(), 'Comida internacional')) {
            $lunch += $workRegister->getUnits();
        }
        if (str_contains($workRegister->getDescription(), 'Cena') && !str_contains($workRegister->getDescription(), 'Cena internacional')) {
            $dinner += $workRegister->getUnits();
        }
        if (str_contains($workRegister->getDescription(), 'Comida internacional')) {
            $lunchInt += $workRegister->getUnits();
        }
        if (str_contains($workRegister->getDescription(), 'Cena internacional')) {
            $dinnerInt += $workRegister->getUnits();
        }
        if (str_contains($workRegister->getDescription(), 'Dieta') && !str_contains($workRegister->getDescription(), 'Dieta internacional')) {
            $diet += $workRegister->getUnits();
        }
        if (str_contains($workRegister->getDescription(), 'Dieta internacional')) {
            $dietInt += $workRegister->getUnits();
        }
        if (str_contains($workRegister->getDescription(), 'Pernoctación')) {
            $overNight += $workRegister->getUnits();
        }
//                if (str_contains($workRegister->getDescription(), 'Plus carretera')) {
//                    $roadExtra += $workRegister->getUnits();
//                }
        if (str_contains($workRegister->getDescription(), 'Salida')) {
            $exitExtra += $workRegister->getUnits();
        }

        return [$normalHours, $extraHours, $negativeHours, $lunch, $dinner, $lunchInt, $dinnerInt, $diet, $dietInt, $overNight, $exitExtra];
    }

    /**
     * @param $operator
     *
     * @return array|int[]
     */
    private function getPricesForOperator($operator): array
    {
        $bountyGroup = $operator->getEnterpriseGroupBounty();
        $normalHourPrice = $bountyGroup ? $bountyGroup->getExtraNormalHour() : 0;
        $extraHourPrice = $bountyGroup ? $bountyGroup->getExtraExtraHour() : 0;
        $negativeHourPrice = $bountyGroup ? $bountyGroup->getNegativeHour() : 0;
        $lunchPrice = $bountyGroup ? $bountyGroup->getLunch() : 0;
        $lunchIntPrice = $bountyGroup ? $bountyGroup->getInternationalLunch() : 0;
        $dinnerPrice = $bountyGroup ? $bountyGroup->getDinner() : 0;
        $dinnerIntPrice = $bountyGroup ? $bountyGroup->getInternationalDinner() : 0;
        $dietPrice = $bountyGroup ? $bountyGroup->getDiet() : 0;
        $dietIntPrice = $bountyGroup ? $bountyGroup->getExtraNight() : 0;
        $overNightPrice = $bountyGroup ? $bountyGroup->getOverNight() : 0;
//        $roadExtraPrice = $bountyGroup ? $bountyGroup->getRoadExtraHour() : 0;
        $exitExtraPrice = $bountyGroup ? $bountyGroup->getCarOutput() : 0;

        return [$normalHourPrice, $extraHourPrice, $negativeHourPrice, $lunchPrice, $lunchIntPrice, $dinnerPrice, $dinnerIntPrice, $dietPrice, $dietIntPrice, $overNightPrice, $exitExtraPrice];
    }

    /**
     * @param $totalNormalHours
     * @param $totalExtraHours
     * @param $totalNegativeHours
     * @param $totalLunch
     * @param $totalLunchInt
     * @param $totalDinner
     * @param $totalDinnerInt
     * @param $totalDiet
     * @param $totalDietInt
     * @param $totalOverNight
     * @param $totalExitExtra
     */
    private function getTotalsWorkRegisterHeader(OperatorWorkRegisterHeader $workRegisterHeader, $totalNormalHours, $totalExtraHours, $totalNegativeHours, $totalLunch, $totalLunchInt, $totalDinner, $totalDinnerInt, $totalDiet, $totalDietInt, $totalOverNight, $totalExitExtra): array
    {
        $workRegisters = $workRegisterHeader->getOperatorWorkRegisters();
        $normalHours = 0;
        $extraHours = 0;
        $negativeHours = 0;
        $lunch = 0;
        $lunchInt = 0;
        $dinner = 0;
        $dinnerInt = 0;
        $diet = 0;
        $dietInt = 0;
        $overNight = 0;
//            $roadExtra = 0;
        $exitExtra = 0;
        $workRegister = null;
        /** @var OperatorWorkRegister $workRegister */
        foreach ($workRegisters as $workRegister) {
            list($normalHours, $extraHours, $negativeHours, $lunch, $dinner,
                $lunchInt, $dinnerInt, $diet, $dietInt, $overNight, $exitExtra) =
                $this->getDetailedUnits($workRegister, $normalHours,
                    $extraHours, $negativeHours, $lunch, $dinner, $lunchInt, $dinnerInt,
                    $diet, $dietInt, $overNight, $exitExtra);
        }
        $totalNormalHours += $normalHours;
        $totalExtraHours += $extraHours;
        $totalNegativeHours += $negativeHours;
        $totalLunch += $lunch;
        $totalLunchInt += $lunchInt;
        $totalDinner += $dinner;
        $totalDinnerInt += $dinnerInt;
        $totalDiet += $diet;
        $totalDietInt += $dietInt;
        $totalOverNight += $overNight;
//            $totalRoadExtra += $roadExtra;
        $totalExitExtra += $exitExtra;

        return [$normalHours, $extraHours, $negativeHours, $lunch, $lunchInt, $dinner, $dinnerInt, $diet, $dietInt, $overNight, $exitExtra, $workRegister, $totalNormalHours, $totalExtraHours, $totalNegativeHours, $totalLunch, $totalLunchInt, $totalDinner, $totalDinnerInt, $totalDiet, $totalDietInt, $totalOverNight, $totalExitExtra];
    }

    /**
     * @param $totalNormalHours
     * @param $totalExtraHours
     * @param $totalNegativeHours
     * @param $totalLunch
     * @param $totalDinner
     * @param $totalLunchInt
     * @param $totalDinnerInt
     * @param $totalDiet
     * @param $totalDietInt
     * @param $totalOverNight
     * @param $totalExitExtra
     *
     * @return float|int
     */
    private function getFinalSum(int $normalHourPrice, $totalNormalHours, int $extraHourPrice, $totalExtraHours, int $negativeHourPrice, $totalNegativeHours, int $lunchPrice, $totalLunch, int $dinnerPrice, $totalDinner, int $lunchIntPrice, $totalLunchInt, int $dinnerIntPrice, $totalDinnerInt, int $dietPrice, $totalDiet, int $dietIntPrice, $totalDietInt, int $overNightPrice, $totalOverNight, int $exitExtraPrice, $totalExitExtra)
    {
        $finalSum =
            $normalHourPrice * $totalNormalHours +
            $extraHourPrice * $totalExtraHours +
            $negativeHourPrice * $totalNegativeHours +
            $lunchPrice * $totalLunch +
            $dinnerPrice * $totalDinner +
            $lunchIntPrice * $totalLunchInt +
            $dinnerIntPrice * $totalDinnerInt +
            $dietPrice * $totalDiet +
            $dietIntPrice * $totalDietInt +
            $overNightPrice * $totalOverNight +
//            $roadExtraPrice * $totalRoadExtra +
            $exitExtraPrice * $totalExitExtra;

        return $finalSum;
    }

    /**
     * @param $totalLunch
     * @param $totalDinner
     * @param $totalLunchInt
     * @param $totalDinnerInt
     * @param $totalDiet
     * @param $totalDietInt
     *
     * @return float|int
     */
    private function getFinalDiets(int $lunchPrice, $totalLunch, int $dinnerPrice, $totalDinner, int $lunchIntPrice, $totalLunchInt, int $dinnerIntPrice, $totalDinnerInt, int $dietPrice, $totalDiet, int $dietIntPrice, $totalDietInt)
    {
        $finalDiets =
            $lunchPrice * $totalLunch +
            $dinnerPrice * $totalDinner +
            $lunchIntPrice * $totalLunchInt +
            $dinnerIntPrice * $totalDinnerInt +
            $dietPrice * $totalDiet +
            $dietIntPrice * $totalDietInt;

        return $finalDiets;
    }

    /**
     * @param $totalNormalHours
     * @param $totalExtraHours
     * @param $totalNegativeHours
     * @param $totalOverNight
     * @param $totalExitExtra
     *
     * @return float|int
     */
    private function getFinalExtras(int $normalHourPrice, $totalNormalHours, int $extraHourPrice, $totalExtraHours, int $negativeHourPrice, $totalNegativeHours, int $overNightPrice, $totalOverNight, int $exitExtraPrice, $totalExitExtra)
    {
        $finalExtra =
            $normalHourPrice * $totalNormalHours +
            $extraHourPrice * $totalExtraHours +
            $negativeHourPrice * $totalNegativeHours +
            $overNightPrice * $totalOverNight +
            $exitExtraPrice * $totalExitExtra;

        return $finalExtra;
    }

    private function startPage(TCPDF $pdf): int
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

        return $width;
    }
}
