<?php

namespace App\Controller\Admin\Sale;

use App\Controller\Admin\BaseAdminController;
use App\Entity\Enterprise\EnterpriseHolidays;
use App\Entity\Sale\SaleServiceTariff;
use App\Service\GuardService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SaleServiceTariffAdminController.
 */
class SaleServiceTariffAdminController extends BaseAdminController
{
    /**
     * @param int $id
     *
     * @return JsonResponse
     */
    public function getJsonSaleTariffByIdAction($id)
    {
        /** @var SaleServiceTariff $saleServiceTariff */
        $saleServiceTariff = $this->admin->getObject($id);
        if (!$saleServiceTariff) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }

        $serializer = $this->container->get('serializer');
        $serializedSaleTariff = $serializer->serialize($saleServiceTariff->getSaleTariffs(), 'json', array('groups' => array('apiSaleTariff')));

        return new JsonResponse($serializedSaleTariff);
    }
}
