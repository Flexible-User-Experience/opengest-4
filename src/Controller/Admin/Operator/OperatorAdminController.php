<?php

namespace App\Controller\Admin\Operator;

use App\Controller\Admin\BaseAdminController;
use App\Entity\Operator\Operator;
use App\Entity\Payslip\Payslip;
use App\Entity\Payslip\PayslipLine;
use App\Entity\Payslip\PayslipOperatorDefaultLine;
use App\Form\Type\GeneratePayslipsFormType;
use App\Service\GuardService;
use DateTime;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
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

    public function downloadProfilePhotoImageAction($id, DownloadHandler $downloadHandler): Response
    {
        /** @var Operator $operator */
        $operator = $this->admin->getObject($id);

        return $this->downloadDocument($id, $downloadHandler, $operator, 'profilePhotoImageFile', $operator->getProfilePhotoImage());
    }

    public function downloadTaxIdentificationNumberImageAction($id, DownloadHandler $downloadHandler): Response
    {
        /** @var Operator $operator */
        $operator = $this->admin->getObject($id);

        return $this->downloadDocument($id, $downloadHandler, $operator, 'taxIdentificationNumberImageFile', $operator->getTaxIdentificationNumberImage());
    }

    public function downloadDrivingLicenseImageAction($id, DownloadHandler $downloadHandler): Response
    {
        /** @var Operator $operator */
        $operator = $this->admin->getObject($id);

        return $this->downloadDocument($id, $downloadHandler, $operator, 'drivingLicenseImageFile', $operator->getDrivingLicenseImage());
    }

    public function downloadCranesOperatorLicenseImageAction($id, DownloadHandler $downloadHandler): Response
    {
        /** @var Operator $operator */
        $operator = $this->admin->getObject($id);

        return $this->downloadDocument($id, $downloadHandler, $operator, 'cranesOperatorLicenseImageFile', $operator->getCranesOperatorLicenseImage());
    }

    public function downloadMedicalCheckImageAction($id, DownloadHandler $downloadHandler): Response
    {
        /** @var Operator $operator */
        $operator = $this->admin->getObject($id);

        return $this->downloadDocument($id, $downloadHandler, $operator, 'medicalCheckImageFile', $operator->getMedicalCheckImage());
    }

    public function downloadEpisImageAction($id, DownloadHandler $downloadHandler): Response
    {
        /** @var Operator $operator */
        $operator = $this->admin->getObject($id);

        return $this->downloadDocument($id, $downloadHandler, $operator, 'episImageFile', $operator->getEpisImage());
    }

    public function downloadTrainingDocumentImageAction($id, DownloadHandler $downloadHandler): Response
    {
        /** @var Operator $operator */
        $operator = $this->admin->getObject($id);

        return $this->downloadDocument($id, $downloadHandler, $operator, 'trainingDocumentImageFile', $operator->getTrainingDocumentImage());
    }

    public function downloadInformationImageAction($id, DownloadHandler $downloadHandler): Response
    {
        /** @var Operator $operator */
        $operator = $this->admin->getObject($id);

        return $this->downloadDocument($id, $downloadHandler, $operator, 'informationImageFile', $operator->getInformationImage());
    }

    public function downloadUseOfMachineryAuthorizationImageAction($id, DownloadHandler $downloadHandler): Response
    {
        /** @var Operator $operator */
        $operator = $this->admin->getObject($id);

        return $this->downloadDocument($id, $downloadHandler, $operator, 'useOfMachineryAuthorizationImageFile', $operator->getUseOfMachineryAuthorizationImage());
    }

    public function downloadDischargeSocialSecurityImageAction($id, DownloadHandler $downloadHandler): Response
    {
        /** @var Operator $operator */
        $operator = $this->admin->getObject($id);

        return $this->downloadDocument($id, $downloadHandler, $operator, 'dischargeSocialSecurityImageFile', $operator->getDischargeSocialSecurityImage());
    }

    public function downloadEmploymentContractImageAction($id, DownloadHandler $downloadHandler): Response
    {
        /** @var Operator $operator */
        $operator = $this->admin->getObject($id);

        return $this->downloadDocument($id, $downloadHandler, $operator, 'employmentContractImageFile', $operator->getEmploymentContractImage());
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
//                'operators' => $operators
            ]
        );
    }

    public function generatePayslipsAction(Request $request)
    {
        $formData = $request->request->get('app_generate_payslips');
        try {
            $em = $this->getDoctrine()->getManager();
            $i = 0;
            /** @var Operator $operators */
            $operators = $formData['operators'];
            /** @var Operator $operator */
            foreach ($operators as $operator) {
                $operator = $em->getRepository(Operator::class)->find($operator);
                $fromDate = DateTime::createFromFormat('d/m/Y', $formData['fromDate']);
                $toDate = DateTime::createFromFormat('d/m/Y', $formData['toDate']);
                $payslip = new Payslip();
                $payslip->setOperator($operator);
                $payslip->setFromDate($fromDate);
                $payslip->setToDate($toDate);
                $em->persist($payslip);
                $totalAmount = 0;
                $operatorDefaultLines = $operator->getPayslipOperatorDefaultLines();
                if ($operatorDefaultLines) {
                    foreach ($operator->getPayslipOperatorDefaultLines() as $defaultLine) {
                        $payslipLine = $this->makePayslipLineFromDefaultPayslipLine($defaultLine);
                        $payslip->addPayslipLine($payslipLine);
                        $totalAmount += $payslipLine->getAmount();
                    }
                }
                $payslip->setTotalAmount($totalAmount);
                $em->persist($payslip);
                $em->flush();
                ++$i;
            }

            $this->addFlash(
                'success',
                'Nóminas generadas: '.$i.'.'
            );

            return new RedirectResponse($this->generateUrl('admin_app_payslip_payslip_list'));
        } catch (\Exception $exception) {
            $this->addFlash(
                'warning',
                'No se han podido generar las nòminas. Error en el formulario.'
            );

            return new RedirectResponse($this->generateUrl('admin_app_payslip_payslip_list'));
        }
    }

    private function makePayslipLineFromDefaultPayslipLine(PayslipOperatorDefaultLine $payslipOperatorDefaultLine): PayslipLine
    {
        $payslipLine = new PayslipLine();
        $payslipLine->setPayslipLineConcept($payslipOperatorDefaultLine->getPayslipLineConcept());
        $payslipLine->setPriceUnit($payslipOperatorDefaultLine->getPriceUnit());
        $payslipLine->setUnits($payslipOperatorDefaultLine->getUnits());
        $payslipLine->setAmount($payslipOperatorDefaultLine->getAmount());

        return $payslipLine;
    }
}
