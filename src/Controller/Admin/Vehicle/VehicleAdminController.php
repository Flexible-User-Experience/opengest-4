<?php

namespace App\Controller\Admin\Vehicle;

use App\Controller\Admin\BaseAdminController;
use App\Entity\Vehicle\Vehicle;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class VehicleAdminController.
 */
class VehicleAdminController extends BaseAdminController
{
    /**
     * @param int|null $id
     *
     * @return RedirectResponse|Response
     */
    public function editAction($id = null)
    {
        $request = $this->getRequest();
        $id = $request->get($this->admin->getIdParameter());

        /** @var Vehicle $vehicle */
        $vehicle = $this->admin->getObject($id);
        if (!$vehicle) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }

        $guardService = $this->container->get('app.guard_service');
        if (!$guardService->isOwnVehicle($vehicle)) {
            throw $this->createAccessDeniedException(sprintf('forbidden object with id: %s', $id));
        }

        return parent::editAction($id);
    }
}
