<?php

namespace App\Controller\Admin\Operator;

use App\Controller\Admin\BaseAdminController;
use App\Entity\Operator\Operator;
use App\Entity\Payslip\Payslip;
use App\Entity\Payslip\PayslipLine;
use App\Entity\Payslip\PayslipOperatorDefaultLine;
use App\Enum\OperatorDocumentsEnum;
use App\Form\Type\Operator\GenerateDocumentationFormType;
use App\Form\Type\Operator\GeneratePayslipsFormType;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\String\UnicodeString;
use Symfony\Contracts\Translation\TranslatorInterface;
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
    public function editAction(Request $request, $id = null): Response
    {
        $id = $request->get($this->admin->getIdParameter());

        /** @var Operator $operator */
        $operator = $this->admin->getObject($id);
        $operatorAbsences = $operator->getOperatorAbsences();
        if (!$operator) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }

        return parent::editAction($request);
    }

    public function downloadProfilePhotoImageAction(Request $request, $id, DownloadHandler $downloadHandler): Response
    {
        /** @var Operator $operator */
        $operator = $this->admin->getObject($id);

        return $this->downloadDocument($request, $id, $downloadHandler, $operator, 'profilePhotoImageFile', $operator->getProfilePhotoImage());
    }

    public function downloadTaxIdentificationNumberImageAction(Request $request, $id, DownloadHandler $downloadHandler): Response
    {
        /** @var Operator $operator */
        $operator = $this->admin->getObject($id);

        return $this->downloadDocument($request, $id, $downloadHandler, $operator, 'taxIdentificationNumberImageFile', $operator->getTaxIdentificationNumberImage());
    }

    public function downloadDrivingLicenseImageAction(Request $request, $id, DownloadHandler $downloadHandler): Response
    {
        /** @var Operator $operator */
        $operator = $this->admin->getObject($id);

        return $this->downloadDocument($request, $id, $downloadHandler, $operator, 'drivingLicenseImageFile', $operator->getDrivingLicenseImage());
    }

    public function downloadCranesOperatorLicenseImageAction(Request $request, $id, DownloadHandler $downloadHandler): Response
    {
        /** @var Operator $operator */
        $operator = $this->admin->getObject($id);

        return $this->downloadDocument($request, $id, $downloadHandler, $operator, 'cranesOperatorLicenseImageFile', $operator->getCranesOperatorLicenseImage());
    }

    public function downloadMedicalCheckImageAction(Request $request, $id, DownloadHandler $downloadHandler): Response
    {
        /** @var Operator $operator */
        $operator = $this->admin->getObject($id);

        return $this->downloadDocument($request, $id, $downloadHandler, $operator, 'medicalCheckImageFile', $operator->getMedicalCheckImage());
    }

    public function downloadEpisImageAction(Request $request, $id, DownloadHandler $downloadHandler): Response
    {
        /** @var Operator $operator */
        $operator = $this->admin->getObject($id);

        return $this->downloadDocument($request, $id, $downloadHandler, $operator, 'episImageFile', $operator->getEpisImage());
    }

    public function downloadTrainingDocumentImageAction(Request $request, $id, DownloadHandler $downloadHandler): Response
    {
        /** @var Operator $operator */
        $operator = $this->admin->getObject($id);

        return $this->downloadDocument($request, $id, $downloadHandler, $operator, 'trainingDocumentImageFile', $operator->getTrainingDocumentImage());
    }

    public function downloadInformationImageAction(Request $request, $id, DownloadHandler $downloadHandler): Response
    {
        /** @var Operator $operator */
        $operator = $this->admin->getObject($id);

        return $this->downloadDocument($request, $id, $downloadHandler, $operator, 'informationImageFile', $operator->getInformationImage());
    }

    public function downloadUseOfMachineryAuthorizationImageAction(Request $request, $id, DownloadHandler $downloadHandler): Response
    {
        /** @var Operator $operator */
        $operator = $this->admin->getObject($id);

        return $this->downloadDocument($request, $id, $downloadHandler, $operator, 'useOfMachineryAuthorizationImageFile', $operator->getUseOfMachineryAuthorizationImage());
    }

    public function downloadDischargeSocialSecurityImageAction(Request $request, $id, DownloadHandler $downloadHandler): Response
    {
        /** @var Operator $operator */
        $operator = $this->admin->getObject($id);

        return $this->downloadDocument($request, $id, $downloadHandler, $operator, 'dischargeSocialSecurityImageFile', $operator->getDischargeSocialSecurityImage());
    }

    public function downloadEmploymentContractImageAction(Request $request, $id, DownloadHandler $downloadHandler): Response
    {
        /** @var Operator $operator */
        $operator = $this->admin->getObject($id);

        return $this->downloadDocument($request, $id, $downloadHandler, $operator, 'employmentContractImageFile', $operator->getEmploymentContractImage());
    }

    public function batchActionCreatePayslipFromOperators(ProxyQueryInterface $selectedModelQuery, Request $request)
    {
        $this->admin->checkAccess('edit');
        $form = $this->createForm(GeneratePayslipsFormType::class);
        $form->handleRequest($request);
        /** @var Operator[] $operators */
        $operators = $selectedModelQuery->execute()->getQuery()->getResult();
        $form->get('operators')->setData($operators);

        return $this->renderWithExtraParams(
            'admin/operator/payslipGeneration.html.twig',
            [
                'generatePayslipsForm' => $form->createView(),
            ]
        );
    }

    public function batchActionDownloadDocumentation(ProxyQueryInterface $selectedModelQuery, Request $request): Response
    {
        $this->admin->checkAccess('edit');
        $form = $this->createForm(GenerateDocumentationFormType::class);
        $form->handleRequest($request);
        /** @var Operator[] $operators */
        $operators = $selectedModelQuery->execute()->getQuery()->getResult();
        $form->get('operators')->setData($operators);

        return $this->renderWithExtraParams(
            'admin/operator/documentationGeneration.html.twig',
            [
                'generateDocumentationForm' => $form->createView(),
            ]
        );
    }

    public function generatePayslipsAction(Request $request)
    {
        $formData = $request->request->get('app_generate_payslips');
        try {
            $em = $this->em->getManager();
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

    public function generateDocumentationAction(Request $request, TranslatorInterface $translator)
    {
        $formData = $request->request->get('app_generate_payslips');
        /** @var Operator $operators */
        $operatorIds = $formData['operators'];
        $documentIds = $formData['documentation'];
        $documentation = [];
        if (!$operatorIds) {
            $this->addFlash('warning', 'No hay operarios seleccionados');
        }
        $operatorRepository = $this->em->getRepository(Operator::class);
        /** @var Operator[] $operators */
        $operators = new ArrayCollection();
        /* @var Operator $operator */
        foreach ($operatorIds as $operatorId) {
            $operator = $operatorRepository->findOneBy(['id' => $operatorId]);
            $operators[] = $operator;
            if (!$operator) {
                continue;
            }
            foreach ($documentIds as $documentId) {
                $documentName = OperatorDocumentsEnum::getName($documentId);
                $documentNameNotTranslated = OperatorDocumentsEnum::getReversedEnumArray()[$documentId];
                $method = new UnicodeString('GET_'.$documentName);
                $fileName = call_user_func([$operator, $method->lower()->camel()->toString()]);
                if ('' != $fileName) {
                    $filePath = $this->getParameter('kernel.project_dir').'/var/uploads/images/operator/'.$fileName;
                    if (file_exists($filePath)) {
                        $fileContents = file_get_contents($filePath);
                        $documentation[$operator->getId()][] = [
                            'name' => $documentName,
                            'nameTranslated' => $translator->trans($documentNameNotTranslated, [], 'admin'),
                            'content' => $fileContents,
                            'fileType' => explode('.', $fileName)[1],
                        ];
                    }
                }
            }
        }

        return new Response($this->operatorDocumentationPdfManager->outputSingle($operators, $documentation), 200, ['Content-type' => 'application/pdf']);
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
