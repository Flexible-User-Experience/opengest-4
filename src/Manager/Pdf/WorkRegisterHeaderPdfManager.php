<?php

namespace App\Manager\Pdf;

use App\Entity\Operator\Operator;
use App\Entity\Operator\OperatorWorkRegister;
use App\Entity\Operator\OperatorWorkRegisterHeader;
use App\Entity\Sale\SaleDeliveryNote;
use App\Enum\ConstantsEnum;
use App\Manager\RepositoriesManager;
use App\Service\PdfEngineService;
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

    /**
     * @param SaleDeliveryNote[]|ArrayCollection|array $saleDeliveryNotes
     *
     * @return TCPDF
     */
    public function buildCollection($workRegisterHeaders, $from, $to)
    {
        $this->pdfEngineService->initDefaultPageEngineWithTitle('Grupo de nóminas detalladas');
        $pdf = $this->pdfEngineService->getEngine();
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
        $bountyGroup = $operator->getEnterpriseGroupBounty();
        $workingHourPrice = $bountyGroup ? $bountyGroup->getNormalHour() : 0;
        $normalHourPrice = $bountyGroup ? $bountyGroup->getExtraNormalHour() : 0;
        $extraHourPrice = $bountyGroup ? $bountyGroup->getExtraExtraHour() : 0;
        $negativeHourPrice = $bountyGroup ? $bountyGroup->getNegativeHour() : 0;
        $lunchPrice = $bountyGroup ? $bountyGroup->getLunch() : 0;
        $lunchIntPrice = $bountyGroup ? $bountyGroup->getDinner() : 0;
        $dinnerPrice = $bountyGroup ? $bountyGroup->getInternationalLunch() : 0;
        $dinnerIntPrice = $bountyGroup ? $bountyGroup->getInternationalDinner() : 0;
        $dietPrice = $bountyGroup ? $bountyGroup->getDiet() : 0;
        $dietIntPrice = $bountyGroup ? $bountyGroup->getExtraNight() : 0;
        $overNightPrice = $bountyGroup ? $bountyGroup->getOverNight() : 0;
//        $roadExtraPrice = $bountyGroup ? $bountyGroup->getRoadExtraHour() : 0;
        $exitExtraPrice = $bountyGroup ? $bountyGroup->getCarOutput() : 0;

        // totals
        $totalWorkingHours = 0;
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
        /** @var OperatorWorkRegisterHeader $workRegisterHeader */
        foreach ($workRegisterHeaders as $workRegisterHeader) {
            $workRegisters = $workRegisterHeader->getOperatorWorkRegisters();
            $workingHours = 0;
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
            /** @var OperatorWorkRegister $workRegister */
            foreach ($workRegisters as $workRegister) {
                if (str_contains($workRegister->getDescription(), 'Hora laboral')) {
                    $workingHours += $workRegister->getUnits();
                }
                if (str_contains($workRegister->getDescription(), 'Hora normal')) {
                    $normalHours += $workRegister->getUnits();
                }
                if (str_contains($workRegister->getDescription(), 'Hora extra')) {
                    $extraHours += $workRegister->getUnits();
                }
                if (str_contains($workRegister->getDescription(), 'Hora negativa')) {
                    $negativeHours += $workRegister->getUnits();
                }
                if (str_contains($workRegister->getDescription(), 'Comida')) {
                    $lunch += $workRegister->getUnits();
                }
                if (str_contains($workRegister->getDescription(), 'Cena')) {
                    $dinner += $workRegister->getUnits();
                }
                if (str_contains($workRegister->getDescription(), 'Comida internacional')) {
                    $lunchInt += $workRegister->getUnits();
                }
                if (str_contains($workRegister->getDescription(), 'Cena internacional')) {
                    $dinnerInt += $workRegister->getUnits();
                }
                if (str_contains($workRegister->getDescription(), 'Dieta')) {
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
            }
            $totalWorkingHours += $workingHours;
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
            // Draw each line, as every workReagister header refers to a date
            $this->pdfEngineService->setStyleSize('', 9);
            $pdf->setCellPaddings(1, 0, 1, 0);

            $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
                $workRegisterHeader->getDateFormatted(),
                1, 0, 'L', false);
//            $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
//                $workingHours,
//                1, 0, 'L', false);
            $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
                number_format($normalHours,'0',',','.'),
                1, 0, 'C', false);
            $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
                number_format($extraHours,'0',',','.'),
                1, 0, 'C', false);
            $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
                number_format($negativeHours,'0',',','.'),
                1, 0, 'C', false);
            $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
                number_format($lunch,'0',',','.'),
                1, 0, 'C', false);
            $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
                number_format($dinner,'0',',','.'),
                1, 0, 'C', false);
            $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
                number_format($lunchInt,'0',',','.'),
                1, 0, 'C', false);
            $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
                number_format($dinnerInt,'0',',','.'),
                1, 0, 'C', false);
            $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
                number_format($diet,'0',',','.'),
                1, 0, 'C', false);
            $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
                number_format($dietInt,'0',',','.'),
                1, 0, 'C', false);
            $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
                number_format($overNight,'0',',','.'),
                1, 0, 'C', false);
//            $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
//                $roadExtra,
//                1, 0, 'L', false);
            $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
                number_format($exitExtra,'0',',','.'),
                1, 0, 'C', false);
            $pdf->Ln();
        }
        // Draw sum per concept
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            'Suma',
            'B', 0, 'L', false);
//        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
//            $totalWorkingHours,
//            1, 0, 'L', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            number_format($totalNormalHours,'0',',','.'),
            1, 0, 'C', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            number_format($totalExtraHours,'0',',','.'),
            1, 0, 'C', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            number_format($totalNegativeHours,'0',',','.'),
            1, 0, 'C', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            number_format($totalLunch,'0',',','.'),
            1, 0, 'C', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            number_format($totalDinner,'0',',','.'),
            1, 0, 'C', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            number_format($totalLunchInt,'0',',','.'),
            1, 0, 'C', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            number_format($totalDinnerInt,'0',',','.'),
            1, 0, 'C', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            number_format($totalDiet,'0',',','.'),
            1, 0, 'C', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            number_format($totalDietInt,'0',',','.'),
            1, 0, 'C', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            number_format($totalOverNight,'0',',','.'),
            1, 0, 'C', false);
//        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
//            $totalRoadExtra,
//            1, 0, 'L', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            number_format($totalExitExtra,'0',',','.'),
            1, 0, 'C', false);
        $pdf->Ln();

        // Draw price per concept
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            'Precio unit.',
            'B', 0, 'L', false);
//        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
//            $workingHourPrice,
//            1, 0, 'L', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            number_format($normalHourPrice,'2',',','.'),
            1, 0, 'C', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            number_format($extraHourPrice,'2',',','.'),
            1, 0, 'C', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            number_format($negativeHourPrice,'2',',','.'),
            1, 0, 'C', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            number_format($lunchPrice,'2',',','.'),
            1, 0, 'C', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            number_format($dinnerPrice,'2',',','.'),
            1, 0, 'C', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            number_format($lunchIntPrice,'2',',','.'),
            1, 0, 'C', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            number_format($dinnerIntPrice,'2',',','.'),
            1, 0, 'C', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            number_format($dietPrice,'2',',','.'),
            1, 0, 'C', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            number_format($dietIntPrice,'2',',','.'),
            1, 0, 'C', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            number_format($overNightPrice,'2',',','.'),
            1, 0, 'C', false);
//        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
//            $roadExtraPrice,
//            1, 0, 'L', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            number_format($exitExtraPrice,'2',',','.'),
            1, 0, 'C', false);
        $pdf->Ln();

        // Draw total per concept
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            'Total (€)',
            'B', 0, 'L', false);
//        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
//            $workingHourPrice * $totalWorkingHours,
//            1, 0, 'L', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            number_format($normalHourPrice * $totalNormalHours,'2',',','.'),
            1, 0, 'C', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            number_format($extraHourPrice * $totalExtraHours,'2',',','.'),
            1, 0, 'C', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            number_format($negativeHourPrice * $totalNegativeHours,'2',',','.'),
            1, 0, 'C', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            number_format($lunchPrice * $totalLunch,'2',',','.'),
            1, 0, 'C', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            number_format($dinnerPrice * $totalDinner,'2',',','.'),
            1, 0, 'C', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            number_format($lunchIntPrice * $totalLunchInt,'2',',','.'),
            1, 0, 'C', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            number_format($dinnerIntPrice * $totalDinnerInt,'2',',','.'),
            1, 0, 'C', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            number_format($dietPrice * $totalDiet,'2',',','.'),
            1, 0, 'C', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            number_format($dietIntPrice * $totalDietInt,'2',',','.'),
            1, 0, 'C', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            number_format($overNightPrice * $totalOverNight,'2',',','.'),
            1, 0, 'C', false);
//        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
//            $roadExtraPrice * $totalRoadExtra,
//            1, 0, 'L', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            number_format($exitExtraPrice * $totalExitExtra,'2',',','.'),
            1, 0, 'C', false);
        $pdf->Ln(10);
        $finalSum = $workingHourPrice * $totalWorkingHours +
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
            'Total',
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
//                    !str_contains($workRegister->getDescription(), 'Plus carretera') &&
                    !str_contains($workRegister->getDescription(), 'Salida')
                ) {
                    $this->pdfEngineService->setStyleSize('', 9);

                    $otherAmounts += $workRegister->getAmount();
                    $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
                        $workRegisterHeader->getDateFormatted(),
                         1, 0,'L', false);
                    $pdf->Cell($cellWidth *4, ConstantsEnum::PDF_CELL_HEIGHT_SM,
                        $workRegister->getDescription(),
                        1,  0,'L', false,'',1);
                    $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
                        number_format($workRegister->getAmount(),'2',',','.'),
                         1, 0,'C', false);
                    $pdf->Ln();
                }
            }
            $totalOtherAmounts += $otherAmounts;
        }
        $pdf->Ln();
        $pdf->SetX(230);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            'Suma:',
            0, 0, 'R', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            number_format($finalSum,'2',',','.').' €',
            0, 0, 'R', false);
        $pdf->Ln();
        $pdf->SetX(230);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            'Importes varios',
            0, 0, 'R', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            number_format($totalOtherAmounts,'2',',','.').' €',
            0, 0, 'R', false);
        $pdf->Ln();
        $pdf->SetX(230);
        $this->pdfEngineService->setStyleSize('B', 12);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            'Total (€)',
            'T', 0, 'R', false);
        $pdf->Cell($cellWidth, ConstantsEnum::PDF_CELL_HEIGHT_SM,
            number_format($totalOtherAmounts + $finalSum,'2',',','.').' €',
            'T', 0, 'R', false);

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
