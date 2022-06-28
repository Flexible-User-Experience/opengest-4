<?php

namespace App\Controller\Admin\Vehicle;

use App\Controller\Admin\BaseAdminController;
use App\Entity\Vehicle\VehicleMaintenance;
use App\Manager\Pdf\VehicleMaintenancePdfManager;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class VehicleMaintenanceAdminController.
 */
class VehicleMaintenanceAdminController extends BaseAdminController
{
    public function downloadPdfPendingMaintenanceAction(VehicleMaintenancePdfManager $vehicleMaintenancePdfManager): Response
    {
        $vehicleMaintenances = $this->admin->getModelManager()->findBy(VehicleMaintenance::class, [
            'enabled' => true,
            'needsCheck' => true,
        ]);
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
}
