<?php

namespace App\Controller\Admin\Partner;

use App\Controller\Admin\BaseAdminController;
use App\Entity\Partner\Partner;
use App\Repository\Partner\PartnerRepository;
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
    public function editAction(Request $request, $id = null): RedirectResponse|Response
    {
        $id = $request->get($this->admin->getIdParameter());

        /** @var Partner $partner */
        $partner = $this->admin->getObject($id);
        if (!$partner) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }

        return parent::editAction($request);
    }

    /**
     * @param int $id
     *
     * @return JsonResponse
     */
    public function getJsonPartnerByIdAction($id): JsonResponse
    {
        /** @var Partner $partner */
        $partner = $this->admin->getObject($id);
        if (!$partner) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
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
    public function getPartnerContactsByIdAction($id): JsonResponse
    {
        /** @var Partner $partner */
        $partner = $this->admin->getObject($id);
        if (!$partner) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
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
    public function getJsonDeliveryNotesByIdAction($id): JsonResponse
    {
        /** @var Partner $partner */
        $partner = $this->admin->getObject($id);
        if (!$partner) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }

        $serializer = $this->container->get('serializer');
        $serializedDeliveryNotes = $serializer->serialize($partner->getSaleDeliveryNotes(), 'json', ['groups' => ['api']]);

        return new JsonResponse($serializedDeliveryNotes);
    }

    public function getJsonBuildingSitesByIdAction(int $id): JsonResponse
    {
        /** @var Partner $partner */
        $partner = $this->admin->getObject($id);
        if (!$partner) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }

        $serializer = $this->container->get('serializer');
        $serializedBuildingSites = $serializer->serialize($partner->getBuildingSites(), 'json', ['groups' => ['api']]);

        return new JsonResponse($serializedBuildingSites);
    }

    public function getJsonOrdersByIdAction(int $id): JsonResponse
    {
        /** @var Partner $partner */
        $partner = $this->admin->getObject($id);
        if (!$partner) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }

        $serializer = $this->container->get('serializer');
        $serializedOrders = $serializer->serialize($partner->getOrders(), 'json', ['groups' => ['api']]);

        return new JsonResponse($serializedOrders);
    }

    public function getJsonProjectsByIdAction(int $id): JsonResponse
    {
        /** @var Partner $partner */
        $partner = $this->admin->getObject($id);
        if (!$partner) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }

        $serializer = $this->container->get('serializer');
        $serializedProjects = $serializer->serialize($partner->getProjects(), 'json', ['groups' => ['api']]);

        return new JsonResponse($serializedProjects);
    }

    public function checkIfCifNifIsUsedInAnotherPartnersAction(int $id, Request $request): JsonResponse
    {
        $request = $this->resolveRequest($request);
        $cifNif = $request->get('cifNif');
        /** @var Partner $partner */
        $partner = $this->admin->getObject($id);
        if (!$partner) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }
        /** @var PartnerRepository $partnerRepository */
        $partnerRepository = $this->em->getRepository(Partner::class);
        $partnersWithSameCifNif = $partnerRepository->getPartnersWithSameCifNifExceptCurrent($partner, $cifNif);
        $serializer = $this->container->get('serializer');
        $serializedPartners = $serializer->serialize($partnersWithSameCifNif, 'json', ['groups' => ['api']]);

        return new JsonResponse($serializedPartners);
    }

    public function checkIfPartnerIsBlockedAction(int $id): JsonResponse
    {
        /** @var Partner $partner */
        $partner = $this->admin->getObject($id);
        if (!$partner) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }

        return new JsonResponse(['isBlocked' => $partner->isBlocked()]);
    }
}
