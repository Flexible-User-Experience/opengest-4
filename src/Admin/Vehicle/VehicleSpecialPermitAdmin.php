<?php

namespace App\Admin\Vehicle;

use App\Admin\AbstractBaseAdmin;
use App\Entity\Vehicle\Vehicle;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\DoctrineORMAdminBundle\Filter\DateFilter;
use Sonata\Form\Type\DatePickerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

/**
 * Class VehicleSpecialPermitAdmin.
 *
 * @category Admin
 *
 * @author   Jordi Sort <jordi.sort@mirmit.com>
 */
class VehicleSpecialPermitAdmin extends AbstractBaseAdmin
{
    /**
     * @var string
     */
    protected $classnameLabel = 'Permisos especiales';

    /**
     * @var string
     */
    protected $baseRoutePattern = 'vehiculos/permisos-especiales';

    /**
     * @var string
     */
    protected $translationDomain = 'admin';

    /**
     * @var array
     */
    protected $datagridValues = [
        '_sort_by' => 'expeditionDate',
        '_sort_order' => 'desc',
    ];

    /**
     * Methods.
     */
    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        parent::configureRoutes($collection);
        $collection
            ->add('downloadRouteImage', $this->getRouterIdParameter().'/imagen-ruta')
            ;
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        if ($this->getRootCode() == $this->getCode()) {
            $formMapper
                ->with('General', $this->getFormMdSuccessBoxArray(4))
                ->add(
                    'vehicle',
                    EntityType::class,
                    [
                        'label' => 'admin.label.vehicle',
                        'required' => true,
                        'class' => Vehicle::class,
                        'choice_label' => 'name',
                        'query_builder' => $this->rm->getVehicleRepository()->getFilteredByEnterpriseEnabledSortedByNameQB($this->getUserLogedEnterprise()),
                    ]
                );
        } else {
            $formMapper
                ->with('General', $this->getFormMdSuccessBoxArray(4))
                ->add(
                    'vehicle',
                    EntityType::class,
                    [
                        'label' => 'admin.label.vehicle',
                        'required' => true,
                        'class' => Vehicle::class,
                        'choice_label' => 'name',
                        'query_builder' => $this->rm->getVehicleRepository()->getFilteredByEnterpriseEnabledSortedByNameQB($this->getUserLogedEnterprise()),
                        'attr' => [
                            'hidden' => 'true',
                        ],
                    ]
                );
        }
        $formMapper
            ->add(
                'expedientNumber',
                null,
                [
                    'label' => 'admin.label.expedient_number',
                    'required' => false,
                ]
            )
            ->add(
                'expeditionDate',
                DatePickerType::class,
                [
                    'label' => 'admin.label.expedition_date',
                    'format' => 'dd/MM/yyyy',
                    'dp_default_date' => (new \DateTime())->format('d/m/Y'),
                    'required' => true,
                ]
            )
            ->add(
                'expiryDate',
                DatePickerType::class,
                [
                    'label' => 'admin.label.expiry_date',
                    'format' => 'dd/MM/yyyy',
                    'dp_default_date' => (new \DateTime())->format('d/m/Y'),
                    'required' => true,
                ]
            )
            ->add(
                'additionalVehicle',
                null,
                [
                    'label' => 'admin.label.additional_vehicle',
                    'required' => false,
                ]
            )
            ->add(
                'additionalRegistrationNumber',
                null,
                [
                    'label' => 'admin.label.additional_registration_number',
                    'required' => false,
                ]
            )
            ->end()
            ;
        if ($this->getRootCode() == $this->getCode()) {
            $formMapper
                ->with('Caracterísitcas', $this->getFormMdSuccessBoxArray(4))
                ->add(
                    'totalLength',
                    null,
                    [
                        'label' => 'admin.label.total_length',
                        'required' => false,
                    ]
                )
                ->add(
                    'totalHeight',
                    null,
                    [
                        'label' => 'admin.label.total_height',
                        'required' => false,
                    ]
                )
                ->add(
                    'totalWidth',
                    null,
                    [
                        'label' => 'admin.label.total_width',
                        'required' => false,
                    ]
                )
                ->add(
                    'maximumWeight',
                    null,
                    [
                        'label' => 'admin.label.maximum_weight',
                        'required' => false,
                    ]
                )
                ->add(
                    'numberOfAxes',
                    null,
                    [
                        'label' => 'admin.label.number_of_axes',
                        'required' => false,
                    ]
                )
                ->add(
                    'loadContent',
                    null,
                    [
                        'label' => 'admin.label.load_content',
                        'required' => false,
                    ]
                )
                ->end()
                ->with('Ruta y notas', $this->getFormMdSuccessBoxArray(4))
                ->add(
                    'route',
                    null,
                    [
                        'label' => 'admin.label.route',
                        'required' => false,
                    ]
                )
                ;

            if ($this->id($this->getSubject())) { // is edit mode, disable on new subjetcs
                $formMapper
                    ->add(
                        'routeImageFile',
                        FileType::class,
                        [
                            'label' => 'admin.label.route_image',
                            'help' => $this->getDocumentHelper('admin_app_vehicle_vehiclespecialpermit_downloadRouteImage', 'routeImage'),
                            'required' => false,
                        ]
                    );
            }
            $formMapper
                ->add(
                    'notes',
                    null,
                    [
                        'label' => 'admin.label.notes',
                        'required' => false,
                    ]
                )
                ->end();
        }
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add(
                'vehicle',
                null,
                [
                    'label' => 'admin.label.vehicle',
                ],
                EntityType::class,
                [
                    'class' => Vehicle::class,
                    'query_builder' => $this->rm->getVehicleRepository()->getFilteredByEnterpriseEnabledSortedByNameQB($this->getUserLogedEnterprise()),
                ]
            )
            ->add(
                'expeditionDate',
                DateFilter::class,
                [
                    'label' => 'admin.label.expedition_date',
                    'field_type' => DatePickerType::class,
                ],
                null,
                [
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy',
                ]
            )
            ->add(
                'expiryDate',
                DateFilter::class,
                [
                    'label' => 'admin.label.expiry_date',
                    'field_type' => DatePickerType::class,
                ]
            )
            ->add(
                'expedientNumber',
                null,
                [
                    'label' => 'admin.label.expedient_number',
                ]
            )
        ;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add(
                'vehicle',
                null,
                [
                    'label' => 'Vehicle',
                    'editable' => false,
                    'associated_property' => 'name',
                    'sortable' => true,
                    'sort_field_mapping' => ['fieldName' => 'name'],
                    'sort_parent_association_mappings' => [['fieldName' => 'vehicle']],
                ]
            )
            ->add(
                'expeditionDate',
                'date',
                [
                    'label' => 'admin.label.expedition_date',
                    'format' => 'd/m/Y',
                ]
            )
            ->add(
                'expiryDate',
                'date',
                [
                    'label' => 'admin.label.expiry_date',
                    'format' => 'd/m/Y',
                ]
            )
            ->add(
                'expedientNumber',
                null,
                [
                    'label' => 'admin.label.expedient_number',
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
                    'label' => 'Accions',
                ]
            )
        ;
    }
}
