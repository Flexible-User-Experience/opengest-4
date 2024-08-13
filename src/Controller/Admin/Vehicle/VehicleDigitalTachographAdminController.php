<?php

namespace App\Controller\Admin\Vehicle;

use App\Controller\Admin\BaseAdminController;
use App\Entity\Vehicle\VehicleDigitalTachograph;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Vich\UploaderBundle\Handler\DownloadHandler;

/**
 * Class VehicleDigitalTachographAdminController.
 */
class VehicleDigitalTachographAdminController extends BaseAdminController
{
    public function downloadAction(Request $request, DownloadHandler $downloadHandler): StreamedResponse
    {
        $id = $request->get($this->admin->getIdParameter());

        /** @var VehicleDigitalTachograph $tachograph */
        $vehicleDigitalTachograph = $this->admin->getObject($id);
        if (!$vehicleDigitalTachograph) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }

        return $downloadHandler->downloadObject($vehicleDigitalTachograph, 'uploadedFile');
    }

    /**
     * @param int|null $id
     *
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, $id = null): RedirectResponse|Response
    {
        $id = $request->get($this->admin->getIdParameter());

        /** @var VehicleDigitalTachograph $vehicleDigitalTachograph */
        $vehicleDigitalTachograph = $this->admin->getObject($id);
        if (!$vehicleDigitalTachograph) {
            throw $this->createNotFoundException(sprintf('unable to find the object wirh id %s', $id));
        }

        return parent::editAction($request);
    }
}
