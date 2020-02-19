<?php

namespace App\Controller\Front;

use App\Entity\Enterprise\Enterprise;
use App\Entity\Vehicle\Vehicle;
use App\Entity\Vehicle\VehicleCategory;
use App\Enum\ConstantsEnum;
use App\Repository\Vehicle\VehicleCategoryRepository;
use App\Repository\Vehicle\VehicleRepository;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class VehiclesController.
 *
 * @category Controller
 */
class VehiclesController extends AbstractController
{
    /**
     * @Route("/vehiculos", name="front_vehicles")
     *
     * @param VehicleCategoryRepository $vcr
     *
     * @return RedirectResponse
     *
     * @throws EntityNotFoundException
     */
    public function vehiclesAction(VehicleCategoryRepository $vcr)
    {
        $categories = $vcr->findEnabledSortedByNameForWeb();
        if (0 == count($categories)) {
            throw new EntityNotFoundException();
        }
        /** @var VehicleCategory $categoria */
        $categoria = $categories[0];

        return $this->redirectToRoute('front_vehicles_category', [
            'slug' => $categoria->getSlug(),
        ]);
    }

    /**
     * @Route("/vehiculo/{category_slug}/{slug}", name="front_vehicle_detail")
     *
     * @param VehicleRepository $vr
     * @param string            $slug
     *
     * @return Response
     *
     * @throws EntityNotFoundException
     */
    public function vehicleDetailAction(VehicleRepository $vr, $slug)
    {
        /** @var Vehicle|null $vehicle */
        $vehicle = $vr->findOneBy(['slug' => $slug]);
        if (!$vehicle) {
            throw new EntityNotFoundException();
        }
        if (Enterprise::GRUAS_ROMANI_TIN != $vehicle->getEnterprise()->getTaxIdentificationNumber()) {
            throw new EntityNotFoundException();
        }

        return $this->render('frontend/vehicle_detail.html.twig', [
            'vehicle' => $vehicle,
        ]);
    }

    /**
     * @Route("/vehiculos/categoria/{slug}/{page}", name="front_vehicles_category")
     *
     * @param VehicleCategoryRepository $vcr
     * @param VehicleRepository         $vr
     * @param string                    $slug
     * @param int                       $page
     *
     * @return Response
     *
     * @throws EntityNotFoundException
     */
    public function vehiclesCategoryAction(VehicleCategoryRepository $vcr, VehicleRepository $vr, $slug, $page = 1)
    {
        /** @var VehicleCategory|null $category */
        $category = $vcr->findOneBy(['slug' => $slug]);
        if (!$category) {
            throw new EntityNotFoundException();
        }
        $vehicles = $vr->findEnabledSortedByPositionAndNameForWeb($category);
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate($vehicles, $page, ConstantsEnum::FRONTEND_ITEMS_PER_PAGE_LIMIT);

        return $this->render('frontend/vehicles.html.twig', [
            'category' => $category,
            'pagination' => $pagination,
        ]);
    }
}
