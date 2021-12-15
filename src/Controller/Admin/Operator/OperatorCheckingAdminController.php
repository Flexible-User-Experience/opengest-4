<?php

namespace App\Controller\Admin\Operator;

use App\Controller\Admin\BaseAdminController;
use App\Entity\Operator\OperatorChecking;
use App\Manager\Pdf\OperatorCheckingPdfManager;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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

    public function downloadPdfOperatorPendingCheckingsAction(OperatorCheckingPdfManager $operatorCheckingPdfManager): Response
    {
        $operatorCheckings = $this->admin->getModelManager()->findBy(OperatorChecking::class, [
            'enabled' => true,
        ]);
        if (!$operatorCheckings) {
            $this->addFlash('warning', 'No existen revisiones pendientes.');
        }

        return new Response($operatorCheckingPdfManager->outputSingle($operatorCheckings), 200, ['Content-type' => 'application/pdf']);
    }
}
