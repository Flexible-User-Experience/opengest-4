<?php

namespace App\Manager\Xml;

use App\Entity\Operator\OperatorWorkRegister;
use App\Entity\Operator\OperatorWorkRegisterHeader;
use App\Entity\Payslip\Payslip;
use App\Entity\Payslip\PayslipLine;
use App\Enum\ConstantsEnum;
use App\Service\PdfEngineService;
use Doctrine\Common\Collections\ArrayCollection;
use TCPDF;

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
    public function buildSingle($payslips)
    {
        return $this->buildPayslipXml($payslips);
    }

    /**
     * @param Payslip[]|ArrayCollection|array $payslips
     *
     * @return string
     */
    public function outputSingle($payslips)
    {
        $xmlDoc = $this->buildSingle($payslips);

        return $xmlDoc;
    }

    /**
     * @param Payslip[]|ArrayCollection|array $payslips
     *
     * @return string
     */
    private function buildPayslipXml($payslips)
    {
        $today = date('Y/m/d');
        $totalPayment = 0;
        $totalTransactions = 0;
        /** @var Payslip $payslip * */
        foreach ($payslips as $payslip) {
            $totalPayment = $payslip->getTotalAmount() + $totalPayment;
            $totalTransactions = $totalTransactions++;
        }
        $company = $payslip->getOperator()->getEnterprise()->getName();
        $NIFSuf = $payslip->getOperator()->getEnterprise()->getTaxIdentificationNumber() . 'SSS';
        $IBAN = '';


        $xmlDocStart = `
        <Document xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns="urn:iso:std:iso:20022:tech:xsd:pain.001.001.03">
            <CstmrCdtTrfInitn>
                <GrpHdr>
                    <MsgId>1.1 Referencia identificativa del fichero</MsgId>
                    <CreDtTm>`.$today.`</CreDtTm>
                    <NbOfTxs>`.$totalTransactions.`</NbOfTxs>
                    <CtrlSum>`.$totalPayment.`</CtrlSum>
                    <InitgPty>
                        <Nm>`.$company.`</Nm>
                        <Id>
                            <OrgId>
                                <Othr>
                                    <Id>`.$NIFSuf.`</Id>
                                </Othr>
                            </OrgId>
                        </Id>
                    </InitgPty>
                </GrpHdr>
                <PmtInf>
                    <PmtInfId>2.1 Identificación de Información del pago – unívoca e irrepetible en un mismo fichero</PmtInfId>
                    <PmtMtd>TRF</PmtMtd>
                    <BtchBookg>2.3 Indicador de apunte en cuenta</BtchBookg>
                    <NbOfTxs>`.$totalTransactions.`</NbOfTxs>
                    <CtrlSum>`.$totalPayment.`</CtrlSum>
                    <PmtTpInf>
                        <InstrPrty>NORM</InstrPrty>
                        <SvcLvl>
                            <Cd>SEPA</Cd>
                        </SvcLvl>
                        <CtgyPurp>
                            <Cd>SALA</Cd>
                        </CtgyPurp>
                    </PmtTpInf>
                    <ReqdExctnDt>`.$today.`</ReqdExctnDt>
                    <Dbtr>
                        <Nm>`.$company.`</Nm>
                        <PstlAdr>
                            <Ctry>2.19 País según código ISO 3166 Alpha-2</Ctry>
                            <AdrLine>2.19 Dirección en texto libre hasta 70 caracteres</AdrLine>
                            <AdrLine>2.19 Dirección en texto libre hasta 70 caracteres</AdrLine>
                        </PstlAdr>
                        <Id>
                            <OrgId>
                                <Othr>
                                    <Id>`.$NIFSuf.`</Id>
                                </Othr>
                            </OrgId>
                        </Id>
                    </Dbtr>
                    <DbtrAcct>
                        <Id>
                            <IBAN>`.$IBAN.`</IBAN>
                        </Id>
                    </DbtrAcct>
                    <DbtrAgt>
                        <FinInstnId>
                            <BIC>CAIXESBBXXX</BIC>
                        </FinInstnId>
                    </DbtrAgt>
                    <ChrgBr>DEBT</ChrgBr>
                    <CdtTrfTxInf>
                    `;
                    foreach($payslips as $payslip){
                        $operator = $payslip->getOperator()->getFullName();
                        $opIBAN = $payslip->getOperator()->getBancAccountNumber();
                        $amount = $payslip->getTotalAmount();
                        $intervalDate = 'Nómina desde '.$payslip->getFromDateFormatted().' hasta '.$payslip->getToDateFormatted();
                        $xmlDocDetail = `
                        <PmtId>
                            <InstrId>2.29 Referencia única ordenante para identificar la operación hasta 35 caracteres</InstrId>
                            <EndToEndId>2.30 Referencia única para beneficiario hsta 35 caracteres</EndToEndId>
                        </PmtId>
                        <Amt>
                            <InstdAmt Ccy="EUR">`.$amount.`</InstdAmt>
                        </Amt>
                        <CdtrAgt>
                            <FinInstnId>
                                <BIC>2.77 BIC de la entidad del beneficiario</BIC>
                            </FinInstnId>
                        </CdtrAgt>
                        <Cdtr>
                            <Nm>`.$operator.`</Nm>
                        </Cdtr>
                        <CdtrAcct>
                            <Id>
                                <IBAN>`.$opIBAN.`</IBAN>
                            </Id>
                        </CdtrAcct>
                        <RmtInf>
                            <Ustrd>`.$intervalDate.`</Ustrd>
                        </RmtInf>
                        `;
                    }
                    $xmlDocEnd = `
                    </CdtTrfTxInf>
                </PmtInf>
            </CstmrCdtTrfInitn>
        </Document>
        `;

                    $xmlDoc = $xmlDocStart.$xmlDocDetail.$xmlDocEnd;


        return $xmlDoc;
    }
}