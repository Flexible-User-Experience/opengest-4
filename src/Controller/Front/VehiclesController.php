<?php

namespace App\Controller\Front;

use App\Entity\Enterprise\Enterprise;
use App\Entity\Vehicle\Vehicle;
use App\Entity\Vehicle\VehicleCategory;
use App\Enum\ConstantsEnum;
use Doctrine\ORM\EntityNotFoundException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class VehiclesController.
 *
 * @category Controller
 */
class VehiclesController extends Controller
{
    /**
     * @Route("/vehiculos", name="front_vehicles")
     *
     * @return RedirectResponse
     *
     * @throws EntityNotFoundException
     */
    public function vehiclesAction()
    {
        $categories = $this->getDoctrine()->getRepository('App:Vehicle\VehicleCategory')->findEnabledSortedByNameForWeb();
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
     * @param $slug
     *
     * @return Response
     *
     * @throws EntityNotFoundException
     */
    public function vehicleDetailAction($slug)
    {
        /** @var Vehicle|null $vehicle */
        $vehicle = $this->getDoctrine()->getRepository('App:Vehicle\Vehicle')->findOneBy(['slug' => $slug]);
        if (!$vehicle) {
            throw new EntityNotFoundException();
        }
        if (Enterprise::GRUAS_ROMANI_TIN != $vehicle->getEnterprise()->getTaxIdentificationNumber()) {
            throw new EntityNotFoundException();
        }

        return $this->render(':Frontend:vehicle_detail.html.twig', [
            'vehicle' => $vehicle,
        ]);
    }

    /**
     * @Route("/vehiculos/categoria/{slug}/{page}", name="front_vehicles_category")
     *
     * @param $slug
     * @param int $page
     *
     * @return Response
     *
     * @throws EntityNotFoundException
     */
    public function vehiclesCategoryAction($slug, $page = 1)
    {
        $category = $this->getDoctrine()->getRepository('App:Vehicle\VehicleCategory')->findOneBy(['slug' => $slug]);
        if (!$category) {
            throw new EntityNotFoundException();
        }
        $vehicles = $this->getDoctrine()->getRepository('App:Vehicle\Vehicle')->findEnabledSortedByPositionAndNameForWeb($category);
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate($vehicles, $page, ConstantsEnum::FRONTEND_ITEMS_PER_PAGE_LIMIT);

        return $this->render(':Frontend:vehicles.html.twig', [
            'category' => $category,
            'pagination' => $pagination,
        ]);
    }
}
