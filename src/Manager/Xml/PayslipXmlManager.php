<?php

namespace App\Manager\Xml;

use App\Entity\Enterprise\EnterpriseTransferAccount;
use App\Entity\Payslip\Payslip;
use App\Service\PdfEngineService;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class PayslipXmlManager.
 *
 * @category Manager
 */
class PayslipXmlManager
{
    /**
     * Methods.
     */
    //    public function __construct(PdfEngineService $pdfEngineService)
    //    {
    //        $this->pdfEngineService = $pdfEngineService;
    //    }

    /**
     * @param Payslip[]|ArrayCollection|array $payslips
     *
     * @return string
     */
    public function buildSingle($payslips, $diets)
    {
        return $this->buildPayslipXml($payslips, $diets);
    }

    /**
     * @param Payslip[]|ArrayCollection|array $payslips
     *
     * @return string
     */
    public function outputSingle($payslips, $diets)
    {
        $xmlDoc = $this->buildSingle($payslips, $diets);

        return $xmlDoc;
    }

    /**
     * @param Payslip[]|ArrayCollection|array $payslips
     *
     * @return string
     */
    private function buildPayslipXml($payslips, $diets)
    {
        $date = new DateTime();
//        $today = date('Y/m/d');
        $today = $date->format('Y/m/d');
        $totalPayment = 0;
        $totalTransactions = 0;
        /** @var Payslip $payslip * */
        foreach ($payslips as $payslip) {
            if ( $diets ){
                $totalPayment = $payslip->getExpenses() + $totalPayment;
            } else {
                $totalPayment = $payslip->getTotalAmount() + $totalPayment;
            }
            $totalTransactions = $totalTransactions + 1 ;
        }
        $company = $payslip->getOperator()->getEnterprise()->getName();
        $NIFSuf = $payslip->getOperator()->getEnterprise()->getTaxIdentificationNumber().'SSS';
        /** @var EnterpriseTransferAccount $eta */
        $eta = $payslip->getOperator()->getEnterprise()->getEnterpriseTransferAccounts()->filter(function (EnterpriseTransferAccount $eta) {
            return 'La Caixa' === $eta->getName();
        })->first();
        $IBAN = $eta->getIban().$eta->getBankCode().$eta->getOfficeNumber().$eta->getControlDigit().$eta->getAccountNumber();
        $swift = $eta->getSwift();
        $fileId = $date->getTimestamp();
        $xmlDocStart =
            '<Document xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns="urn:iso:std:iso:20022:tech:xsd:pain.001.001.03">
            <CstmrCdtTrfInitn>
                <GrpHdr>
                    <MsgId>GR-'.$fileId.'</MsgId>
                    <CreDtTm>'.$today.'</CreDtTm>
                    <NbOfTxs>'.$totalTransactions.'</NbOfTxs>
                    <CtrlSum>'.$totalPayment.'</CtrlSum>
                    <InitgPty>
                        <Nm>'.$company.'</Nm>
                        <Id>
                            <OrgId>
                                <Othr>
                                    <Id>'.$NIFSuf.'</Id>
                                </Othr>
                            </OrgId>
                        </Id>
                    </InitgPty>
                </GrpHdr>
                <PmtInf>
                    <PmtInfId>GR-'.$fileId.'</PmtInfId>
                    <PmtMtd>TRF</PmtMtd>
                    <NbOfTxs>'.$totalTransactions.'</NbOfTxs>
                    <CtrlSum>'.$totalPayment.'</CtrlSum>
                    <PmtTpInf>
                        <InstrPrty>NORM</InstrPrty>
                        <SvcLvl>
                            <Cd>SEPA</Cd>
                        </SvcLvl>
                        <CtgyPurp>
                            <Cd>SALA</Cd>
                        </CtgyPurp>
                    </PmtTpInf>
                    <ReqdExctnDt>'.$today.'</ReqdExctnDt>
                    <Dbtr>
                        <Nm>'.$company.'</Nm>
                        <Id>
                            <OrgId>
                                <Othr>
                                    <Id>'.$NIFSuf.'</Id>
                                </Othr>
                            </OrgId>
                        </Id>
                    </Dbtr>
                    <DbtrAcct>
                        <Id>
                            <IBAN>'.$IBAN.'</IBAN>
                        </Id>
                    </DbtrAcct>
                    <DbtrAgt>
                        <FinInstnId>
                            <BIC>'.$swift.'</BIC>
                        </FinInstnId>
                    </DbtrAgt>
                    <ChrgBr>DEBT</ChrgBr>
                    <CdtTrfTxInf>
                    ';
        $xmlDocDetail = '';
        foreach ($payslips as $payslip) {
            $operator = $payslip->getOperator()->getFullName();
            $opIBAN = $payslip->getOperator()->getBancAccountNumber();
            if($diets){
                $amount = $payslip->getExpenses();
            } else{
                $amount = $payslip->getTotalAmount();
            }
            $intervalDate = 'Nómina desde '.$payslip->getFromDateFormatted().' hasta '.$payslip->getToDateFormatted();
            $xmlDocDetail = $xmlDocDetail.
            '            <PmtId>
                <InstrId>GR-'.$fileId.'-'.$payslip->getOperator()->getId().'</InstrId>
                <EndToEndId>GR-'.$fileId.'-'.$payslip->getOperator()->getId().'</EndToEndId>
            </PmtId>
            <Amt>
                <InstdAmt Ccy="EUR">'.$amount.'</InstdAmt>
            </Amt>
            <Cdtr>
                <Nm>'.$operator.'</Nm>
            </Cdtr>
            <CdtrAcct>
                <Id>
                    <IBAN>'.$opIBAN.'</IBAN>
                </Id>
            </CdtrAcct>
            <RmtInf>
                <Ustrd>'.$intervalDate.'</Ustrd>
            </RmtInf>
            '
            ;
        }
        $xmlDocEnd = '</CdtTrfTxInf>
                </PmtInf>
            </CstmrCdtTrfInitn>
        </Document>
        ';

        return $xmlDocStart.$xmlDocDetail.$xmlDocEnd;
    }
}
