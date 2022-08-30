<?php

namespace App\Controller\Admin;

use App\Manager\CostManager;
use App\Manager\DeliveryNoteManager;
use App\Manager\EnterpriseHolidayManager;
use App\Manager\InvoiceManager;
use App\Manager\Pdf\DocumentationPdfManager;
use App\Manager\Pdf\OperatorCheckingPdfManager;
use App\Manager\Pdf\PaymentReceiptPdfManager;
use App\Manager\Pdf\PayslipPdfManager;
use App\Manager\Pdf\SaleDeliveryNotePdfManager;
use App\Manager\Pdf\SaleInvoicePdfManager;
use App\Manager\Pdf\VehicleCheckingPdfManager;
use App\Manager\Pdf\WorkRegisterHeaderPdfManager;
use App\Manager\VehicleMaintenanceManager;
use App\Manager\Xls\ImputableCostXlsManager;
use App\Manager\Xls\MarginAnalysisXlsManager;
use App\Manager\Xls\OperatorWorkRegisterHeaderXlsManager;
use App\Manager\Xml\PayslipXmlManager;
use Doctrine\Persistence\ManagerRegistry;
use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Vich\UploaderBundle\Exception\NoFileFoundException;
use Vich\UploaderBundle\Handler\DownloadHandler;

/**
 * Class BaseAdminController.
 *
 * @category Controller
 *
 * @author   David Romaní <david@flux.cat>
 */
abstract class BaseAdminController extends Controller
{
    protected InvoiceManager $im;

    protected CostManager $costManager;

    protected DeliveryNoteManager $deliveryNoteManager;

    protected ImputableCostXlsManager $imputableCostXlsManager;

    protected MarginAnalysisXlsManager $marginAnalysisXlsManager;

    protected SaleDeliveryNotePdfManager $sdnpm;

    protected SaleInvoicePdfManager $sipm;

    protected WorkRegisterHeaderPdfManager $wrhpm;

    protected OperatorWorkRegisterHeaderXlsManager $operatorWorkRegisterHeaderXlsManager;

    protected PayslipPdfManager $ppm;

    protected PayslipXmlManager $pxm;

    protected PaymentReceiptPdfManager $paymentReceiptPdfManager;

    protected OperatorCheckingPdfManager $operatorCheckingPdfManager;

    protected DocumentationPdfManager $documentationPdfManager;

    protected VehicleCheckingPdfManager $vehicleCheckingPdfManager;

    protected VehicleMaintenanceManager $vehicleMaintenanceManager;

    protected EnterpriseHolidayManager $enterpriseHolidayManager;

    protected ManagerRegistry $em;

    public function __construct(InvoiceManager $invoiceManager,
        CostManager $costManager,
        DeliveryNoteManager $deliveryNoteManager,
        ImputableCostXlsManager $imputableCostXlsManager,
        MarginAnalysisXlsManager $marginAnalysisXlsManager,
        SaleDeliveryNotePdfManager $sdnpm,
        SaleInvoicePdfManager $sipm,
        WorkRegisterHeaderPdfManager $wrhpm,
        OperatorWorkRegisterHeaderXlsManager $operatorWorkRegisterHeaderXlsManager,
        PayslipPdfManager $ppm,
        PayslipXmlManager $pxm,
        OperatorCheckingPdfManager $operatorCheckingPdfManager,
        DocumentationPdfManager $documentationPdfManager,
        VehicleCheckingPdfManager $vehicleCheckingPdfManager,
        VehicleMaintenanceManager $vehicleMaintenanceManager,
        ManagerRegistry $managerRegistry,
        EnterpriseHolidayManager $enterpriseHolidayManager,
        PaymentReceiptPdfManager $paymentReceiptPdfManager)
    {
        $this->im = $invoiceManager;
        $this->costManager = $costManager;
        $this->deliveryNoteManager = $deliveryNoteManager;
        $this->imputableCostXlsManager = $imputableCostXlsManager;
        $this->marginAnalysisXlsManager = $marginAnalysisXlsManager;
        $this->sdnpm = $sdnpm;
        $this->sipm = $sipm;
        $this->wrhpm = $wrhpm;
        $this->operatorWorkRegisterHeaderXlsManager = $operatorWorkRegisterHeaderXlsManager;
        $this->ppm = $ppm;
        $this->pxm = $pxm;
        $this->operatorCheckingPdfManager = $operatorCheckingPdfManager;
        $this->documentationPdfManager = $documentationPdfManager;
        $this->vehicleCheckingPdfManager = $vehicleCheckingPdfManager;
        $this->vehicleMaintenanceManager = $vehicleMaintenanceManager;
        $this->em = $managerRegistry;
        $this->enterpriseHolidayManager = $enterpriseHolidayManager;
        $this->paymentReceiptPdfManager = $paymentReceiptPdfManager;
    }

    /**
     * @return Request
     */
    protected function resolveRequest(Request $request = null)
    {
        return $request;
    }

    protected function downloadDocument(Request $request, $id, DownloadHandler $downloadHandler, $object, $documentFile, $documentName, $fillErrorBag = true): Response
    {
        if (!$object) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }

        try {
            $return = $downloadHandler->downloadObject(
                $object,
                $fileField = $documentFile,
                $objectClass = get_class($object),
                $fileName = $documentName,
                $forceDownload = false
            );
        } catch (\ErrorException|NoFileFoundException $e) {
            if ($fillErrorBag) {
                $this->addFlash(
                    'warning',
                    'No se pudo recuperar el documento  '.$documentName.'.'
                );
            }
            $referer = $request->headers->get('referer');
            $return = new RedirectResponse($referer);
        }

        return $return;
    }
}
