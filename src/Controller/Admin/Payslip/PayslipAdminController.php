<?php

namespace App\Controller\Admin\Payslip;

use App\Controller\Admin\BaseAdminController;
use App\Entity\Payslip\Payslip;
use App\Form\Type\GeneratePaymentDocumentsPayslipFormType;
use DateTime;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PayslipAdminController.
 */
class PayslipAdminController extends BaseAdminController
{
    public function batchActionGeneratePaymentDocuments(ProxyQueryInterface $selectedModelQuery, Request $request): Response
    {
        $this->admin->checkAccess('edit');
        $form = $this->createForm(GeneratePaymentDocumentsPayslipFormType::class);
        $form->handleRequest($request);
        /** @var Payslip[] $payslips */
        $payslips = $selectedModelQuery->execute()->getQuery()->getResult();
        $form->get('payslips')->setData($payslips);

        return $this->renderWithExtraParams(
            'admin/payslip/payslipPaymentDocumentGeneration.html.twig',
            [
                'generatePaymentDocumentsPayslipForm' => $form->createView(),
            ]
        );
    }

    public function generatePaymentDocumentsAction(Request $request)
    {
        $formData = $request->request->get('app_generate_payslip_payment_document');
        /** @var Payslip[] $selectedModels */
        $selectedModels = $formData['payslips'];
        $documentType = $formData['type'];
        $date = DateTime::createFromFormat('d/m/Y', $formData['date']);
        $payslips = [];
        $em = $this->em;
        foreach ($selectedModels as $payslip) {
            $payslips[] = $em->getRepository(Payslip::class)->find($payslip);
        }
        if (0 === count($payslips)) {
            $this->addFlash('warning', 'No existen nóminas en esta selección');
        }
        if ('payslips' === $documentType) {
            $response = new Response($this->pxm->OutputSingle($payslips, false));
            $disposition = HeaderUtils::makeDisposition(
                HeaderUtils::DISPOSITION_ATTACHMENT,
                'nominas.xml'
            );
            $response->headers->set('Content-Disposition', $disposition);
            $response->headers->set('Content-type', 'text/xml');
            $response->setStatusCode('200');

            return $response;
        } elseif ('expenses' === $documentType) {
            $response = new Response($this->pxm->OutputSingle($payslips, true));
            $disposition = HeaderUtils::makeDisposition(
                HeaderUtils::DISPOSITION_ATTACHMENT,
                'nominas.xml'
            );
            $response->headers->set('Content-Disposition', $disposition);
            $response->headers->set('Content-type', 'text/xml');
            $response->setStatusCode('200');

            return $response;
        } else {
            $this->addFlash('warning', 'Documento no válido');
        }

        return new RedirectResponse($this->generateUrl('admin_app_payslip_payslip_list'));
    }

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
