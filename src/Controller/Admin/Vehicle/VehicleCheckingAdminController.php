<?php

namespace App\Controller\Admin\Vehicle;

use App\Controller\Admin\BaseAdminController;
use App\Entity\Vehicle\VehicleChecking;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
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
    public function editAction(Request $request, $id = null): RedirectResponse|Response
    {
        $id = $request->get($this->admin->getIdParameter());

        /** @var VehicleChecking $vehicleChecking */
        $vehicleChecking = $this->admin->getObject($id);
        if (!$vehicleChecking) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }

        return parent::editAction($request);
    }

    public function downloadPdfPendingCheckingsAction(): Response
    {
        $vehicleCheckings = $this->admin->getModelManager()->findBy(VehicleChecking::class, [
                'enabled' => true,
            ]);
        if (!$vehicleCheckings) {
            $this->addFlash('warning', 'No existen mantenimientos pendientes.');
        }

        return new Response($this->vehicleCheckingPdfManager->outputSingle($vehicleCheckings), 200, ['Content-type' => 'application/pdf']);
    }

    public function batchActionDownloadPdfVehiclePendingCheckings(ProxyQueryInterface $selectedModelQuery, Request $request): Response
    {
        $vehicleCheckings = $selectedModelQuery->execute()->getQuery()->getResult();
        if (!$vehicleCheckings) {
            $this->addFlash('warning', 'No hay revisiones seleccionadas.');
        }

        return new Response($this->vehicleCheckingPdfManager->outputSingle($vehicleCheckings), 200, ['Content-type' => 'application/pdf']);
    }
}
