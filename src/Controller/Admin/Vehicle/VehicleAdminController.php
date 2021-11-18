<?php

namespace App\Controller\Admin\Vehicle;

use App\Controller\Admin\BaseAdminController;
use App\Entity\Vehicle\Vehicle;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Vich\UploaderBundle\Handler\DownloadHandler;

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

    public function downloadMainImageAction($id, DownloadHandler $downloadHandler): Response
    {
        /** @var Vehicle $operator */
        $vehicle = $this->admin->getObject($id);

        return $this->downloadDocument($id, $downloadHandler, 'mainImageFile', $vehicle->getMainImage());
    }

    public function downloadChassisImageAction($id, DownloadHandler $downloadHandler): Response
    {
        /** @var Vehicle $operator */
        $vehicle = $this->admin->getObject($id);

        return $this->downloadDocument($id, $downloadHandler, 'chassisImageFile', $vehicle->getChassisImage());
    }

    private function downloadDocument($id, DownloadHandler $downloadHandler, $documentFile, $documentName): Response
    {
        /** @var Vehicle $operator */
        $vehicle = $this->admin->getObject($id);
        if (!$vehicle) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }

        return $downloadHandler->downloadObject(
            $vehicle,
            $fileField = $documentFile,
            $objectClass = Vehicle::class,
            $fileName = $documentName,
            $forceDownload = false
        );
    }
}
