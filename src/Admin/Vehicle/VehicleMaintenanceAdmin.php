<?php

namespace App\Admin\Vehicle;

use App\Admin\AbstractBaseAdmin;
use App\Entity\Vehicle\Vehicle;
use App\Entity\Vehicle\VehicleMaintenance;
use App\Entity\Vehicle\VehicleMaintenanceTask;
use Doctrine\ORM\NonUniqueResultException;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\Operator\EqualOperatorType;
use Sonata\DoctrineORMAdminBundle\Filter\DateRangeFilter;
use Sonata\Form\Type\BooleanType;
use Sonata\Form\Type\DatePickerType;
use Sonata\Form\Type\DateRangePickerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

/**
 * Class VehicleMaintenanceAdmin.
 *
 * @category    Admin
 *
 * @auhtor      Jordi Sort <jordi.sort@mirmit.com>
 */
class VehicleMaintenanceAdmin extends AbstractBaseAdmin
{
    /**
     * @var string
     */
    protected $classnameLabel = 'Mantenimientos de vehículo';

    /**
     * @var string
     */
    protected $baseRoutePattern = 'vehiculos/mantenimientos';

    /**
     * @var string
     */
    protected $translationDomain = 'admin';

    /**
     * @var array
     */
    protected $datagridValues = [
        '_sort_by' => 'needsCheck',
        '_sort_order' => 'DESC',
    ];

    protected function configureFormFields(FormMapper $formMapper)
    {
        if ($this->getRootCode() == $this->getCode()) {
            $formMapper
                ->with('General', $this->getFormMdSuccessBoxArray(8))
                ->add(
                    'vehicle',
                    EntityType::class,
                    [
                        'label' => 'admin.label.vehicle',
                        'class' => Vehicle::class,
                        'placeholder' => '--- selecciona un vehículo ---',
                        'query_builder' => $this->rm->getVehicleRepository()->getFilteredByEnterpriseEnabledSortedByNameQB($this->getUserLogedEnterprise()),
                    ]
                )
                ->end();
        }
        $formMapper
            ->with('General', $this->getFormMdSuccessBoxArray(8))
            ->add(
                'vehicleMaintenanceTask',
                EntityType::class,
                [
                    'label' => 'admin.label.vehicle_maintenance_task',
                    'class' => VehicleMaintenanceTask::class,
                    'placeholder' => '--- selecciona un proceso ---',
                    'query_builder' => $this->rm->getVehicleMaintenanceTaskRepository()->getVehicleMaintenanceTasksSortedByNameQB(),
                ]
            )
            ->add(
                'date',
                DatePickerType::class,
                [
                    'label' => 'admin.label.date',
                    'format' => 'dd/MM/yyyy',
                    'dp_default_date' => (new \DateTime())->format('d/m/Y'),
                ]
            )
            ->add(
                'km',
                null,
                [
                    'label' => 'admin.label.km',
                    'required' => false,
                ]
            )
            ->add(
                'description',
                null,
                [
                    'label' => 'admin.label.description',
                    'required' => false,
                ]
            )
            ->add(
                'needsCheck',
                CheckboxType::class,
                [
                    'label' => 'admin.label.needs_check',
                    'required' => false,
                ]
            )
            ->add(
                'enabled',
                CheckboxType::class,
                [
                    'label' => 'admin.label.enabled_male',
                ]
            )
            ->end()
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
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
                'vehicleMaintenanceTask',
                null,
                [
                    'label' => 'admin.label.vehicle_maintenance_task',
                ]
            )
            ->add(
                'date',
                DateRangeFilter::class,
                [
                    'label' => 'admin.label.date',
                ],
                DateRangePickerType::class,
                [
                    'field_options_start' => [
                        'label' => 'Desde',
                        'format' => 'dd/MM/yyyy',
                    ],
                    'field_options_end' => [
                        'label' => 'Hasta',
                        'format' => 'dd/MM/yyyy',
                    ],
                ]
            )
            ->add(
                'km',
                null,
                [
                    'label' => 'admin.label.km',
                ]
            )
            ->add(
                'description',
                null,
                [
                    'label' => 'admin.label.description',
                ]
            )
            ->add(
                'needsCheck',
                null,
                [
                    'label' => 'admin.label.needs_check',
                ]
            )
            ->add(
                'enabled',
                null,
                [
                    'label' => 'admin.label.enabled_male',
                ]
            )
        ;
    }

    protected function configureDefaultFilterValues(array &$filterValues)
    {
        $filterValues['enabled'] = [
            'type' => EqualOperatorType::TYPE_EQUAL,
            'value' => BooleanType::TYPE_YES,
        ];
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add(
                'vehicle',
                null,
                [
                    'label' => 'admin.label.vehicle',
                ]
            )
            ->add(
                'vehicleMaintenanceTask',
                null,
                [
                    'label' => 'admin.label.vehicle_maintenance_task',
                ]
            )
            ->add(
                'date',
                null,
                [
                    'label' => 'admin.label.date',
                    'format' => 'd/m/Y',
                ]
            )
            ->add(
                'km',
                null,
                [
                    'label' => 'admin.label.km',
                ]
            )
            ->add(
                'description',
                null,
                [
                    'label' => 'admin.label.description',
                ]
            )
            ->add(
                'needsCheck',
                null,
                [
                    'label' => 'admin.label.needs_check',
                ]
            )
            ->add(
                'enabled',
                null,
                [
                    'label' => 'admin.label.enabled_male',
                ]
            )
            ->add(
                '_action',
                'actions',
                [
                    'actions' => [
                        'show' => ['template' => 'admin/buttons/list__action_show_button.html.twig'],
                        'edit' => ['template' => 'admin/buttons/list__action_edit_button.html.twig'],
                        'delete' => ['template' => 'admin/buttons/list__action_delete_button.html.twig'],
                    ],
                    'label' => 'Accions',
                ]
            )
        ;
    }

    /**
     * @param VehicleMaintenance $object
     *
     * @throws NonUniqueResultException
     */
    public function prePersist($object)
    {
        $this->disablePreviousAndCheckIfNeedMaintenance($object);
    }

    /**
     * @param VehicleMaintenance $object
     *
     * @throws NonUniqueResultException
     */
    public function preUpdate($object)
    {
        $this->disablePreviousAndCheckIfNeedMaintenance($object);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function disablePreviousAndCheckIfNeedMaintenance(VehicleMaintenance $object): void
    {
        $this->disablePreviousMaintenance($object);
        if ($this->vmm->checkIfMaintenanceNeedsCheck($object)) {
            $object->setNeedsCheck(true);
        } else {
            $object->setNeedsCheck(false);
        }
    }
}
