<?php

namespace App\Admin\Vehicle;

use App\Admin\AbstractBaseAdmin;
use App\Entity\Vehicle\Vehicle;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\Form\Type\DatePickerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;

/**
 * Class VehicleConsumptionAdmin.
 *
 * @category    Admin
 *
 * @auhtor      Jordi Sort <jordi.sort@mirmit.com>
 */
class VehicleConsumptionAdmin extends AbstractBaseAdmin
{
    /**
     * @var string
     */
    protected $classnameLabel = 'Consumos de combustible';

    /**
     * @var array
     */
    protected $datagridValues = [
        '_sort_by' => 'supplyDate',
        '_sort_order' => 'DESC',
    ];

    /**
     * Methods.
     */
    public function generateBaseRoutePattern(bool $isChildAdmin = false): string
    {
        return 'vehiculos/consumos-combustible';
    }

    public function configureExportFields(): array
    {
        return [
            'supplyDateFormatted',
            'vehicle.description',
            'vehicle.vehicleRegistrationNumber',
            'amount',
            'quantity',
            'priceUnit',
            'fuelType',
        ];
    }

    /**
     * Configure route collection.
     */
    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection
            ->add('uploadCsv', 'upload-csv')
            ->remove('show')
        ;
    }

    public function configureBatchActions(array $actions): array
    {
        return $actions;
    }

    public function configureActionButtons(array $buttonList, string $action, ?object $object = null): array
    {
        $buttonList['uploadCsv'] = [
            'template' => 'admin/buttons/import_csv_vehicle_consumption.html.twig',
        ];

        return $buttonList;
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->with('General', $this->getFormMdSuccessBoxArray(4))
            ->add(
                'supplyDate',
                DatePickerType::class,
                [
                    'label' => 'Fecha suministro',
                    'format' => 'dd/MM/yyyy',
                    'required' => true,
                    'dp_default_date' => (new \DateTime())->format('d/m/Y'),
                ]
            )
            ->add(
                'supplyTime',
                TimeType::class,
                [
                    'label' => 'Hora suministro',
                    'required' => false,
                    'widget' => 'single_text',
                ]
            )
            ->add(
                'supplyCode',
                null,
                [
                    'label' => 'Código suministro',
                    'required' => false,
                ]
            )
            ->add(
                'vehicle',
                EntityType::class,
                [
                    'class' => Vehicle::class,
                    'label' => 'Vehículo',
                    'required' => true,
                    'query_builder' => $this->rm->getVehicleRepository()->getFilteredByEnterpriseEnabledSortedByNameQB($this->getUserLogedEnterprise()),
                ]
            )
            ->add(
                'amount',
                null,
                [
                    'label' => 'Importe',
                    'required' => true,
                ]
            )
            ->add(
                'quantity',
                null,
                [
                    'label' => 'Cantidad',
                    'required' => false,
                ]
            )
            ->add(
                'priceUnit',
                null,
                [
                    'label' => '€/l',
                    'required' => false,
                ]
            )
            ->add(
                'vehicleFuel',
                null,
                [
                    'label' => 'Combustible',
                    'required' => false,
                ]
            )
            ->end()
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add(
                'supplyDate',
                null,
                [
                    'label' => 'Fecha suministro',
                ]
            )
            ->add(
                'supplyTime',
                null,
                [
                    'label' => 'Hora suministro',
                ]
            )
            ->add(
                'supplyCode',
                null,
                [
                    'label' => 'Código suministro',
                ]
            )
            ->add(
                'vehicle',
                null,
                [
                    'label' => 'Vehículo',
                ]
            )
            ->add(
                'quantity',
                null,
                [
                    'label' => 'Cantidad',
                ]
            )
            ->add(
                'priceUnit',
                null,
                [
                    'label' => '€/l',
                ]
            )
            ->add(
                'amount',
                null,
                [
                    'label' => 'Importe',
                ]
            )
            ->add(
                'vehicleFuel',
                null,
                [
                    'label' => 'Tipo Combustible',
                ]
            )
        ;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add(
                'supplyDate',
                null,
                [
                    'label' => 'Fecha suministro',
                    'format' => 'd/m/Y',
                ]
            )
            ->add(
                'supplyTime',
                null,
                [
                    'label' => 'Hora suministro',
                    'format' => 'H:i',
                ]
            )
            ->add(
                'supplyCode',
                null,
                [
                    'label' => 'Código suministro',
                ]
            )
            ->add(
                'vehicle',
                null,
                [
                    'label' => 'Vehículo',
                ]
            )
            ->add(
                'vehicleFuel',
                null,
                [
                    'label' => 'Combustible',
                ]
            )
            ->add(
                'quantity',
                null,
                [
                    'label' => 'Cantidad',
                ]
            )
            ->add(
                'priceUnit',
                null,
                [
                    'label' => '€/l',
                    'template' => 'admin/cells/list__cell_price_unit_currency_number.html.twig',
                ]
            )
            ->add(
                'amount',
                null,
                [
                    'label' => 'Importe',
                    'template' => 'admin/cells/list__cell_amount_currency_number.html.twig',
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
}
