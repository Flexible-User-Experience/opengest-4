<?php

namespace App\Admin\Vehicle;

use App\Admin\AbstractBaseAdmin;
use App\Entity\Sale\SaleServiceTariff;
use App\Entity\Vehicle\Vehicle;
use App\Entity\Vehicle\VehicleCategory;
use App\Entity\Vehicle\VehicleMaintenance;
use App\Enum\UserRolesEnum;
use Doctrine\ORM\NonUniqueResultException;
use Sonata\AdminBundle\Datagrid\DatagridInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\Operator\EqualOperatorType;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\Form\Type\BooleanType;
use Sonata\Form\Type\CollectionType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;

/**
 * Class VehicleAdmin.
 *
 * @category Admin
 *
 * @author   Wils Iglesias <wiglesias83@gmail.com>
 */
class VehicleAdmin extends AbstractBaseAdmin
{
    /**
     * @var string
     */
    protected $classnameLabel = 'Vehicles';

    /**
     * @var string
     */
    protected $baseRoutePattern = 'vehicles/vehicle';

    /**
     * Methods.
     */
    protected function configureDefaultSortValues(array &$sortValues): void
    {
        $sortValues[DatagridInterface::SORT_ORDER] = 'ASC';
        $sortValues[DatagridInterface::SORT_BY] = 'name';
        $sortValues[DatagridInterface::PER_PAGE] = 100;
    }

    /**
     * Configure route collection.
     */
    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        parent::configureRoutes($collection);
        $collection
            ->add('downloadMainImage', $this->getRouterIdParameter().'/main-image')
            ->add('downloadChassisImage', $this->getRouterIdParameter().'/chasis')
            ->add('downloadTechnicalDatasheet1', $this->getRouterIdParameter().'/ficha-tecnica')
            ->add('downloadTechnicalDatasheet2', $this->getRouterIdParameter().'/ficha-tecnica2')
            ->add('downloadLoadTable', $this->getRouterIdParameter().'/tabla-carga')
            ->add('downloadReachDiagram', $this->getRouterIdParameter().'/diagrama-alcances')
            ->add('downloadTrafficCertificate', $this->getRouterIdParameter().'/permiso-circulacion')
            ->add('downloadDimensions', $this->getRouterIdParameter().'/dimensiones')
            ->add('downloadTransportCard', $this->getRouterIdParameter().'/tarjeta-transporte')
            ->add('downloadTrafficInsurance', $this->getRouterIdParameter().'/seguro-circulacion')
            ->add('downloadItv', $this->getRouterIdParameter().'/itv')
            ->add('downloadItc', $this->getRouterIdParameter().'/itc')
            ->add('downloadCEDeclaration', $this->getRouterIdParameter().'/declaracion-ce')
            ->add('generateDocumentation', 'generate-documentation')
            ->add('batch')
            ->remove('delete');
    }

    public function configureExportFields(): array
    {
        return [
            'name',
            'vehicleRegistrationNumber',
            'chassisBrand',
            'chassisNumber',
            'vehicleBrand',
            'vehicleModel',
            'serialNumber',
            'link',
            'mileage',
            'tonnage',
            'enabled',
        ];
    }

    public function configureBatchActions(array $actions): array
    {
        if (
            $this->hasRoute('edit')
        ) {
            $actions['downloadDocumentation'] = [
                'label' => 'admin.action.download_documentation',
                'ask_confirmation' => false,
            ];
        }

        return $actions;
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->tab('General')
            ->with('Recursos', $this->getFormMdSuccessBoxArray(3))
            ->add(
                'mainImageFile',
                FileType::class,
                [
                    'label' => 'mainImage',
                    'help' => $this->getMainImageHelperFormMapperWithThumbnail(),
                    'help_html' => true,
                    'required' => false,
                ]
            )
            ->add(
                'attatchmentPDFFile',
                FileType::class,
                [
                    'label' => 'Document',
                    'help' => $this->getDownloadPdfButton(),
                    'help_html' => true,
                    'required' => false,
                ]
            )
            ->add(
                'category',
                EntityType::class,
                [
                    'label' => 'category',
                    'class' => VehicleCategory::class,
                    'required' => true,
                    'query_builder' => $this->rm->getVehicleCategoryRepository()->getEnabledSortedByNameQBForAdmin(),
                ]
            )
            ->end()
            ->with('General', $this->getFormMdSuccessBoxArray(6))
            ->add(
                'name',
                TextType::class,
                [
                    'label' => 'admin.label.vehicle',
                ]
            )
            ->add(
                'vehicleRegistrationNumber',
                TextType::class,
                [
                    'label' => 'admin.label.vehicle_registration_number',
                    'required' => true,
                ]
            )
            ->add(
                'chassisBrand',
                TextType::class,
                [
                    'label' => 'admin.label.chassis_brand',
                    'required' => true,
                ]
            )
            ->add(
                'chassisNumber',
                TextType::class,
                [
                    'label' => 'admin.label.chassis_number',
                    'required' => false,
                ]
            )
            ->add(
                'vehicleBrand',
                TextType::class,
                [
                    'label' => 'admin.label.vehicle_brand',
                    'required' => false,
                ]
            )
            ->add(
                'vehicleModel',
                TextType::class,
                [
                    'label' => 'admin.label.vehicle_model',
                    'required' => false,
                ]
            )
            ->add(
                'serialNumber',
                TextType::class,
                [
                    'label' => 'admin.label.serial_number',
                    'required' => false,
                ]
            )
            ->end()
            ->with('Controles', $this->getFormMdSuccessBoxArray(3))
            ->add(
                'link',
                UrlType::class,
                [
                    'label' => 'admin.label.manufacturer_webpage',
                    'required' => false,
                ]
            )
            ->add(
                'mileage',
                NumberType::class,
                [
                    'label' => 'admin.label.mileage',
                    'required' => false,
                ]
            )
            ->add(
                'tonnage',
                EntityType::class,
                [
                    'label' => 'admin.label.tonnage',
                    'class' => SaleServiceTariff::class,
                    'required' => false,
                    'query_builder' => $this->rm->getSaleServiceTariffRepository()->getEnabledSortedByNameQB(),
                ]
            )
            ->add(
                'enabled',
                CheckboxType::class,
                [
                    'label' => 'Actiu',
                    'required' => false,
                ]
            )
            ->end()
            ->end()
        ;
        if ($this->id($this->getSubject())) { // is edit mode, disable on new subjetcs
            $formMapper
                ->tab('Documentación')
                ->with('admin.with.vehicle.chassis_image', $this->getFormMdSuccessBoxArray(3))
                ->add(
                    'chassisImageFile',
                    FileType::class,
                    [
                        'label' => false,
                        'help' => $this->getDocumentHelper('admin_app_vehicle_vehicle_downloadChassisImage', 'chassisImage'),
                        'help_html' => true,
                        'required' => false,
                    ]
                )
                ->end()
                ->with('admin.with.vehicle.technical_datasheet_1', $this->getFormMdSuccessBoxArray(3))
                ->add(
                    'technicalDatasheet1File',
                    FileType::class,
                    [
                        'label' => false,
                        'help' => $this->getDocumentHelper('admin_app_vehicle_vehicle_downloadTechnicalDatasheet1', 'technicalDatasheet1'),
                        'help_html' => true,
                        'required' => false,
                    ]
                )
                ->end()
                ->with('admin.with.vehicle.technical_datasheet_2', $this->getFormMdSuccessBoxArray(3))
                ->add(
                    'technicalDatasheet2File',
                    FileType::class,
                    [
                        'label' => false,
                        'help' => $this->getDocumentHelper('admin_app_vehicle_vehicle_downloadTechnicalDatasheet2', 'technicalDatasheet2'),
                        'help_html' => true,
                        'required' => false,
                    ]
                )
                ->end()
                ->with('admin.with.vehicle.load_table', $this->getFormMdSuccessBoxArray(3))
                ->add(
                    'loadTableFile',
                    FileType::class,
                    [
                        'label' => false,
                        'help' => $this->getDocumentHelper('admin_app_vehicle_vehicle_downloadLoadTable', 'loadTable'),
                        'help_html' => true,
                        'required' => false,
                    ]
                )
                ->end()
                ->with('admin.with.vehicle.reach_diagram', $this->getFormMdSuccessBoxArray(3))
                ->add(
                    'reachDiagramFile',
                    FileType::class,
                    [
                        'label' => false,
                        'help' => $this->getDocumentHelper('admin_app_vehicle_vehicle_downloadReachDiagram', 'reachDiagram'),
                        'help_html' => true,
                        'required' => false,
                    ]
                )
                ->end()
                ->with('admin.with.vehicle.traffic_certificate', $this->getFormMdSuccessBoxArray(3))
                ->add(
                    'trafficCertificateFile',
                    FileType::class,
                    [
                        'label' => false,
                        'help' => $this->getDocumentHelper('admin_app_vehicle_vehicle_downloadTrafficCertificate', 'trafficCertificate'),
                        'help_html' => true,
                        'required' => false,
                    ]
                )
                ->end()
                ->with('admin.with.vehicle.dimensions', $this->getFormMdSuccessBoxArray(3))
                ->add(
                    'dimensionsFile',
                    FileType::class,
                    [
                        'label' => false,
                        'help' => $this->getDocumentHelper('admin_app_vehicle_vehicle_downloadDimensions', 'dimensions'),
                        'help_html' => true,
                        'required' => false,
                    ]
                )
                ->end()
                ->with('admin.with.vehicle.transport_card', $this->getFormMdSuccessBoxArray(3))
                ->add(
                    'transportCardFile',
                    FileType::class,
                    [
                        'label' => false,
                        'help' => $this->getDocumentHelper('admin_app_vehicle_vehicle_downloadTransportCard', 'transportCard'),
                        'help_html' => true,
                        'required' => false,
                    ]
                )
                ->end()
                ->with('admin.with.vehicle.traffic_insurance', $this->getFormMdSuccessBoxArray(3))
                ->add(
                    'trafficInsuranceFile',
                    FileType::class,
                    [
                        'label' => false,
                        'help' => $this->getDocumentHelper('admin_app_vehicle_vehicle_downloadTrafficInsurance', 'trafficInsurance'),
                        'help_html' => true,
                        'required' => false,
                    ]
                )
                ->end()
                ->with('admin.with.vehicle.itv', $this->getFormMdSuccessBoxArray(3))
                ->add(
                    'itvFile',
                    FileType::class,
                    [
                        'label' => false,
                        'help' => $this->getDocumentHelper('admin_app_vehicle_vehicle_downloadItv', 'itv'),
                        'help_html' => true,
                        'required' => false,
                    ]
                )
                ->end()
                ->with('admin.with.vehicle.itc', $this->getFormMdSuccessBoxArray(3))
                ->add(
                    'itcFile',
                    FileType::class,
                    [
                        'label' => false,
                        'help' => $this->getDocumentHelper('admin_app_vehicle_vehicle_downloadItc', 'itc'),
                        'help_html' => true,
                        'required' => false,
                    ]
                )
                ->end()
                ->with('Declaración CE', $this->getFormMdSuccessBoxArray(3))
                ->add(
                    'CEDeclarationFile',
                    FileType::class,
                    [
                        'label' => false,
                        'help' => $this->getDocumentHelper('admin_app_vehicle_vehicle_downloadCEDeclaration', 'CEDeclaration'),
                        'help_html' => true,
                        'required' => false,
                    ]
                )
                ->end()
                ->with('admin.label.other_documents', $this->getFormMdSuccessBoxArray(6))
                ->add(
                    'documents',
                    CollectionType::class,
                    [
                        'required' => false,
                        'error_bubbling' => true,
                        'label' => false,
                    ],
                    [
                        'edit' => 'inline',
                        'inline' => 'table',
                    ]
                )
                ->end()
                ->end()
                ->tab('Revisiones')
                ->with('Revisiones', $this->getFormMdSuccessBoxArray(7))
                ->add(
                    'vehicleCheckings',
                    CollectionType::class,
                    [
                        'required' => false,
                        'error_bubbling' => true,
                        'label' => false,
                    ],
                    [
                        'edit' => 'inline',
                        'inline' => 'table',
                    ]
                )
                ->end()
                ->end()
                ->tab('Permisos especiales')
                ->with('Permisos especiales', $this->getFormMdSuccessBoxArray(7))
                ->add(
                    'vehicleSpecialPermits',
                    CollectionType::class,
                    [
                        'required' => false,
                        'error_bubbling' => true,
                        'label' => false,
                        'type_options' => [
                            'delete' => false,
                        ],
                    ],
                    [
                        'edit' => 'inline',
                        'inline' => 'table',
                    ]
                )
                ->end()
                ->end()
                ->tab('Libro historial')
                ->with('Recursos', $this->getFormMdSuccessBoxArray(3))
                ->end()
                ->end()
                ->tab('Matenimientos')
                ->with('Líneas de mantenimiento', $this->getFormMdSuccessBoxArray(10))
                ->add(
                    'vehicleMaintenances',
                    CollectionType::class,
                    [
                        'required' => false,
                        'error_bubbling' => true,
                        'label' => false,
                    ],
                    [
                        'edit' => 'inline',
                        'inline' => 'table',
                    ]
                )
                ->end()
                ->end()
                ->tab('Consumos')
                ->with('Consumos', $this->getFormMdSuccessBoxArray(6))
                ->add(
                    'vehicleConsumptions',
                    CollectionType::class,
                    [
                        'required' => false,
                        'error_bubbling' => true,
                        'label' => false,
                        'btn_add' => false,
                        'disabled' => true,
                        'type_options' => [
                            'delete' => false,
                        ],
                    ],
                    [
                        'edit' => 'inline',
                        'inline' => 'table',
                    ]
                )
                ->end()
                ->end()
                ->tab('Tacógrafo')
                ->with('Tacógrafo', $this->getFormMdSuccessBoxArray(6))
                ->add(
                    'vehicleDigitalTachographs',
                    CollectionType::class,
                    [
                        'required' => false,
                        'error_bubbling' => true,
                        'label' => false,
                    ],
                    [
                        'edit' => 'inline',
                        'inline' => 'table',
                    ]
                )
                ->end()
                ->end()
            ;
        }
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add(
                'name',
                null,
                [
                    'label' => 'admin.label.vehicle',
                    'show_filter' => true,
                ]
            )
            ->add(
                'vehicleRegistrationNumber',
                null,
                [
                    'label' => 'admin.label.vehicle_registration_number',
                ]
            )
            ->add(
                'chassisBrand',
                null,
                [
                    'label' => 'admin.label.chassis_brand',
                ]
            )
            ->add(
                'chassisNumber',
                null,
                [
                    'label' => 'admin.label.chassis_number',
                ]
            )
            ->add(
                'vehicleBrand',
                null,
                [
                    'label' => 'admin.label.vehicle_brand',
                ]
            )
            ->add(
                'vehicleModel',
                null,
                [
                    'label' => 'admin.label.vehicle_model',
                ]
            )
            ->add(
                'serialNumber',
                null,
                [
                    'label' => 'admin.label.serial_number',
                ]
            )
            ->add(
                'link',
                null,
                [
                    'label' => 'link',
                ]
            )
            ->add(
                'enabled',
                null,
                [
                    'label' => 'enabled',
                ]
            );
    }

    protected function configureDefaultFilterValues(array &$filterValues): void
    {
        $filterValues['enabled'] = [
            'type' => EqualOperatorType::TYPE_EQUAL,
            'value' => BooleanType::TYPE_YES,
        ];
    }

    public function configureQuery(ProxyQueryInterface $query): ProxyQueryInterface
    {
        $queryBuilder = parent::configureQuery($query);
        if (!$this->acs->isGranted(UserRolesEnum::ROLE_ADMIN)) {
            $queryBuilder
                ->andWhere($queryBuilder->getRootAliases()[0].'.enterprise = :enterprise')
                ->setParameter('enterprise', $this->getUserLogedEnterprise())
            ;
        }

        return $queryBuilder;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add(
                'name',
                null,
                [
                    'label' => 'admin.label.vehicle',
                ]
            )
            ->add(
                'vehicleRegistrationNumber',
                null,
                [
                    'label' => 'admin.label.vehicle_registration_number',
                    'required' => true,
                ]
            )
            ->add(
                'chassisBrand',
                null,
                [
                    'label' => 'admin.label.chassis_brand',
                    'required' => true,
                ]
            )
            ->add(
                'enabled',
                null,
                [
                    'label' => 'enabled',
                    'editable' => true,
                ]
            )
            ->add(
                '_action',
                'actions',
                [
                    'actions' => [
                        'show' => ['template' => 'admin/buttons/list__action_show_button.html.twig'],
                        'edit' => ['template' => 'admin/buttons/list__action_edit_button.html.twig'],
                    ],
                    'label' => 'admin.actions',
                ]
            );
    }

    /**
     * @param Vehicle $object
     *
     * @throws NonUniqueResultException
     */
    public function prePersist($object): void
    {
        $object->setEnterprise($this->getUserLogedEnterprise());
        $vehicleMaintenances = $object->getVehicleMaintenances();
        /** @var VehicleMaintenance $vehicleMaintenance */
        foreach ($vehicleMaintenances as $vehicleMaintenance) {
            $this->disablePreviousMaintenance($vehicleMaintenance);
            if ($this->vmm->checkIfMaintenanceNeedsCheck($vehicleMaintenance)) {
                $vehicleMaintenance->setNeedsCheck(true);
                $this->em->persist($vehicleMaintenance);
                $this->em->flush();
            }
        }
    }

    /**
     * @param Vehicle $object
     */
    public function preUpdate($object): void
    {
        $vehicleMaintenances = $object->getVehicleMaintenances();
        /** @var VehicleMaintenance $vehicleMaintenance */
        foreach ($vehicleMaintenances as $vehicleMaintenance) {
            $this->disablePreviousMaintenance($vehicleMaintenance);
            if ($this->vmm->checkIfMaintenanceNeedsCheck($vehicleMaintenance)) {
                $vehicleMaintenance->setNeedsCheck(true);
                $this->em->persist($vehicleMaintenance);
                $this->em->flush();
            }
        }
    }
}
