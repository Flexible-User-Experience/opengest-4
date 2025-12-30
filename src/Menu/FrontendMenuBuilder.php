<?php

namespace App\Menu;

use App\Entity\Web\Complement;
use App\Entity\Web\Service;
use App\Entity\Vehicle\Vehicle;
use App\Entity\Vehicle\VehicleCategory;
use App\Entity\Web\Work;
use App\Repository\Web\ComplementRepository;
use App\Repository\Web\ServiceRepository;
use App\Repository\Vehicle\VehicleCategoryRepository;
use App\Repository\Web\WorkRepository;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class FrontendMenuBuilder.
 *
 * @category Menu
 *
 * @author   David Romaní <david@flux.cat>
 */
class FrontendMenuBuilder
{
    /**
     * @var FactoryInterface
     */
    private FactoryInterface $factory;

    /**
     * @var AuthorizationCheckerInterface
     */
    private AuthorizationCheckerInterface $ac;

    /**
     * @var TokenStorageInterface
     */
    private TokenStorageInterface $ts;

    /**
     * @var VehicleCategoryRepository
     */
    private VehicleCategoryRepository $vcr;

    /**
     * @var ServiceRepository
     */
    private ServiceRepository $sr;

    /**
     * @var WorkRepository
     */
    private WorkRepository $wr;

    /**
     * @var ComplementRepository
     */
    private ComplementRepository $cr;

    /**
     * @var RouterInterface
     */
    private RouterInterface $router;

    /**
     * Methods.
     */

    /**
     * @param FactoryInterface              $factory
     * @param AuthorizationCheckerInterface $ac
     * @param TokenStorageInterface         $ts
     * @param VehicleCategoryRepository     $vcr
     * @param ServiceRepository             $sr
     * @param WorkRepository                $wr
     * @param ComplementRepository          $cr
     * @param RouterInterface               $router
     */
    public function __construct(
        FactoryInterface $factory,
        AuthorizationCheckerInterface $ac,
        TokenStorageInterface $ts,
        VehicleCategoryRepository $vcr,
        ServiceRepository $sr,
        WorkRepository $wr,
        ComplementRepository $cr,
        RouterInterface $router
    ) {
        $this->factory = $factory;
        $this->ac = $ac;
        $this->ts = $ts;
        $this->vcr = $vcr;
        $this->sr = $sr;
        $this->wr = $wr;
        $this->router = $router;
        $this->cr = $cr;
    }

    /**
     * @param RequestStack $requestStack
     *
     * @return ItemInterface
     */
    public function createTopMenu(RequestStack $requestStack): ItemInterface
    {
        $route = $requestStack->getCurrentRequest()->get('_route');
        $menu = $this->factory->createItem('root');
        $menu->setChildrenAttribute('class', 'nav navbar-nav navbar-right');
        if ($this->ts->getToken() && $this->ac->isGranted('ROLE_CMS')) {
            $menu->addChild(
                'admin',
                array(
                    'label' => '[ CMS ]',
                    'route' => 'sonata_admin_dashboard',
                )
            );
        }
        $menu->addChild(
            'front_services',
            array(
                'label' => 'Servicios',
                'route' => 'front_services',
                'current' => 'front_services' == $route || 'front_service_detail' == $route,
            )
        );
        $menu->addChild(
            'front_vehicles',
            array(
                'label' => 'Vehículos',
                'route' => 'front_vehicles',
                'current' => 'front_vehicles' == $route || 'front_vehicle_detail' == $route || 'front_vehicles_category' == $route,
            )
        );
        $menu->addChild(
            'front_works',
            array(
                'label' => 'Trabajos',
                'route' => 'front_works',
                'current' => 'front_works' == $route || 'front_work_detail' == $route,
            )
        );
        $menu->addChild(
            'front_complement',
            array(
                'label' => 'Accesorios',
                'route' => 'front_complement',
                'current' => 'front_complement' == $route || 'front_complement_detail' == $route,
            )
        );
        $menu->addChild(
            'front_company',
            array(
                'label' => 'Empresa',
                'route' => 'front_company',
            )
        );

        return $menu;
    }

    /**
     * @return ItemInterface
     */
    public function createVehicleCategoryMenu(): ItemInterface
    {
        $menu = $this->factory->createItem('rootCategory');
        $menu->setChildrenAttribute('class', 'nav nav-pills nav-stacked nav-yellow');
        $categories = $this->vcr->findEnabledSortedByName();

        /** @var VehicleCategory $category */
        foreach ($categories as $category) {
            if ($category->getVehicles()->count() > 0) {
                $menu->addChild(
                    $category->getSlug(),
                    array(
                        'label' => ucfirst(strtolower($category->getName())),
                        'route' => 'front_vehicles_category',
                        'routeParameters' => array(
                            'slug' => $category->getSlug(),
                        ),
                    )
                );
            }
        }

        return $menu;
    }

    /**
     * @return ItemInterface
     */
    public function createServiceMenu(): ItemInterface
    {
        $menu = $this->factory->createItem('rootService');
        $menu->setChildrenAttribute('class', 'nav nav-pills nav-stacked nav-yellow');
        $services = $this->sr->findEnabledSortedByPositionAndName();

        /** @var Service $service */
        foreach ($services as $service) {
            $menu->addChild(
                $service->getSlug(),
                array(
                    'label' => ucfirst(strtolower($service->getName())),
                    'route' => 'front_service_detail',
                    'routeParameters' => array(
                        'slug' => $service->getSlug(),
                    ),
                )
            );
        }

        return $menu;
    }

    /**
     * @return ItemInterface
     */
    public function createFooterMenu(): ItemInterface
    {
        $menu = $this->factory->createItem('rootFooter');
        $menu->setChildrenAttribute('class', 'nav nav-pills nav-stacked nav-yellow');
        $menu->addChild(
            'front_about',
            array(
                'label' => 'Sobre este sitio',
                'route' => 'front_about',
            )
        );
        $menu->addChild(
            'front_privacy',
            array(
                'label' => 'Privacidad',
                'route' => 'front_privacy',
            )
        );
        $menu->addChild(
            'front_sitemap',
            array(
                'label' => 'Mapa del web',
                'route' => 'front_sitemap',
            )
        );

        return $menu;
    }

    /**
     * @return ItemInterface
     */
    public function createSitemapMenu(): ItemInterface
    {
        $menu = $this->factory->createItem('rootSitemap');
        $menu->setChildrenAttribute('class', 'ul-sitemap');
        $menu->addChild(
            $this->router->generate('front_homepage', [], UrlGeneratorInterface::ABSOLUTE_URL),
            array(
                'label' => 'Inicio',
                'route' => 'front_homepage',
            )
        );
        $serviceMenu = $menu->addChild(
            $this->router->generate('front_services', [], UrlGeneratorInterface::ABSOLUTE_URL),
            array(
                'label' => 'Servicios',
                'route' => 'front_services',
            )
        );
        $services = $this->sr->findEnabledSortedByPositionAndName();
        /** @var Service $service */
        foreach ($services as $service) {
            $item = $serviceMenu->addChild(
                $this->router->generate('front_service_detail', ['slug' => $service->getSlug()], UrlGeneratorInterface::ABSOLUTE_URL),
                array(
                    'label' => ucfirst(strtolower($service->getName())),
                    'route' => 'front_service_detail',
                    'routeParameters' => array(
                        'slug' => $service->getSlug(),
                    ),
                )
            );
            $item->setExtra('updated_at', $service->getUpdatedAt());
        }
        $vehicleMenu = $menu->addChild(
            $this->router->generate('front_vehicles', [], UrlGeneratorInterface::ABSOLUTE_URL),
            array(
                'label' => 'Vehículos',
                'route' => 'front_vehicles',
            )
        );
        $vehicleCategories = $this->vcr->findEnabledSortedByName();
        /** @var VehicleCategory $vehicleCategory */
        foreach ($vehicleCategories as $vehicleCategory) {
            $vehicleChildMenu = $vehicleMenu->addChild(
                $this->router->generate('front_vehicles_category', ['slug' => $vehicleCategory->getSlug()], UrlGeneratorInterface::ABSOLUTE_URL),
                array(
                    'label' => ucfirst(strtolower($vehicleCategory->getName())),
                    'route' => 'front_vehicles_category',
                    'routeParameters' => array(
                        'slug' => $vehicleCategory->getSlug(),
                    ),
                )
            );
            $vehicleChildMenu->setExtra('updated_at', $vehicleCategory->getUpdatedAt());
            /** @var Vehicle $vehicle */
            foreach ($vehicleCategory->getVehicles() as $vehicle) {
                $item = $vehicleChildMenu->addChild(
                    $this->router->generate('front_vehicle_detail', ['category_slug' => $vehicle->getCategory()->getSlug(), 'slug' => $vehicle->getSlug()], UrlGeneratorInterface::ABSOLUTE_URL),
                    array(
                        'label' => strtoupper($vehicle->getName()),
                        'route' => 'front_vehicle_detail',
                        'routeParameters' => array(
                            'category_slug' => $vehicle->getCategory()->getSlug(),
                            'slug' => $vehicle->getSlug(),
                        ),
                    )
                );
                $item->setExtra('updated_at', $vehicle->getUpdatedAt());
            }
        }
        $workMenu = $menu->addChild(
            $this->router->generate('front_works', [], UrlGeneratorInterface::ABSOLUTE_URL),
            array(
                'label' => 'Trabajos',
                'route' => 'front_works',
            )
        );
        $works = $this->wr->findEnabledSortedByName();
        /** @var Work $work */
        foreach ($works as $work) {
            $item = $workMenu->addChild(
                $this->router->generate('front_work_detail', ['slug' => $work->getSlug()], UrlGeneratorInterface::ABSOLUTE_URL),
                array(
                    'label' => ucfirst(strtolower($work->getName())),
                    'route' => 'front_work_detail',
                    'routeParameters' => array(
                        'slug' => $work->getSlug(),
                    ),
                )
            );
            $item->setExtra('updated_at', $work->getUpdatedAt());
        }
        $complementMenu = $menu->addChild(
            $this->router->generate('front_complement', [], UrlGeneratorInterface::ABSOLUTE_URL),
            array(
                'label' => 'Accesorios',
                'route' => 'front_complement',
            )
        );
        $complements = $this->cr->findEnabledSortedByName();
        /** @var Complement $complement */
        foreach ($complements as $complement) {
            $item = $complementMenu->addChild(
                $this->router->generate('front_complement_detail', ['slug' => $complement->getSlug()], UrlGeneratorInterface::ABSOLUTE_URL),
                array(
                    'label' => ucfirst(strtolower($complement->getName())),
                    'route' => 'front_complement_detail',
                    'routeParameters' => array(
                        'slug' => $complement->getSlug(),
                    ),
                )
            );
            $item->setExtra('updated_at', $complement->getUpdatedAt());
        }
        $menu->addChild(
            $this->router->generate('front_company', [], UrlGeneratorInterface::ABSOLUTE_URL),
            array(
                'label' => 'Empresa',
                'route' => 'front_company',
            )
        );
        $menu->addChild(
            $this->router->generate('front_about', [], UrlGeneratorInterface::ABSOLUTE_URL),
            array(
                'label' => 'Sobre este sitio',
                'route' => 'front_about',
            )
        );
        $menu->addChild(
            $this->router->generate('front_privacy', [], UrlGeneratorInterface::ABSOLUTE_URL),
            array(
                'label' => 'Privacidad',
                'route' => 'front_privacy',
            )
        );
        $menu->addChild(
            $this->router->generate('front_sitemap', [], UrlGeneratorInterface::ABSOLUTE_URL),
            array(
                'label' => 'Mapa del web',
                'route' => 'front_sitemap',
            )
        );

        return $menu;
    }
}
