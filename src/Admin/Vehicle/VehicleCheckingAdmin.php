<?php

namespace App\Admin\Vehicle;

use App\Admin\AbstractBaseAdmin;
use App\Entity\Vehicle\Vehicle;
use App\Enum\UserRolesEnum;
use Sonata\AdminBundle\Datagrid\DatagridInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\DoctrineORMAdminBundle\Filter\DateRangeFilter;
use Sonata\Form\Type\DatePickerType;
use Sonata\Form\Type\DateRangePickerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

/**
 * Class VehicleCheckingAdmin.
 *
 * @category Admin
 *
 * @author   Wils Iglesias <wiglesias83@gmail.com>
 */
class VehicleCheckingAdmin extends AbstractBaseAdmin
{
    /**
     * @var string
     */
    protected $classnameLabel = 'Revisiones de vehículo';

    /**
     * Methods.
     */
    public function generateBaseRoutePattern(bool $isChildAdmin = false): string
    {
        return 'vehicles/revisio';
    }

    /**
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        parent::configureRoutes($collection);
        $collection
//            ->remove('delete')
            ->add('downloadPdfPendingCheckings', 'download-pdf-pending-checkings')
            ->add('batch')
        ;
    }

    protected function configureDefaultSortValues(array &$sortValues): void
    {
        $sortValues[DatagridInterface::SORT_ORDER] = 'ASC';
        $sortValues[DatagridInterface::SORT_BY] = 'end';
    }

    public function configureBatchActions(array $actions): array
    {
        unset($actions['delete']);
        $actions['downloadPdfVehiclePendingCheckings'] = [
            'ask_confirmation' => false,
            'label' => 'Informe revisiones',
        ];

        return $actions;
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->with('General', $this->getFormMdSuccessBoxArray(6))
            ;
        if ($this->getCode() === $this->getRootCode()) {
            $formMapper
                ->add(
                    'vehicle',
                    EntityType::class,
                    [
                        'label' => 'Vehicle',
                        'required' => true,
                        'class' => Vehicle::class,
                        'choice_label' => 'name',
                        'query_builder' => $this->rm->getVehicleRepository()->getFilteredByEnterpriseEnabledSortedByNameQB($this->getUserLogedEnterprise()),
                    ]
                );
        }
        $formMapper
            ->add(
                'type',
                null,
                [
                    'label' => 'admin.label.checking_type',
                    'required' => true,
                    'query_builder' => $this->rm->getVehicleCheckingTypeRepository()->getEnabledSortedByNameQB(),
                ]
            )
            ->add(
                'begin',
                DatePickerType::class,
                [
                    'label' => 'admin.label.expedition_date',
                    'format' => 'd/M/y',
                    'required' => true,
                ]
            )
            ->add(
                'end',
                DatePickerType::class,
                [
                    'label' => 'admin.label.expiry_date',
                    'format' => 'd/M/y',
                    'required' => true,
                ]
            )
            ->end()
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add(
                'vehicle',
                null,
                [
                    'label' => 'admin.label.vehicle',
                ]
            )
            ->add(
                'type',
                null,
                [
                    'label' => 'admin.label.checking_type',
                ]
            )
            ->add(
                'begin',
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
                'end',
                DateRangeFilter::class,
                [
                    'label' => 'admin.label.expiry_date',

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
        ;
    }

    public function configureQuery(ProxyQueryInterface $query): ProxyQueryInterface
    {
        $queryBuilder = parent::configureQuery($query);
        $queryBuilder
            ->join($queryBuilder->getRootAliases()[0].'.vehicle', 'v')
            ->andWhere('v.enabled = :enabled')
            ->setParameter('enabled', true)
        ;
        if (!$this->acs->isGranted(UserRolesEnum::ROLE_ADMIN)) {
            $queryBuilder
                ->andWhere('v.enterprise = :enterprise')
                ->setParameter('enterprise', $this->ts->getToken()->getUser()->getDefaultEnterprise())
            ;
        }

        return $queryBuilder;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add(
                'status',
                null,
                [
                    'label' => 'admin.label.status',
                    'template' => 'admin/cells/list__cell_vehicle_checking_status.html.twig',
                ]
            )
            ->add(
                'begin',
                'date',
                [
                    'label' => 'admin.label.expedition_date',
                    'format' => 'd/m/Y',
                    'editable' => true,
                ]
            )
            ->add(
                'end',
                'date',
                [
                    'label' => 'admin.label.expiry_date',
                    'format' => 'd/m/Y',
                    'editable' => true,
                ]
            )
            ->add(
                'vehicle',
                null,
                [
                    'label' => 'admin.label.vehicle',
                    'editable' => false,
                    'associated_property' => 'name',
                    'sortable' => true,
                    'sort_field_mapping' => ['fieldName' => 'name'],
                    'sort_parent_association_mappings' => [['fieldName' => 'vehicle']],
                ]
            )
            ->add(
                'type',
                null,
                [
                    'label' => 'admin.label.checking_type',
                    'editable' => true,
                    'associated_property' => 'name',
                    'sortable' => true,
                    'sort_field_mapping' => ['fieldName' => 'name'],
                    'sort_parent_association_mappings' => [['fieldName' => 'type']],
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
            )
        ;
    }
}
