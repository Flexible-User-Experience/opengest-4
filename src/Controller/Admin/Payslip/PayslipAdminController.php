<?php

namespace App\Controller\Admin\Payslip;

use App\Controller\Admin\BaseAdminController;
use App\Entity\Payslip\Payslip;
use App\Manager\Pdf\PayslipPdfManager;
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
    public function pdfAction(Request $request, PayslipPdfManager $payslipPdfManager): Response
    {
        $request = $this->resolveRequest($request);
        $id = $request->get($this->admin->getIdParameter());

        /** @var Payslip $payslip */
        $payslip = $this->admin->getObject($id);
        if (!$payslip) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }

        return new Response($payslipPdfManager->outputSingle($payslip), 200, ['Content-type' => 'application/pdf']);
    }
}
