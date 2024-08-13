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
use App\Manager\RepositoriesManager;
use App\Manager\VehicleMaintenanceManager;
use App\Manager\Xls\ImputableCostXlsManager;
use App\Manager\Xls\MarginAnalysisXlsManager;
use App\Manager\Xls\OperatorWorkRegisterHeaderXlsManager;
use App\Manager\Xml\PayslipXmlManager;
use Doctrine\Persistence\ManagerRegistry;
use Mirmit\EFacturaBundle\Service\EFacturaService;
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
    public function __construct(
        protected readonly InvoiceManager $im,
        protected readonly CostManager $costManager,
        protected readonly DeliveryNoteManager $deliveryNoteManager,
        protected readonly ImputableCostXlsManager $imputableCostXlsManager,
        protected readonly MarginAnalysisXlsManager $marginAnalysisXlsManager,
        protected readonly SaleDeliveryNotePdfManager $sdnpm,
        protected readonly SaleInvoicePdfManager $sipm,
        protected readonly WorkRegisterHeaderPdfManager $wrhpm,
        protected readonly OperatorWorkRegisterHeaderXlsManager $operatorWorkRegisterHeaderXlsManager,
        protected readonly PayslipPdfManager $ppm,
        protected readonly PayslipXmlManager $pxm,
        protected readonly OperatorCheckingPdfManager $operatorCheckingPdfManager,
        protected readonly DocumentationPdfManager $documentationPdfManager,
        protected readonly VehicleCheckingPdfManager $vehicleCheckingPdfManager,
        protected readonly VehicleMaintenanceManager $vehicleMaintenanceManager,
        protected readonly ManagerRegistry $em,
        protected readonly EnterpriseHolidayManager $enterpriseHolidayManager,
        protected readonly PaymentReceiptPdfManager $paymentReceiptPdfManager,
        protected readonly EFacturaService $EFacturaService,
        protected readonly RepositoriesManager $repositoriesManager
    ) {
    }

    protected function resolveRequest(Request $request = null): Request
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
