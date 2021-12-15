<?php

namespace App\Controller\Admin\Vehicle;

use App\Controller\Admin\BaseAdminController;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class VehicleMaintenanceAdminController.
 */
class VehicleMaintenanceAdminController extends BaseAdminController
{
    public function downloadPdfPendingMaintenanceAction(): Response
    {
        $this->addFlash('warning', 'Aquesta acció encara NO funciona!');

        return $this->redirectToRoute('admin_app_vehicle_vehiclemaintenance_list');
    }
}
