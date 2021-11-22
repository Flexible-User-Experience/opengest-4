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

    public function downloadTechnicalDatasheet1Action($id, DownloadHandler $downloadHandler): Response
    {
        /** @var Vehicle $operator */
        $vehicle = $this->admin->getObject($id);

        return $this->downloadDocument($id, $downloadHandler, 'technicalDatasheet1File', $vehicle->getTechnicalDatasheet1());
    }

    public function downloadTechnicalDatasheet2Action($id, DownloadHandler $downloadHandler): Response
    {
        /** @var Vehicle $operator */
        $vehicle = $this->admin->getObject($id);

        return $this->downloadDocument($id, $downloadHandler, 'technicalDatasheet2File', $vehicle->getTechnicalDatasheet2());
    }

    public function downloadLoadTableAction($id, DownloadHandler $downloadHandler): Response
    {
        /** @var Vehicle $operator */
        $vehicle = $this->admin->getObject($id);

        return $this->downloadDocument($id, $downloadHandler, 'loadTableFile', $vehicle->getLoadTable());
    }

    public function downloadReachDiagramAction($id, DownloadHandler $downloadHandler): Response
    {
        /** @var Vehicle $operator */
        $vehicle = $this->admin->getObject($id);

        return $this->downloadDocument($id, $downloadHandler, 'reachDiagramFile', $vehicle->getReachDiagram());
    }

    public function downloadTrafficCertificateAction($id, DownloadHandler $downloadHandler): Response
    {
        /** @var Vehicle $operator */
        $vehicle = $this->admin->getObject($id);

        return $this->downloadDocument($id, $downloadHandler, 'trafficCertificateFile', $vehicle->getTrafficCertificate());
    }

    public function downloadDimensionsAction($id, DownloadHandler $downloadHandler): Response
    {
        /** @var Vehicle $operator */
        $vehicle = $this->admin->getObject($id);

        return $this->downloadDocument($id, $downloadHandler, 'dimensionsFile', $vehicle->getDimensions());
    }

    public function downloadTransportCardAction($id, DownloadHandler $downloadHandler): Response
    {
        /** @var Vehicle $operator */
        $vehicle = $this->admin->getObject($id);

        return $this->downloadDocument($id, $downloadHandler, 'transportCardFile', $vehicle->getTransportCard());
    }

    public function downloadTrafficInsuranceAction($id, DownloadHandler $downloadHandler): Response
    {
        /** @var Vehicle $operator */
        $vehicle = $this->admin->getObject($id);

        return $this->downloadDocument($id, $downloadHandler, 'trafficInsuranceFile', $vehicle->getTrafficInsurance());
    }

    public function downloadItvAction($id, DownloadHandler $downloadHandler): Response
    {
        /** @var Vehicle $operator */
        $vehicle = $this->admin->getObject($id);

        return $this->downloadDocument($id, $downloadHandler, 'itvFile', $vehicle->getItv());
    }

    public function downloadItcAction($id, DownloadHandler $downloadHandler): Response
    {
        /** @var Vehicle $operator */
        $vehicle = $this->admin->getObject($id);

        return $this->downloadDocument($id, $downloadHandler, 'itcFile', $vehicle->getItc());
    }

    public function downloadCEDeclarationAction($id, DownloadHandler $downloadHandler): Response
    {
        /** @var Vehicle $operator */
        $vehicle = $this->admin->getObject($id);

        return $this->downloadDocument($id, $downloadHandler, 'CEDeclarationFile', $vehicle->getCEDeclaration());
    }
}
