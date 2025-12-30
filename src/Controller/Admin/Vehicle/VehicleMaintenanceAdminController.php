<?php

namespace App\Controller\Admin\Vehicle;

use App\Controller\Admin\BaseAdminController;
use App\Entity\Vehicle\VehicleMaintenance;
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
use App\Manager\Pdf\VehicleMaintenancePdfManager;
use App\Manager\Pdf\WorkRegisterHeaderPdfManager;
use App\Manager\VehicleMaintenanceManager;
use App\Manager\Xls\ImputableCostXlsManager;
use App\Manager\Xls\MarginAnalysisXlsManager;
use App\Manager\Xls\OperatorWorkRegisterHeaderXlsManager;
use App\Manager\Xml\PayslipXmlManager;
use App\Repository\Vehicle\VehicleMaintenanceRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Mirmit\EFacturaBundle\Service\EFacturaService;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class VehicleMaintenanceAdminController.
 */
class VehicleMaintenanceAdminController extends BaseAdminController
{
    public function downloadPdfPendingMaintenanceAction(
        VehicleMaintenancePdfManager $vehicleMaintenancePdfManager,
        VehicleMaintenanceRepository $vehicleMaintenanceRepository
    ): Response
    {
        $vehicleMaintenances = $vehicleMaintenanceRepository->getEnabledActiveVehicleMaintenancesSortedById(needsCheck: true);
        if (!$vehicleMaintenances) {
            $this->addFlash('warning', 'No existen mantenimientos pendientes.');
        }

        return new Response($vehicleMaintenancePdfManager->outputSingle($vehicleMaintenances), 200, ['Content-type' => 'application/pdf']);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function checkMaintenancesAction(Request $request)
    {
        $numberOfNewMaintenances = $this->vehicleMaintenanceManager->checkVehicleMaintenance();
        if ($numberOfNewMaintenances > 0) {
            $this->addFlash('success', 'Se han detectado '.$numberOfNewMaintenances.' nuevos mantenimientos necesarios');
        } else {
            $this->addFlash('success', 'No se han detectado nuevos mantenimientos');
        }

        return new RedirectResponse($request->headers->get('referer'));
    }

    public function batchActionDownloadPdfPendingMaintenance(ProxyQueryInterface $selectedModelQuery, VehicleMaintenancePdfManager $vehicleMaintenancePdfManager): Response
    {
        $vehicleMaintenances = $selectedModelQuery->execute();
        if (!$vehicleMaintenances) {
            $this->addFlash('warning', 'No existen mantenimientos pendientes.');
        }

        return new Response($vehicleMaintenancePdfManager->outputSingle($vehicleMaintenances), 200, ['Content-type' => 'application/pdf']);
    }
}
