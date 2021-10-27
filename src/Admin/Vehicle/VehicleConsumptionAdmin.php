<?php

namespace App\Admin\Vehicle;

use App\Admin\AbstractBaseAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

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
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('General', $this->getFormMdSuccessBoxArray(4))
            ->add(
                'name',
                null,
                [
                    'label' => 'Nom',
                    'required' => true,
                ]
            )
            ->add(
                'prefix',
                null,
                [
                    'label' => 'Prefix',
                    'required' => false,
                ]
            )
            ->add(
                'isDefault',
                null,
                [
                    'label' => 'Sèrie per defecte',
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
                'name',
                null,
                [
                    'label' => 'Nom',
                ]
            )
            ->add(
                'prefix',
                null,
                [
                    'label' => 'Prefix',
                ]
            )
            ->add(
                'isDefault',
                null,
                [
                    'label' => 'Sèrie per defecte',
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
                    'label' => 'Nom',
                    'editable' => true,
                ]
            )
            ->add(
                'prefix',
                null,
                [
                    'label' => 'Prefix',
                    'editable' => true,
                ]
            )
            ->add(
                'isDefault',
                null,
                [
                    'label' => 'Sèrie per defecte',
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
                        'delete' => ['template' => 'admin/buttons/list__action_delete_button.html.twig'],
                    ],
                    'label' => 'Accions',
                ]
            )
        ;
    }
}
