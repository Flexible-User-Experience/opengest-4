<?php

namespace App\Controller\Admin\Payslip;

use App\Controller\Admin\BaseAdminController;
use App\Entity\Payslip\Payslip;
use App\Manager\Pdf\PayslipPdfManager;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Symfony\Component\HttpFoundation\Request;
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

        return new Response($this->pxm->OutputSingle($payslips), 200, ['Content-type' => 'text/xml']);
    }
}
