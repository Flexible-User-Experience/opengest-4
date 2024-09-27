<?php

namespace App\Manager\Pdf;

use App\Entity\Operator\OperatorWorkRegister;
use App\Entity\Operator\OperatorWorkRegisterHeader;
use App\Entity\Payslip\Payslip;
use App\Entity\Payslip\PayslipLine;
use App\Enum\ConstantsEnum;
use App\Service\PdfEngineService;
use TCPDF;

/**
 * Class PaymentReceiptPdfManager.
 *
 * @category Manager
 */
class PaymentReceiptPdfManager
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
     * @param Payslip[]|ArrayCollection|array $payslips
     *
     * @return string
     */
    public function buildSingle($payslips, $diets, $date): string
    {
        $this->pdfEngineService->initDefaultPageEngineWithTitle('Listado recibos');
        $pdf = $this->pdfEngineService->getEngine();

        return $this->buildPayslipReceipts($payslips, $diets, $date, $pdf);
    }

    /**
     * @param Payslip[]|ArrayCollection|array $payslips
     *
     * @return string
     */
    public function outputSingle($payslips, $diets, $date): string
    {
        $pdf = $this->buildSingle($payslips, $diets, $date);

        return $pdf->Output('Listdo Recibos'.'.pdf','I');
    }

    /**
     * @param Payslip[]|ArrayCollection|array $payslips
     *
     * @return string
     */
    private function buildPayslipReceipts($payslips, $diets, $date, $pdf): string
    {

        usort($payslips, function (Payslip $a, Payslip $b) {
            return strcasecmp($a->getOperator()->getSurname1(), $b->getOperator()->getSurname1());
        });
        // add start page
        list($spaceBetween, $receiptSize, $pageWidth, $marginRight, $marginLeft) = $this->addStartPage($pdf);
        //START GENERATING RECEIPTS
        /** @var Payslip $payslip */
        foreach($payslips as $payslip){
            if($diets && $payslip->getExpenses() > 0){
                if($pdf->GetY() > 280){
                    list($spaceBetween, $receiptSize, $pageWidth, $marginRight, $marginLeft) = $this->addStartPage($pdf);
                }
                $style = array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 255));
                $pdf->SetLineStyle($style);
                $pdf->RoundedRect($pdf->GetX(), $pdf->GetY(), $pageWidth, $receiptSize, 3.50, '1111', '');
                // generate receipt format
                $xStartWriting = 25;
                $pdf->SetXY($xStartWriting, $pdf->GetY() + 8);
                $pdf->SetFont(ConstantsEnum::PDF_DEFAULT_FONT, 'b', 15);
                $pdf->Cell(80, 0,
                    'RECIBO',
                    0, 0, 'L', 0);
                $pdf->SetFont(ConstantsEnum::PDF_DEFAULT_FONT, '', 15);
                $pdf->Cell(25, 0,
                    'Importe: ',
                    0, 0, 'L', 0);
                $pdf->SetFont(ConstantsEnum::PDF_DEFAULT_FONT, 'b', 15);
                $pdf->Cell(40, 0,
                    number_format($payslip->getExpenses(),2,',','.').'€',
                    0, 0, 'L', 1);
                $pdf->SetXY($xStartWriting, $pdf->GetY() + 15);
                $pdf->SetFont(ConstantsEnum::PDF_DEFAULT_FONT, 'b', 15);
                $pdf->Cell(90, 0,
                    $payslip->getOperator()->getFullName(),
                    0, 0, 'L', 0);
                $pdf->SetFont(ConstantsEnum::PDF_DEFAULT_FONT, '', 15);
                $pdf->Cell(20, 0,
                    'Fecha: ',
                    0, 0, 'L', 0);
                $pdf->SetFont(ConstantsEnum::PDF_DEFAULT_FONT, 'b', 15);
                $pdf->Cell(40, 0,
                    date_format($date,'d/m/Y'),
                    0, 0, 'L', 0);
                $pdf->Ln();
                $pdf->Line($marginLeft, $pdf->GetY(), $pageWidth + $marginLeft, $pdf->GetY(), $style);
                $pdf->SetFont(ConstantsEnum::PDF_DEFAULT_FONT, '', 15);
                $pdf->SetXY($xStartWriting, $pdf->GetY() + 10);
                $pdf->Cell(40, 0,
                    'Recibo de: Grúas Romaní',
                    0, 1, 'L', 0);
                $pdf->SetX($xStartWriting);
                //TODO escriure el nom del mes sencer
                $pdf->Cell(40, 0,
                    'Por: Dietas mes de '.date_format($payslip->getToDate(),'M'),
                    0, 0, 'L', 0);
                $pdf->SetFont(ConstantsEnum::PDF_DEFAULT_FONT, 'b', 10);

                $squareY =  $pdf->GetY()-10.2;
                $squareX = 135;
                $pdf->SetXY($squareX, $squareY);
                $pdf->Cell(50,0,
                'FIRMA',
                0,1,'C',0);
                $pdf->RoundedRect($squareX, $squareY, 55, 30, 3.50, '0100', '');

                $pdf->Ln($spaceBetween);
            } elseif(!$diets && $payslip->getOtherCosts() > 0){
                if($pdf->GetY() > 280){
                    list($spaceBetween, $receiptSize, $pageWidth, $marginRight, $marginLeft) = $this->addStartPage($pdf);
                }
                $style = array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 255));
                $pdf->SetLineStyle($style);
                $pdf->RoundedRect($pdf->GetX(), $pdf->GetY(), $pageWidth, $receiptSize, 3.50, '1111', '');
                //TODO generate receipt format
                $xStartWriting = 25;
                $pdf->SetXY($xStartWriting, $pdf->GetY() + 8);
                $pdf->SetFont(ConstantsEnum::PDF_DEFAULT_FONT, 'b', 15);
                $pdf->Cell(80, 0,
                    'RECIBO',
                    0, 0, 'L', 0);
                $pdf->SetFont(ConstantsEnum::PDF_DEFAULT_FONT, '', 15);
                $pdf->Cell(25, 0,
                    'Importe: ',
                    0, 0, 'L', 0);
                $pdf->SetFont(ConstantsEnum::PDF_DEFAULT_FONT, 'b', 15);
                $pdf->Cell(40, 0,
                    number_format($payslip->getOtherCosts(),2,',','.').'€',
                    0, 0, 'L', 1);
                $pdf->SetXY($xStartWriting, $pdf->GetY() + 15);
                $pdf->SetFont(ConstantsEnum::PDF_DEFAULT_FONT, 'b', 15);
                $pdf->Cell(90, 0,
                    $payslip->getOperator()->getFullName(),
                    0, 0, 'L', 0);
                $pdf->SetFont(ConstantsEnum::PDF_DEFAULT_FONT, '', 15);
                $pdf->Cell(20, 0,
                    'Fecha: ',
                    0, 0, 'L', 0);
                $pdf->SetFont(ConstantsEnum::PDF_DEFAULT_FONT, 'b', 15);
                $pdf->Cell(40, 0,
                    date_format($date,'d/m/Y'),
                    0, 0, 'L', 0);
                $pdf->Ln();
                $pdf->Line($marginLeft, $pdf->GetY(), $pageWidth + $marginLeft, $pdf->GetY(), $style);
                $pdf->SetFont(ConstantsEnum::PDF_DEFAULT_FONT, '', 15);
                $pdf->SetXY($xStartWriting, $pdf->GetY() + 10);
                $pdf->Cell(40, 0,
                    'Recibo de: Grúas Romaní',
                    0, 1, 'L', 0);
                $pdf->SetX($xStartWriting);
                //TODO escriure el nom del mes sencer
                $pdf->Cell(40, 0,
                    'Por: Otros costes del mes de '.date_format($payslip->getToDate(),'M'),
                    0, 0, 'L', 0);
                $pdf->SetFont(ConstantsEnum::PDF_DEFAULT_FONT, 'b', 10);

                $squareY =  $pdf->GetY()-10.2;
                $squareX = 135;
                $pdf->SetXY($squareX, $squareY);
                $pdf->Cell(50,0,
                    'FIRMA',
                    0,1,'C',0);
                $pdf->RoundedRect($squareX, $squareY, 55, 30, 3.50, '0100', '');

                $pdf->Ln($spaceBetween);
            }
        }

        return $pdf;
    }

    /**
     * @param $pdf
     * @return int[]
     */
    private function addStartPage($pdf): array
    {
        $marginTop = 15;
        $marginLeft = 20;
        $marginRight = 20;
        $spaceBetween = 45;
        $receiptSize = 70;
        $pageWidth = $pdf->getPageWidth() -  $marginRight - $marginLeft;
        $pdf->setMargins($marginLeft, $marginTop, $marginRight, true);
        $pdf->AddPage(ConstantsEnum::PDF_PORTRAIT_PAGE_ORIENTATION, ConstantsEnum::PDF_PAGE_A4);
        $pdf->SetFont(ConstantsEnum::PDF_DEFAULT_FONT, '', 9);
        $pdf->setCellPaddings(1, 1, 1, 1);
        $pdf->setTextColor(0, 0, 255);
        $pdf->setFillColor(204, 229, 255);
        $pdf->setColor( 0, 0, 255);
        return array($spaceBetween, $receiptSize, $pageWidth, $marginRight, $marginLeft);


//        // get the current page break margin
//        $bMargin = $pdf->getBreakMargin();
//        // get current auto-page-break mode
//        $auto_page_break = $pdf->getAutoPageBreak();
//        // disable auto-page-break
//        $pdf->SetAutoPageBreak(false, 0);
//        // restore auto-page-break status
//        $pdf->SetAutoPageBreak($auto_page_break, $bMargin);
//        // set the starting point for the page content
//        $pdf->setPageMark();
//        // set cell padding
    }

}
