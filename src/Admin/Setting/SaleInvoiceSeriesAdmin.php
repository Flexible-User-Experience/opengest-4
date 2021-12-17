<?php

namespace App\Admin\Setting;

use App\Admin\AbstractBaseAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

/**
 * Class SaleInvoiceSeriesAdmin.
 *
 * @category    Admin
 *
 * @auhtor      Rubèn Hierro <info@rubenhierro.com>
 */
class SaleInvoiceSeriesAdmin extends AbstractBaseAdmin
{
    /**
     * @var string
     */
    protected $classnameLabel = 'Sèries factura';

    /**
     * @var string
     */
    protected $baseRoutePattern = 'configuracio/series-factura';

    /**
     * @var array
     */
    protected $datagridValues = [
        '_sort_by' => 'name',
        '_sort_order' => 'ASC',
    ];

    /**
     * Methods.
     */
    protected function configureFormFields(FormMapper $formMapper): void
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

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
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

    protected function configureListFields(ListMapper $listMapper): void
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
