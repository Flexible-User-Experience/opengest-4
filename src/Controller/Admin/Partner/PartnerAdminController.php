<?php

namespace App\Controller\Admin\Partner;

use App\Controller\Admin\BaseAdminController;
use App\Entity\Partner\Partner;
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
}
