<?php

namespace App\Controller\Admin\Operator;

use App\Controller\Admin\BaseAdminController;
use App\Entity\Operator\Operator;
use App\Service\GuardService;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Vich\UploaderBundle\Handler\DownloadHandler;

/**
 * Class OperatorAdminController.
 */
class OperatorAdminController extends BaseAdminController
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

        /** @var Operator $operator */
        $operator = $this->admin->getObject($id);
        if (!$operator) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }
        /** @var GuardService $guardService */
        $guardService = $this->get('app.guard_service');
        if (!$guardService->isOwnOperator($operator)) {
            throw $this->createAccessDeniedException(sprintf('forbidden object with id: %s', $id));
        }

        return parent::editAction($id);
    }

    public function downloadProfilePhotoImageAction($id = null, DownloadHandler $downloadHandler): Response
    {
        /** @var Operator $operator */
        $operator = $this->admin->getObject($id);
        if (!$operator) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }
        /** @var GuardService $guardService */
        $guardService = $this->get('app.guard_service');
        if (!$guardService->isOwnOperator($operator)) {
            throw $this->createAccessDeniedException(sprintf('forbidden object with id: %s', $id));
        }

        return $downloadHandler->downloadObject(
            $operator,
            $fileField = 'profilePhotoImageFile',
            $objectClass = Operator::class,
            $fileName = $operator->getProfilePhotoImage(),
            $forceDownload = false
        );
    }

    public function batchActionCreatePayslipFromOperators(ProxyQueryInterface $selectedModelQuery)
    {
        $this->admin->checkAccess('edit');
        /** @var Operator[] $operators */
        $operators = $selectedModelQuery->execute();

        return $this->renderView('admin/operator/payslipGeneration.html.twig', $operators);
    }
}
