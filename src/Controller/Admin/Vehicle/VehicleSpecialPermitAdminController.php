<?php

namespace App\Controller\Admin\Vehicle;

use App\Controller\Admin\BaseAdminController;
use App\Entity\Vehicle\VehicleSpecialPermit;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Vich\UploaderBundle\Handler\DownloadHandler;

/**
 * Class VehicleSpecialPermitAdminController.
 */
class VehicleSpecialPermitAdminController extends BaseAdminController
{
    public function downloadRouteImageAction(Request $request, $id, DownloadHandler $downloadHandler): Response
    {
        /** @var VehicleSpecialPermit $operator */
        $vehiclePermit = $this->admin->getObject($id);

        return $this->downloadDocument($request, $id, $downloadHandler, $vehiclePermit, 'routeImageFile', $vehiclePermit->getRouteImage());
    }
}
