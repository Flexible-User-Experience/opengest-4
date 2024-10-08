<?php

namespace App\Admin\Vehicle;

use App\Admin\AbstractBaseAdmin;
use App\Entity\Vehicle\Vehicle;
use Sonata\AdminBundle\Datagrid\DatagridInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\DoctrineORMAdminBundle\Filter\DateFilter;
use Sonata\DoctrineORMAdminBundle\Filter\DateRangeFilter;
use Sonata\Form\Type\DatePickerType;
use Sonata\Form\Type\DateRangePickerType;
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
     * Methods.
     */
    public function generateBaseRoutePattern(bool $isChildAdmin = false): string
    {
        return 'vehiculos/permisos-especiales';
    }

    protected function configureDefaultSortValues(array &$sortValues): void
    {
        $sortValues[DatagridInterface::SORT_ORDER] = 'ASC';
        $sortValues[DatagridInterface::SORT_BY] = 'vehicle';
    }

    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        parent::configureRoutes($collection);
        $collection
            ->add('downloadRouteImage', $this->getRouterIdParameter().'/imagen-ruta')
            ;
        $collection
            ->add('downloadPdfPendingSpecialPermits', 'download-pdf-pending-special-permits')
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
                            'help_html' => true,
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
                    'field_type' => EntityType::class,
                    'field_options' => [
                            'class' => Vehicle::class,
                            'query_builder' => $this->rm->getVehicleRepository()->getFilteredByEnterpriseEnabledSortedByNameQB($this->getUserLogedEnterprise()),
                        ],
                ]
            )
            ->add(
                'expeditionDate',
                DateRangeFilter::class,
                [
                    'label' => 'admin.label.expedition_date',
                    'field_type' => DateRangePickerType::class,
                    'field_options' => [
                        'field_options_start' => [
                            'label' => 'Desde',
                            'format' => 'dd/MM/yyyy',
                        ],
                        'field_options_end' => [
                            'label' => 'Hasta',
                            'format' => 'dd/MM/yyyy',
                        ],
                    ],
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
