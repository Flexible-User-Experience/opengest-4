<?php

namespace App\Controller\Admin\Operator;

use App\Controller\Admin\BaseAdminController;
use App\Entity\Operator\OperatorChecking;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Vich\UploaderBundle\Handler\DownloadHandler;

/**
 * Class OperatorCheckingAdminController.
 */
class OperatorCheckingAdminController extends BaseAdminController
{
    /**
     * @param int|null $id
     *
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, $id = null): Response
    {
        $id = $request->get($this->admin->getIdParameter());

        /** @var OperatorChecking $operatorChecking */
        $operatorChecking = $this->admin->getObject($id);
        if (!$operatorChecking) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }

        return parent::editAction($request);
    }

    public function downloadAction(Request $request, DownloadHandler $downloadHandler): StreamedResponse
    {
        $id = $request->get($this->admin->getIdParameter());
        $object = $this->admin->getObject($id);
        if (!$object) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }

        return $downloadHandler->downloadObject($object, 'uploadedFile');
    }

    public function downloadPdfOperatorPendingCheckingsAction(Request $request): Response
    {
        $category = $request->get('category');
        $operatorCheckings = $this->admin->getModelManager()->findBy(OperatorChecking::class, [
            'enabled' => true,
        ]);
        $operatorCheckings = array_filter($operatorCheckings, function (OperatorChecking $operatorChecking) use ($category) {
            return $operatorChecking->getType()->getCategory() == $category;
        });
        // TODO filter by expedition date <= today plus + 1 month;
        if (!$operatorCheckings) {
            $this->addFlash('warning', 'No existen revisiones pendientes.');
        }

        return new Response($this->operatorCheckingPdfManager->outputSingle($operatorCheckings), 200, ['Content-type' => 'application/pdf']);
    }

    public function batchActionDownloadPdfOperatorPendingCheckings(ProxyQueryInterface $selectedModelQuery, Request $request): Response
    {
        $operatorCheckings = $selectedModelQuery->execute()->getQuery()->getResult();
        if (!$operatorCheckings) {
            $this->addFlash('warning', 'No hay revisiones seleccionadas.');
        }

        return new Response($this->operatorCheckingPdfManager->outputSingle($operatorCheckings), 200, ['Content-type' => 'application/pdf']);
    }
}
