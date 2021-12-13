<?php

namespace App\Controller\Admin\Vehicle;

use App\Controller\Admin\BaseAdminController;
use App\Entity\Vehicle\VehicleChecking;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class VehicleCheckingAdminController.
 */
class VehicleCheckingAdminController extends BaseAdminController
{
    /**
     * @param int|null $id
     *
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, $id = null): Response
    {
        $id = $request->get($this->admin->getIdParameter());

        /** @var VehicleChecking $vehicleChecking */
        $vehicleChecking = $this->admin->getObject($id);
        if (!$vehicleChecking) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }

        $guardService = $this->container->get('app.guard_service');
        if (!$guardService->isOwnVehicleChecking($vehicleChecking)) {
            throw $this->createAccessDeniedException(sprintf('forbidden object with id: %s', $id));
        }

        return parent::editAction($request);
    }
}
