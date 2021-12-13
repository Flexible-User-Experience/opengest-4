<?php

namespace App\Controller\Admin\Partner;

use App\Controller\Admin\BaseAdminController;
use App\Entity\Partner\Partner;
use App\Service\GuardService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PartnerAdminController.
 */
class PartnerAdminController extends BaseAdminController
{
    /**
     * @param int|null $id
     *
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, $id = null): Response
    {
        $id = $request->get($this->admin->getIdParameter());

        /** @var Partner $partner */
        $partner = $this->admin->getObject($id);
        if (!$partner) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }
        /** @var GuardService $guardService */
        $guardService = $this->container->get('app.guard_service');
        if (!$guardService->isOwnPartner($partner)) {
            throw $this->createAccessDeniedException(sprintf('forbidden object with id: %s', $id));
        }

        return parent::editAction($request);
    }

    /**
     * @param int $id
     *
     * @return JsonResponse
     */
    public function getJsonPartnerByIdAction($id)
    {
        /** @var Partner $partner */
        $partner = $this->admin->getObject($id);
        if (!$partner) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }
        /** @var GuardService $guardService */
        $guardService = $this->container->get('app.guard_service');
        if (!$guardService->isOwnPartner($partner)) {
            throw $this->createAccessDeniedException(sprintf('forbidden object with id: %s', $id));
        }

        $serializer = $this->container->get('serializer');
        $serializedPartner = $serializer->serialize($partner, 'json', ['groups' => ['api']]);

        return new JsonResponse($serializedPartner);
    }

    /**
     * @param int $id
     *
     * @return JsonResponse
     */
    public function getPartnerContactsByIdAction($id)
    {
        /** @var Partner $partner */
        $partner = $this->admin->getObject($id);
        if (!$partner) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }
        /** @var GuardService $guardService */
        $guardService = $this->container->get('app.guard_service');
        if (!$guardService->isOwnPartner($partner)) {
            throw $this->createAccessDeniedException(sprintf('forbidden object with id: %s', $id));
        }

        $serializer = $this->container->get('serializer');
        $serializedContacts = $serializer->serialize($partner->getContacts(), 'json', ['groups' => ['api']]);

        return new JsonResponse($serializedContacts);
    }

    /**
     * @param int $id
     *
     * @return JsonResponse
     */
    public function getJsonDeliveryNotesByIdAction($id)
    {
        /** @var Partner $partner */
        $partner = $this->admin->getObject($id);
        if (!$partner) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }
        /** @var GuardService $guardService */
        $guardService = $this->container->get('app.guard_service');
        if (!$guardService->isOwnPartner($partner)) {
            throw $this->createAccessDeniedException(sprintf('forbidden object with id: %s', $id));
        }

        $serializer = $this->container->get('serializer');
        $serializedDeliveryNotes = $serializer->serialize($partner->getSaleDeliveryNotes(), 'json', ['groups' => ['api']]);

        return new JsonResponse($serializedDeliveryNotes);
    }

    /**
     * @param int $id
     *
     * @return JsonResponse
     */
    public function getJsonBuildingSitesByIdAction($id)
    {
        /** @var Partner $partner */
        $partner = $this->admin->getObject($id);
        if (!$partner) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }
        /** @var GuardService $guardService */
        $guardService = $this->container->get('app.guard_service');
        if (!$guardService->isOwnPartner($partner)) {
            throw $this->createAccessDeniedException(sprintf('forbidden object with id: %s', $id));
        }

        $serializer = $this->container->get('serializer');
        $serializedBuildingSites = $serializer->serialize($partner->getBuildingSites(), 'json', ['groups' => ['api']]);

        return new JsonResponse($serializedBuildingSites);
    }
}
