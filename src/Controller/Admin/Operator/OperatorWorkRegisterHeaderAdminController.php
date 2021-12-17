<?php

namespace App\Controller\Admin\Operator;

use App\Controller\Admin\BaseAdminController;
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
    public function batchActionGenerateWorkRegisterReportPdf(ProxyQueryInterface $selectedModelQuery): Response
    {
        $this->admin->checkAccess('edit');
        $operatorWorkRegisterHeaders = $selectedModelQuery->execute()->getQuery()->getResult();
        $owrhForDates = $operatorWorkRegisterHeaders;

        //TODO get from, to from the filter selection
        $from = array_shift($owrhForDates)->getDateFormatted();

        if (!$owrhForDates) {
            $to = $from;
        } else {
            $to = array_pop($owrhForDates)->getDateFormatted();
        }

        if (!$operatorWorkRegisterHeaders) {
            $this->addFlash('warning', 'No existen registros para esta selección');
        }

        return new Response($this->wrhpm->outputCollection($operatorWorkRegisterHeaders, $from, $to), 200, ['Content-type' => 'application/pdf']);
    }
}
