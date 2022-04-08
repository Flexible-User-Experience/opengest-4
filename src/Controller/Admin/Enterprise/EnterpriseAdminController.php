<?php

namespace App\Controller\Admin\Enterprise;

use App\Controller\Admin\BaseAdminController;
use App\Entity\Enterprise\Enterprise;
use App\Entity\Setting\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
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
    public function editAction(Request $request, $id = null): Response
    {
        $id = $request->get($this->admin->getIdParameter());

        /** @var Enterprise $enterprise */
        $enterprise = $this->admin->getObject($id);
        if (!$enterprise) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }

        return parent::editAction($request);
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

    public function downloadTc1receiptAction(Request $request, $id, DownloadHandler $downloadHandler): Response
    {
        /** @var Enterprise $enterprise */
        $enterprise = $this->admin->getObject($id);

        return $this->downloadDocument($request, $id, $downloadHandler, $enterprise, 'tc1ReceiptFile', $enterprise->getTc1Receipt());
    }

    public function downloadTc2receiptAction(Request $request, $id, DownloadHandler $downloadHandler): Response
    {
        /** @var Enterprise $enterprise */
        $enterprise = $this->admin->getObject($id);

        return $this->downloadDocument($request, $id, $downloadHandler, $enterprise, 'tc2ReceiptFile', $enterprise->getTc2Receipt());
    }

    public function downloadSsRegistrationAction(Request $request, $id, DownloadHandler $downloadHandler): Response
    {
        /** @var Enterprise $enterprise */
        $enterprise = $this->admin->getObject($id);

        return $this->downloadDocument($request, $id, $downloadHandler, $enterprise, 'ssRegistrationFile', $enterprise->getSsRegistration());
    }

    public function downloadSsPaymentCertificateAction(Request $request, $id, DownloadHandler $downloadHandler): Response
    {
        /** @var Enterprise $enterprise */
        $enterprise = $this->admin->getObject($id);

        return $this->downloadDocument($request, $id, $downloadHandler, $enterprise, 'ssPaymentCertificateFile', $enterprise->getSsPaymentCertificate());
    }

    public function downloadRc1InsuranceAction(Request $request, $id, DownloadHandler $downloadHandler): Response
    {
        /** @var Enterprise $enterprise */
        $enterprise = $this->admin->getObject($id);

        return $this->downloadDocument($request, $id, $downloadHandler, $enterprise, 'rc1InsuranceFile', $enterprise->getRc1Insurance());
    }

    public function downloadRc2InsuranceAction(Request $request, $id, DownloadHandler $downloadHandler): Response
    {
        /** @var Enterprise $enterprise */
        $enterprise = $this->admin->getObject($id);

        return $this->downloadDocument($request, $id, $downloadHandler, $enterprise, 'rc2InsuranceFile', $enterprise->getRc2Insurance());
    }

    public function downloadRcReceiptAction(Request $request, $id, DownloadHandler $downloadHandler): Response
    {
        /** @var Enterprise $enterprise */
        $enterprise = $this->admin->getObject($id);

        return $this->downloadDocument($request, $id, $downloadHandler, $enterprise, 'rcReceiptFile', $enterprise->getRcReceipt());
    }

    public function downloadPreventionServiceContractAction(Request $request, $id, DownloadHandler $downloadHandler): Response
    {
        /** @var Enterprise $enterprise */
        $enterprise = $this->admin->getObject($id);

        return $this->downloadDocument($request, $id, $downloadHandler, $enterprise, 'preventionServiceContractFile', $enterprise->getPreventionServiceContract());
    }

    public function downloadPreventionServiceInvoiceAction(Request $request, $id, DownloadHandler $downloadHandler): Response
    {
        /** @var Enterprise $enterprise */
        $enterprise = $this->admin->getObject($id);

        return $this->downloadDocument($request, $id, $downloadHandler, $enterprise, 'preventionServiceInvoiceFile', $enterprise->getPreventionServiceInvoice());
    }

    public function downloadPreventionServiceReceiptAction(Request $request, $id, DownloadHandler $downloadHandler): Response
    {
        /** @var Enterprise $enterprise */
        $enterprise = $this->admin->getObject($id);

        return $this->downloadDocument($request, $id, $downloadHandler, $enterprise, 'preventionServiceReceiptFile', $enterprise->getPreventionServiceReceipt());
    }

    public function downloadOccupationalAccidentsInsuranceAction(Request $request, $id, DownloadHandler $downloadHandler): Response
    {
        /** @var Enterprise $enterprise */
        $enterprise = $this->admin->getObject($id);

        return $this->downloadDocument($request, $id, $downloadHandler, $enterprise, 'occupationalAccidentsInsuranceFile', $enterprise->getOccupationalAccidentsInsurance());
    }

    public function downloadOccupationalReceiptAction(Request $request, $id, DownloadHandler $downloadHandler): Response
    {
        /** @var Enterprise $enterprise */
        $enterprise = $this->admin->getObject($id);

        return $this->downloadDocument($request, $id, $downloadHandler, $enterprise, 'occupationalReceiptFile', $enterprise->getOccupationalReceipt());
    }

    public function downloadLaborRiskAssessmentAction(Request $request, $id, DownloadHandler $downloadHandler): Response
    {
        /** @var Enterprise $enterprise */
        $enterprise = $this->admin->getObject($id);

        return $this->downloadDocument($request, $id, $downloadHandler, $enterprise, 'laborRiskAssessmentFile', $enterprise->getLaborRiskAssessment());
    }

    public function downloadSecurityPlanAction(Request $request, $id, DownloadHandler $downloadHandler): Response
    {
        /** @var Enterprise $enterprise */
        $enterprise = $this->admin->getObject($id);

        return $this->downloadDocument($request, $id, $downloadHandler, $enterprise, 'securityPlanFile', $enterprise->getSecurityPlan());
    }

    public function downloadReaCertificateAction(Request $request, $id, DownloadHandler $downloadHandler): Response
    {
        /** @var Enterprise $enterprise */
        $enterprise = $this->admin->getObject($id);

        return $this->downloadDocument($request, $id, $downloadHandler, $enterprise, 'reaCertificateFile', $enterprise->getReaCertificate());
    }

    public function downloadOilCertificateAction(Request $request, $id, DownloadHandler $downloadHandler): Response
    {
        /** @var Enterprise $enterprise */
        $enterprise = $this->admin->getObject($id);

        return $this->downloadDocument($request, $id, $downloadHandler, $enterprise, 'oilCertificateFile', $enterprise->getOilCertificate());
    }

    public function downloadGencatPaymentCertificateAction(Request $request, $id, DownloadHandler $downloadHandler): Response
    {
        /** @var Enterprise $enterprise */
        $enterprise = $this->admin->getObject($id);

        return $this->downloadDocument($request, $id, $downloadHandler, $enterprise, 'gencatPaymentCertificateFile', $enterprise->getGencatPaymentCertificate());
    }

    public function downloadDeedsOfPowersAction(Request $request, $id, DownloadHandler $downloadHandler): Response
    {
        /** @var Enterprise $enterprise */
        $enterprise = $this->admin->getObject($id);

        return $this->downloadDocument($request, $id, $downloadHandler, $enterprise, 'deedsOfPowersFile', $enterprise->getDeedsOfPowers());
    }

    public function downloadIaeRegistrationAction(Request $request, $id, DownloadHandler $downloadHandler): Response
    {
        /** @var Enterprise $enterprise */
        $enterprise = $this->admin->getObject($id);

        return $this->downloadDocument($request, $id, $downloadHandler, $enterprise, 'iaeRegistrationFile', $enterprise->getIaeRegistration());
    }

    public function downloadIaeReceiptAction(Request $request, $id, DownloadHandler $downloadHandler): Response
    {
        /** @var Enterprise $enterprise */
        $enterprise = $this->admin->getObject($id);

        return $this->downloadDocument($request, $id, $downloadHandler, $enterprise, 'iaeReceiptFile', $enterprise->getIaeReceipt());
    }

    public function downloadMutualPartnershipAction(Request $request, $id, DownloadHandler $downloadHandler): Response
    {
        /** @var Enterprise $enterprise */
        $enterprise = $this->admin->getObject($id);

        return $this->downloadDocument($request, $id, $downloadHandler, $enterprise, 'mutualPartnershipFile', $enterprise->getMutualPartnership());
    }

    public function downloadDeedOfIncorporationAction(Request $request, $id, DownloadHandler $downloadHandler): Response
    {
        /** @var Enterprise $enterprise */
        $enterprise = $this->admin->getObject($id);

        return $this->downloadDocument($request, $id, $downloadHandler, $enterprise, 'deedOfIncorporationFile', $enterprise->getDeedOfIncorporation());
    }

    public function downloadTaxIdentificationNumberCardAction(Request $request, $id, DownloadHandler $downloadHandler): Response
    {
        /** @var Enterprise $enterprise */
        $enterprise = $this->admin->getObject($id);

        return $this->downloadDocument($request, $id, $downloadHandler, $enterprise, 'taxIdentificationNumberCardFile', $enterprise->getTaxIdentificationNumberCard());
    }

    public function downloadLogoImageAction(Request $request, $id, DownloadHandler $downloadHandler): Response
    {
        /** @var Enterprise $enterprise */
        $enterprise = $this->admin->getObject($id);

        return $this->downloadDocument($request, $id, $downloadHandler, $enterprise, 'logoFile', $enterprise->getLogo());
    }
}
