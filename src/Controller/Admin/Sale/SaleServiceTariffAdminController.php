<?php

namespace App\Controller\Admin\Sale;

use App\Controller\Admin\BaseAdminController;
use App\Entity\Partner\Partner;
use App\Entity\Sale\SaleServiceTariff;
use App\Entity\Sale\SaleTariff;
use App\Repository\Sale\SaleTariffRepository;
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
    public function getJsonSaleTariffByIdAction($id, $partnerId = null): JsonResponse
    {
        /** @var SaleServiceTariff $saleServiceTariff */
        $saleServiceTariff = $this->admin->getObject($id);
        if (!$saleServiceTariff) {
            throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
        }
        $serializer = $this->container->get('serializer');
        $saleTariffRepository = $this->repositoriesManager->getSaleTariffRepository();
        $saleTariffs = $saleTariffRepository->getFilteredBySaleServiceTariffWithoutPartnerSortedByDate($saleServiceTariff);
        if ($partnerId) {
            $partner = $this->admin->getModelManager()->find(Partner::class, $partnerId);
            if ($partner) {
                $saleTariffsFromPartner = $saleTariffRepository->getFilteredByPartnerAndSaleServiceTariffSortedByDate($partner, $saleServiceTariff);
                $saleTariffs =
                    array_merge($saleTariffsFromPartner, $saleTariffs)
                ;
            } else {
                throw $this->createNotFoundException(sprintf('unable to find the partner with id: %s', $id));
            }
        }
//        $saleTariffs = $saleServiceTariff->getSaleTariffs();
//        if ($partnerId) {
//            $saleTariffs = $saleTariffs->filter(function (SaleTariff $saleTariff) use ($partnerId) {
//                return (!$saleTariff->getPartner() || ($saleTariff->getPartner()->getId() == $partnerId)) && $saleTariff->getEnabled();
//            });
//            $saleTariffs = new ArrayCollection($saleTariffs->getValues());
//        }
        $serializedSaleTariff = $serializer->serialize($saleTariffs, 'json', ['groups' => ['apiSaleTariff']]);

        return new JsonResponse($serializedSaleTariff);
    }
}
