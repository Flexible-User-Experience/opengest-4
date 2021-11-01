<?php

namespace App\Admin\Vehicle;

use App\Admin\AbstractBaseAdmin;
use App\Entity\Vehicle\Vehicle;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\Form\Type\DatePickerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

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
     * @var string
     */
    protected $baseRoutePattern = 'vehiculos/consumos-combustible';

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
    public function getExportFields(): array
    {
        return ['supplyDate', 'vehicle.description'];
    }

    /**
     * Configure route collection.
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);
        $collection
            ->add('uploadCsv', 'upload-csv')
            ;
    }

    protected function configureFormFields(FormMapper $formMapper)
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
                null,
                [
                    'label' => 'Hora suministro',
                    'required' => false,
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
                'fuelType',
                null,
                [
                    'label' => 'Tipo Combustible',
                    'required' => false,
                ]
            )
            ->end()
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
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
                'amount',
                null,
                [
                    'label' => 'Importe',
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
                'fuelType',
                null,
                [
                    'label' => 'Tipo Combustible',
                ]
            )
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
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
                    'format' => 'h:i',
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
                'amount',
                null,
                [
                    'label' => 'Importe',
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
                'fuelType',
                null,
                [
                    'label' => 'Tipo Combustible',
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
