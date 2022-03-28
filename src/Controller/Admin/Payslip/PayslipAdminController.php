<?php

namespace App\Controller\Admin\Payslip;

use App\Controller\Admin\BaseAdminController;
use App\Entity\Payslip\Payslip;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PayslipAdminController.
 */
class PayslipAdminController extends BaseAdminController
{
    /**
     * Generate PDF receipt action.
     */
    public function batchActionGeneratePayslip(ProxyQueryInterface $selectedModelQuery): Response
    {
        $payslips = $selectedModelQuery->execute()->getQuery()->getResult();

        if (!$payslips) {
            $this->addFlash('warning', 'No existen nóminas en esta selección');
        }

        return new Response($this->ppm->outputCollection($payslips), 200, ['Content-type' => 'application/pdf']);
    }

    /**
     * Generate XML for payslip payment.
     */
    public function batchActionGeneratePayslipXMLPayment(ProxyQueryInterface $selectedModelQuery): Response
    {
        $payslips = $selectedModelQuery->execute()->getQuery()->getResult();

        if (!$payslips) {
            $this->addFlash('warning', 'No existen nóminas en esta selección');
        }
        $response = new Response($this->pxm->OutputSingle($payslips, false));
        $disposition = HeaderUtils::makeDisposition(
            HeaderUtils::DISPOSITION_ATTACHMENT,
            'nominas.xml'
        );
        $response->headers->set('Content-Disposition', $disposition);
        $response->headers->set('Content-type', 'text/xml');
        $response->setStatusCode('200');

        return $response;
    }

    /**
     * Generate XML for payslip payment.
     */
    public function batchActionGeneratePayslipDietsXMLPayment(ProxyQueryInterface $selectedModelQuery): Response
    {
        $payslips = $selectedModelQuery->execute()->getQuery()->getResult();

        if (!$payslips) {
            $this->addFlash('warning', 'No existen nóminas en esta selección');
        }
        $response = new Response($this->pxm->OutputSingle($payslips, true));
        $disposition = HeaderUtils::makeDisposition(
            HeaderUtils::DISPOSITION_ATTACHMENT,
            'nominas.xml'
        );
        $response->headers->set('Content-Disposition', $disposition);
        $response->headers->set('Content-type', 'text/xml');
        $response->setStatusCode('200');

        return $response;
    }
}
