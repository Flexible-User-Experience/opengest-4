<?php

namespace App\Controller\Admin\Enterprise;

use App\Controller\Admin\BaseAdminController;
use App\Entity\Enterprise\Enterprise;
use App\Entity\Setting\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Vich\UploaderBundle\Handler\DownloadHandler;

/**
 * Class EnterpriseAdminController.
 */
class EnterpriseAdminController extends BaseAdminController
{
    /**
     * @param int|null $id
     *
     * @return RedirectResponse|Response
     */
    public function editAction($id = null): Response
    {
        $request = $this->getRequest();
        $id = $request->get($this->admin->getIdParameter());

        /** @var Enterprise $enterprise */
        $enterprise = $this->admin->getObject($id);
        if (!$enterprise) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }
        /** GuardService $guardService */
        $guardService = $this->container->get('app.guard_service');
        if (!$guardService->isOwnEnterprise($enterprise)) {
            throw $this->createAccessDeniedException(sprintf('forbidden object with id: %s', $id));
        }

        return parent::editAction($id);
    }

    /**
     * @param int|null $id
     *
     * @return RedirectResponse
     */
    public function changeAction($id = null)
    {
        $request = $this->getRequest();
        $id = $request->get($this->admin->getIdParameter());

        /** @var Enterprise $enterprise */
        $enterprise = $this->admin->getObject($id);
        if (!$enterprise) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }

        /** @var User $user */
        $user = $this->getUser();
        $user->setDefaultEnterprise($enterprise);

        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('sonata_admin_dashboard');
    }

    public function downloadTc1receiptAction($id, DownloadHandler $downloadHandler): Response
    {
        /** @var Enterprise $enterprise */
        $enterprise = $this->admin->getObject($id);

        return $this->downloadDocument($id, $downloadHandler, $enterprise, 'tc1ReceiptFile', $enterprise->getTc1Receipt());
    }

    public function downloadTc2receiptAction($id, DownloadHandler $downloadHandler): Response
    {
        /** @var Enterprise $enterprise */
        $enterprise = $this->admin->getObject($id);

        return $this->downloadDocument($id, $downloadHandler, $enterprise, 'tc2ReceiptFile', $enterprise->getTc2Receipt());
    }

    public function downloadSsRegistrationAction($id, DownloadHandler $downloadHandler): Response
    {
        /** @var Enterprise $enterprise */
        $enterprise = $this->admin->getObject($id);

        return $this->downloadDocument($id, $downloadHandler, $enterprise, 'ssRegistrationFile', $enterprise->getSsRegistration());
    }

    public function downloadSsPaymentCertificateAction($id, DownloadHandler $downloadHandler): Response
    {
        /** @var Enterprise $enterprise */
        $enterprise = $this->admin->getObject($id);

        return $this->downloadDocument($id, $downloadHandler, $enterprise, 'ssPaymentCertificateFile', $enterprise->getSsPaymentCertificate());
    }

    public function downloadRc1InsuranceAction($id, DownloadHandler $downloadHandler): Response
    {
        /** @var Enterprise $enterprise */
        $enterprise = $this->admin->getObject($id);

        return $this->downloadDocument($id, $downloadHandler, $enterprise, 'rc1InsuranceFile', $enterprise->getRc1Insurance());
    }

    public function downloadRc2InsuranceAction($id, DownloadHandler $downloadHandler): Response
    {
        /** @var Enterprise $enterprise */
        $enterprise = $this->admin->getObject($id);

        return $this->downloadDocument($id, $downloadHandler, $enterprise, 'rc2InsuranceFile', $enterprise->getRc2Insurance());
    }

    public function downloadRcReceiptAction($id, DownloadHandler $downloadHandler): Response
    {
        /** @var Enterprise $enterprise */
        $enterprise = $this->admin->getObject($id);

        return $this->downloadDocument($id, $downloadHandler, $enterprise, 'rcReceiptFile', $enterprise->getRcReceipt());
    }

    public function downloadPreventionServiceContractAction($id, DownloadHandler $downloadHandler): Response
    {
        /** @var Enterprise $enterprise */
        $enterprise = $this->admin->getObject($id);

        return $this->downloadDocument($id, $downloadHandler, $enterprise, 'preventionServiceContractFile', $enterprise->getPreventionServiceContract());
    }

    public function downloadPreventionServiceInvoiceAction($id, DownloadHandler $downloadHandler): Response
    {
        /** @var Enterprise $enterprise */
        $enterprise = $this->admin->getObject($id);

        return $this->downloadDocument($id, $downloadHandler, $enterprise, 'preventionServiceInvoiceFile', $enterprise->getPreventionServiceInvoice());
    }

    public function downloadPreventionServiceReceiptAction($id, DownloadHandler $downloadHandler): Response
    {
        /** @var Enterprise $enterprise */
        $enterprise = $this->admin->getObject($id);

        return $this->downloadDocument($id, $downloadHandler, $enterprise, 'preventionServiceReceiptFile', $enterprise->getPreventionServiceReceipt());
    }

    public function downloadOccupationalAccidentsInsuranceAction($id, DownloadHandler $downloadHandler): Response
    {
        /** @var Enterprise $enterprise */
        $enterprise = $this->admin->getObject($id);

        return $this->downloadDocument($id, $downloadHandler, $enterprise, 'occupationalAccidentsInsuranceFile', $enterprise->getOccupationalAccidentsInsurance());
    }

    public function downloadOccupationalReceiptAction($id, DownloadHandler $downloadHandler): Response
    {
        /** @var Enterprise $enterprise */
        $enterprise = $this->admin->getObject($id);

        return $this->downloadDocument($id, $downloadHandler, $enterprise, 'occupationalReceiptFile', $enterprise->getOccupationalReceipt());
    }

    public function downloadLaborRiskAssessmentAction($id, DownloadHandler $downloadHandler): Response
    {
        /** @var Enterprise $enterprise */
        $enterprise = $this->admin->getObject($id);

        return $this->downloadDocument($id, $downloadHandler, $enterprise, 'laborRiskAssessmentFile', $enterprise->getLaborRiskAssessment());
    }

    public function downloadSecurityPlanAction($id, DownloadHandler $downloadHandler): Response
    {
        /** @var Enterprise $enterprise */
        $enterprise = $this->admin->getObject($id);

        return $this->downloadDocument($id, $downloadHandler, $enterprise, 'securityPlanFile', $enterprise->getSecurityPlan());
    }

    public function downloadReaCertificateAction($id, DownloadHandler $downloadHandler): Response
    {
        /** @var Enterprise $enterprise */
        $enterprise = $this->admin->getObject($id);

        return $this->downloadDocument($id, $downloadHandler, $enterprise, 'reaCertificateFile', $enterprise->getReaCertificate());
    }

    public function downloadOilCertificateAction($id, DownloadHandler $downloadHandler): Response
    {
        /** @var Enterprise $enterprise */
        $enterprise = $this->admin->getObject($id);

        return $this->downloadDocument($id, $downloadHandler, $enterprise, 'oilCertificateFile', $enterprise->getOilCertificate());
    }

    public function downloadGencatPaymentCertificateAction($id, DownloadHandler $downloadHandler): Response
    {
        /** @var Enterprise $enterprise */
        $enterprise = $this->admin->getObject($id);

        return $this->downloadDocument($id, $downloadHandler, $enterprise, 'gencatPaymentCertificateFile', $enterprise->getGencatPaymentCertificate());
    }

    public function downloadDeedsOfPowersAction($id, DownloadHandler $downloadHandler): Response
    {
        /** @var Enterprise $enterprise */
        $enterprise = $this->admin->getObject($id);

        return $this->downloadDocument($id, $downloadHandler, $enterprise, 'deedsOfPowersFile', $enterprise->getDeedsOfPowers());
    }

    public function downloadIaeRegistrationAction($id, DownloadHandler $downloadHandler): Response
    {
        /** @var Enterprise $enterprise */
        $enterprise = $this->admin->getObject($id);

        return $this->downloadDocument($id, $downloadHandler, $enterprise, 'iaeRegistrationFile', $enterprise->getIaeRegistration());
    }

    public function downloadIaeReceiptAction($id, DownloadHandler $downloadHandler): Response
    {
        /** @var Enterprise $enterprise */
        $enterprise = $this->admin->getObject($id);

        return $this->downloadDocument($id, $downloadHandler, $enterprise, 'iaeReceiptFile', $enterprise->getIaeReceipt());
    }

    public function downloadMutualPartnershipAction($id, DownloadHandler $downloadHandler): Response
    {
        /** @var Enterprise $enterprise */
        $enterprise = $this->admin->getObject($id);

        return $this->downloadDocument($id, $downloadHandler, $enterprise, 'mutualPartnershipFile', $enterprise->getMutualPartnership());
    }

    public function downloadDeedOfIncorporationAction($id, DownloadHandler $downloadHandler): Response
    {
        /** @var Enterprise $enterprise */
        $enterprise = $this->admin->getObject($id);

        return $this->downloadDocument($id, $downloadHandler, $enterprise, 'deedOfIncorporationFile', $enterprise->getDeedOfIncorporation());
    }

    public function downloadTaxIdentificationNumberCardAction($id, DownloadHandler $downloadHandler): Response
    {
        /** @var Enterprise $enterprise */
        $enterprise = $this->admin->getObject($id);

        return $this->downloadDocument($id, $downloadHandler, $enterprise, 'taxIdentificationNumberCardFile', $enterprise->getTaxIdentificationNumberCard());
    }
}
