<?php

namespace App\Admin\Vehicle;

use App\Admin\AbstractBaseAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

/**
 * Class VehicleMaintenanceTaskAdmin.
 *
 * @category    Admin
 *
 * @auhtor      Jordi Sort <jordi.sort@mirmit.com>
 */
class VehicleMaintenanceTaskAdmin extends AbstractBaseAdmin
{
    /**
     * @var string
     */
    protected $classnameLabel = 'Procesos de taller';

    /**
     * @var string
     */
    protected $baseRoutePattern = 'vehiculos/procesos-de-taller';

    /**
     * @var array
     */
    protected $datagridValues = [
        '_sort_by' => 'id',
        '_sort_order' => 'ASC',
    ];

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('General', $this->getFormMdSuccessBoxArray(4))
            ->add(
                'name',
                null,
                [
                    'label' => 'admin.label.name',
                    'required' => true,
                ]
            )
            ->add(
                'hours',
                null,
                [
                    'label' => 'admin.label.hours',
                    'required' => false,
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
                'name',
                null,
                [
                    'label' => 'admin.label.name',
                ]
            )
            ->add(
                'hours',
                null,
                [
                    'label' => 'admin.label.hours',
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
                'enabled',
                null,
                [
                    'label' => 'admin.label.enabled_male',
                ]
            )
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add(
                'name',
                null,
                [
                    'label' => 'admin.label.name',
                ]
            )
            ->add(
                'hours',
                null,
                [
                    'label' => 'admin.label.hours',
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
}
