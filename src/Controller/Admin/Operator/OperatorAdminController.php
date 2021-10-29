<?php

namespace App\Controller\Admin\Operator;

use App\Controller\Admin\BaseAdminController;
use App\Entity\Operator\Operator;
use App\Entity\Payslip\Payslip;
use App\Form\Type\GeneratePayslipsFormType;
use App\Service\GuardService;
use Symfony\Component\HttpFoundation\Request;
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

    public function batchActionCreatePayslipFromOperators(ProxyQueryInterface $selectedModelQuery, Request $request)
    {
        $this->admin->checkAccess('edit');
        $form = $this->createForm(GeneratePayslipsFormType::class);
        $form->handleRequest($request);
        /** @var Operator[] $operators */
        $operators = $selectedModelQuery->execute();
        $form->get('operators')->setData($operators);

        return $this->renderWithExtraParams(
            'admin/operator/payslipGeneration.html.twig',
            [
                'generatePayslipsForm' => $form->createView(),
                'operators' => $operators
            ]
        );
    }

    public function generatePayslipsAction(Request $request)
    {
        $form = $this->createForm(GeneratePayslipsFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $operators = $form->get('operators')->getData();
            foreach ($operators as $operator) {
                $payslip = new Payslip();
                $payslip->setOperator($operator);
                $payslip->setFromDate($form->get('fromDate')->getData());
                dd($payslip);
                dd($operator);
            }
        }
    }
}
