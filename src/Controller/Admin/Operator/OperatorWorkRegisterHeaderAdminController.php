<?php

namespace App\Controller\Admin\Operator;

use App\Controller\Admin\BaseAdminController;
use App\Entity\Operator\OperatorWorkRegisterHeader;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class OperatorWorkRegisterHeaderAdminController.
 */
class OperatorWorkRegisterHeaderAdminController extends BaseAdminController
{
    /**
     * @return Response|RedirectResponse
     */
    public function batchActionGenerateWorkRegisterReportPdf(ProxyQueryInterface $selectedModelQuery)
    {
        $this->admin->checkAccess('edit');
        $operatorWorkRegisterHeaders = $selectedModelQuery->execute()->getQuery()->getResult();
        /** @var OperatorWorkRegisterHeader $operatorWorkRegisterHeader */
        foreach ($operatorWorkRegisterHeaders as $operatorWorkRegisterHeader) {
            dd($operatorWorkRegisterHeader->getOperatorWorkRegisters());
        }

        $this->addFlash('warning', 'This action is not working');

        return new RedirectResponse($this->generateUrl('admin_app_operator_operatorworkregisterheader_list'));
    }
}
