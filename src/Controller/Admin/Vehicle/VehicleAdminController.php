<?php

namespace App\Controller\Admin\Vehicle;

use App\Controller\Admin\BaseAdminController;
use App\Entity\Vehicle\Vehicle;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
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
    public function editAction(Request $request, $id = null): Response
    {
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

        return parent::editAction($request);
    }

    public function downloadMainImageAction(Request $request, $id, DownloadHandler $downloadHandler): Response
    {
        /** @var Vehicle $operator */
        $vehicle = $this->admin->getObject($id);

        return $this->downloadDocument($request, $id, $downloadHandler, $vehicle, 'mainImageFile', $vehicle->getMainImage());
    }

    public function downloadChassisImageAction(Request $request, $id, DownloadHandler $downloadHandler): Response
    {
        /** @var Vehicle $operator */
        $vehicle = $this->admin->getObject($id);

        return $this->downloadDocument($request, $id, $downloadHandler, $vehicle, 'chassisImageFile', $vehicle->getChassisImage());
    }

    public function downloadTechnicalDatasheet1Action(Request $request, $id, DownloadHandler $downloadHandler): Response
    {
        /** @var Vehicle $operator */
        $vehicle = $this->admin->getObject($id);

        return $this->downloadDocument($request, $id, $downloadHandler, $vehicle, 'technicalDatasheet1File', $vehicle->getTechnicalDatasheet1());
    }

    public function downloadTechnicalDatasheet2Action(Request $request, $id, DownloadHandler $downloadHandler): Response
    {
        /** @var Vehicle $operator */
        $vehicle = $this->admin->getObject($id);

        return $this->downloadDocument($request, $id, $downloadHandler, $vehicle, 'technicalDatasheet2File', $vehicle->getTechnicalDatasheet2());
    }

    public function downloadLoadTableAction(Request $request, $id, DownloadHandler $downloadHandler): Response
    {
        /** @var Vehicle $operator */
        $vehicle = $this->admin->getObject($id);

        return $this->downloadDocument($request, $id, $downloadHandler, $vehicle, 'loadTableFile', $vehicle->getLoadTable());
    }

    public function downloadReachDiagramAction(Request $request, $id, DownloadHandler $downloadHandler): Response
    {
        /** @var Vehicle $operator */
        $vehicle = $this->admin->getObject($id);

        return $this->downloadDocument($request, $id, $downloadHandler, $vehicle, 'reachDiagramFile', $vehicle->getReachDiagram());
    }

    public function downloadTrafficCertificateAction(Request $request, $id, DownloadHandler $downloadHandler): Response
    {
        /** @var Vehicle $operator */
        $vehicle = $this->admin->getObject($id);

        return $this->downloadDocument($request, $id, $downloadHandler, $vehicle, 'trafficCertificateFile', $vehicle->getTrafficCertificate());
    }

    public function downloadDimensionsAction(Request $request, $id, DownloadHandler $downloadHandler): Response
    {
        /** @var Vehicle $operator */
        $vehicle = $this->admin->getObject($id);

        return $this->downloadDocument($request, $id, $downloadHandler, $vehicle, 'dimensionsFile', $vehicle->getDimensions());
    }

    public function downloadTransportCardAction(Request $request, $id, DownloadHandler $downloadHandler): Response
    {
        /** @var Vehicle $operator */
        $vehicle = $this->admin->getObject($id);

        return $this->downloadDocument($request, $id, $downloadHandler, $vehicle, 'transportCardFile', $vehicle->getTransportCard());
    }

    public function downloadTrafficInsuranceAction(Request $request, $id, DownloadHandler $downloadHandler): Response
    {
        /** @var Vehicle $operator */
        $vehicle = $this->admin->getObject($id);

        return $this->downloadDocument($request, $id, $downloadHandler, $vehicle, 'trafficInsuranceFile', $vehicle->getTrafficInsurance());
    }

    public function downloadItvAction(Request $request, $id, DownloadHandler $downloadHandler): Response
    {
        /** @var Vehicle $operator */
        $vehicle = $this->admin->getObject($id);

        return $this->downloadDocument($request, $id, $downloadHandler, $vehicle, 'itvFile', $vehicle->getItv());
    }

    public function downloadItcAction(Request $request, $id, DownloadHandler $downloadHandler): Response
    {
        /** @var Vehicle $operator */
        $vehicle = $this->admin->getObject($id);

        return $this->downloadDocument($request, $id, $downloadHandler, $vehicle, 'itcFile', $vehicle->getItc());
    }

    public function downloadCEDeclarationAction(Request $request, $id, DownloadHandler $downloadHandler): Response
    {
        /** @var Vehicle $operator */
        $vehicle = $this->admin->getObject($id);

        return $this->downloadDocument($request, $id, $downloadHandler, $vehicle, 'CEDeclarationFile', $vehicle->getCEDeclaration());
    }
}
