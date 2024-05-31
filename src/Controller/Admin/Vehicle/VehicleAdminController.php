<?php

namespace App\Controller\Admin\Vehicle;

use App\Controller\Admin\BaseAdminController;
use App\Entity\Enterprise\Enterprise;
use App\Entity\Operator\Operator;
use App\Entity\Vehicle\Vehicle;
use App\Enum\EnterpriseDocumentsEnum;
use App\Enum\VehicleDocumentsEnum;
use App\Form\Type\Vehicle\GenerateDocumentationFormType;
use Doctrine\Common\Collections\ArrayCollection;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\String\UnicodeString;
use Symfony\Contracts\Translation\TranslatorInterface;
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

        return parent::editAction($request);
    }

    public function downloadMainImageAction(Request $request, $id, DownloadHandler $downloadHandler): Response
    {
        /** @var Vehicle $operator */
        $vehicle = $this->admin->getObject($id);

        return $this->downloadDocument($request, $id, $downloadHandler, $vehicle, 'mainImageFile', $vehicle->getMainImage(), false);
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

    public function downloadTrafficReceiptAction(Request $request, $id, DownloadHandler $downloadHandler): Response
    {
        /** @var Vehicle $operator */
        $vehicle = $this->admin->getObject($id);

        return $this->downloadDocument($request, $id, $downloadHandler, $vehicle, 'trafficReceiptFile', $vehicle->getTrafficReceipt());
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

    public function batchActionDownloadDocumentation(ProxyQueryInterface $selectedModelQuery, Request $request): Response
    {
        $this->admin->checkAccess('edit');
        $form = $this->createForm(GenerateDocumentationFormType::class);
        $form->handleRequest($request);
        /** @var Vehicle[] $vehicle */
        $vehicle = $selectedModelQuery->execute()->getQuery()->getResult();
        $form->get('vehicles')->setData($vehicle);

        return $this->renderWithExtraParams(
            'admin/vehicle/documentationGeneration.html.twig',
            [
                'generateDocumentationForm' => $form->createView(),
            ]
        );
    }

    public function downloadLogBookPdfAction(Request $request)
    {
        $this->addFlash('warning', 'Esta funcionalidad no está implementada todavía');

        return parent::editAction($request);
    }

    public function generateDocumentationAction(Request $request, TranslatorInterface $translator)
    {
        $formData = $request->request->get('app_generate_vehicle_documentation');
        $documentation = [];
        $vehicleIds = $formData['vehicles'];
        if (!$vehicleIds) {
            $this->addFlash('warning', 'No hay vehículos seleccionados');
        }
        $vehicleRepository = $this->em->getRepository(Vehicle::class);
        /** @var Operator[] $operators */
        $vehicles = new ArrayCollection();
        /* @var Operator $operator */
        foreach ($vehicleIds as $vehicleId) {
            $vehicle = $vehicleRepository->findOneBy(['id' => $vehicleId]);
            $vehicles[] = $vehicle;
            if (!$vehicle) {
                continue;
            }
            if (array_key_exists('documentation', $formData)) {
                $documentIds = $formData['documentation'];
                foreach ($documentIds as $documentId) {
                    $documentName = VehicleDocumentsEnum::getName($documentId);
                    $documentNameNotTranslated = VehicleDocumentsEnum::getReversedEnumArray()[$documentId];
                    $method = new UnicodeString('GET_'.$documentName);
                    $fileName = call_user_func([$vehicle, $method->lower()->camel()->toString()]);
                    if ('' != $fileName) {
                        $filePath = $this->getParameter('kernel.project_dir').'/var/uploads/images/vehicle/'.$fileName;
                        if (file_exists($filePath)) {
                            $fileContents = file_get_contents($filePath);
                            $documentation[$vehicle->getId()][] = [
                                'name' => $documentName,
                                'nameTranslated' => $translator->trans($documentNameNotTranslated, [], 'admin'),
                                'content' => $fileContents,
                                'fileType' => explode('.', $fileName)[1],
                                'exists' => true,
                            ];
                        }
                    } else {
                        $documentation[$vehicle->getId()][] = [
                            'exists' => false,
                        ];
                    }
                }
            }
        }
        $enterpriseDocumentation = [];
        if (array_key_exists('enterpriseDocumentation', $formData)) {
            $enterpriseDocumentIds = $formData['enterpriseDocumentation'];
            $enterprise = $this->admin->getModelManager()->find(Enterprise::class, 1);
            foreach ($enterpriseDocumentIds as $enterpriseDocumentId) {
                $documentName = EnterpriseDocumentsEnum::getName($enterpriseDocumentId);
                $documentNameNotTranslated = EnterpriseDocumentsEnum::getReversedEnumArray()[$enterpriseDocumentId];
                $method = new UnicodeString('GET_'.$documentName);
                $fileName = call_user_func([$enterprise, $method->lower()->camel()->toString()]);
                if ('' != $fileName) {
                    $filePath = $this->getParameter('kernel.project_dir').'/var/uploads/images/enterprise/'.$fileName;
                    if (file_exists($filePath)) {
                        $fileContents = file_get_contents($filePath);
                        $enterpriseDocumentation[] = [
                            'name' => $documentName,
                            'nameTranslated' => $translator->trans($documentNameNotTranslated, [], 'admin'),
                            'content' => $fileContents,
                            'fileType' => explode('.', $fileName)[1],
                            'exists' => true,
                        ];
                    }
                } else {
                    $enterpriseDocumentation[] = [
                        'exists' => false,
                    ];
                }
            }
        }

        return new Response($this->documentationPdfManager->outputSingle($vehicles, $documentation, $enterpriseDocumentation), 200, ['Content-type' => 'application/pdf']);
    }
}
