<?php

namespace App\Controller\Admin\Vehicle;

use App\Controller\Admin\BaseAdminController;
use App\Entity\Vehicle\VehicleSpecialPermit;
use App\Manager\Pdf\VehicleSpecialPermitPdfManager;
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

    public function downloadPdfPendingSpecialPermitsAction(VehicleSpecialPermitPdfManager $vehicleSpecialPermitPdfManager): Response
    {
        $vehicleSpecialPermits = $this->admin->getModelManager()->findBy(VehicleSpecialPermit::class, [
                'enabled' => true,
            ]);
        if (!$vehicleSpecialPermits) {
            $this->addFlash('warning', 'No existen permisos especiales pendientes de actualizar.');
        }

        return new Response($vehicleSpecialPermitPdfManager->outputSingle($vehicleSpecialPermits), 200, ['Content-type' => 'application/pdf']);
    }
}
