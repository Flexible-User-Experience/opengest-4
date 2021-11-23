<?php

namespace App\Controller\Admin\Sale;

use App\Controller\Admin\BaseAdminController;
use App\Entity\Sale\SaleServiceTariff;
use App\Entity\Sale\SaleTariff;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\JsonResponse;

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
    public function getJsonSaleTariffByIdAction($id, $partnerId = null)
    {
        /** @var SaleServiceTariff $saleServiceTariff */
        $saleServiceTariff = $this->admin->getObject($id);
        if (!$saleServiceTariff) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }
        $serializer = $this->container->get('serializer');
        $saleTariffs = $saleServiceTariff->getSaleTariffs();
        if ($partnerId) {
            $saleTariffs = $saleTariffs->filter(function (SaleTariff $saleTariff) use ($partnerId) {
                return !$saleTariff->getPartner() || ($saleTariff->getPartner()->getId() == $partnerId);
            });
            $saleTariffs = new ArrayCollection($saleTariffs->getValues());
        }
        $serializedSaleTariff = $serializer->serialize($saleTariffs, 'json', ['groups' => ['apiSaleTariff']]);

        return new JsonResponse($serializedSaleTariff);
    }
}
