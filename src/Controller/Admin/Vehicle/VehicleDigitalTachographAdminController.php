<?php

namespace App\Controller\Admin\Vehicle;

use App\Controller\Admin\BaseAdminController;
use App\Entity\Vehicle\VehicleDigitalTachograph;
use App\Service\GuardService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Class VehicleDigitalTachographAdminController.
 */
class VehicleDigitalTachographAdminController extends BaseAdminController
{
    /**
     * @param int|null $id
     *
     * @return StreamedResponse
     */
    public function downloadAction($id = null)
    {
        $request = $this->getRequest();
        $id = $request->get($this->admin->getIdParameter());

        /** @var VehicleDigitalTachograph $tachograph */
        $vehicleDigitalTachograph = $this->admin->getObject($id);
        if (!$vehicleDigitalTachograph) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }

        /** @var GuardService $guardService */
        $guardService = $this->container->get('app.guard_service');
        if (!$guardService->isOwnVehicle($vehicleDigitalTachograph->getVehicle())) {
            throw $this->createNotFoundException(sprintf('forbidden object with id: %s', $id));
        }

        $downloadHandler = $this->container->get('vich_uploader.download_handler');

        return $downloadHandler->downloadObject($vehicleDigitalTachograph, 'uploadedFile');
    }

    /**
     * @param int|null $id
     *
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, $id = null): Response
    {
        $id = $request->get($this->admin->getIdParameter());

        /** @var VehicleDigitalTachograph $vehicleDigitalTachograph */
        $vehicleDigitalTachograph = $this->admin->getObject($id);
        if (!$vehicleDigitalTachograph) {
            throw $this->createNotFoundException(sprintf('unable to find the object wirh id %s', $id));
        }

        /** @var GuardService $guardService */
        $guardService = $this->container->get('app.guard_service');
        if (!$guardService->isOwnVehicle($vehicleDigitalTachograph->getVehicle())) {
            throw $this->createNotFoundException('forbidden object wirh id %s', $id);
        }

        return parent::editAction($request);
    }
}
