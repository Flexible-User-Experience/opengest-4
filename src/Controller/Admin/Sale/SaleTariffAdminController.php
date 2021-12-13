<?php

namespace App\Controller\Admin\Sale;

use App\Controller\Admin\BaseAdminController;
use App\Entity\Enterprise\EnterpriseHolidays;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SaleTariffAdminController.
 */
class SaleTariffAdminController extends BaseAdminController
{
    /**
     * @param int|null $id
     *
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, $id = null): Response
    {
        $id = $request->get($this->admin->getIdParameter());

        /** @var EnterpriseHolidays $enterpriseHoliday */
        $saleTariff = $this->admin->getObject($id);
        if (!$saleTariff) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }

        return parent::editAction($request);
    }

    /**
     * @param int $id
     *
     * @return JsonResponse
     */
    public function getJsonSaleTariffByIdAction($id)
    {
        /** @var EnterpriseHolidays $enterpriseHoliday */
        $saleTariff = $this->admin->getObject($id);
        if (!$saleTariff) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }

        $serializer = $this->container->get('serializer');
        $serializedSaleTariff = $serializer->serialize($saleTariff, 'json', ['groups' => ['apiSaleTariff']]);

        return new JsonResponse($serializedSaleTariff);
    }
}
