<?php

namespace App\Admin\Payslip;

use App\Admin\AbstractBaseAdmin;
use Exception;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

/**
 * Class PayslipOperatorDefaultLineAdmin.
 *
 * @category    Admin
 */
class PayslipOperatorDefaultLineAdmin extends AbstractBaseAdmin
{
    /**
     * @var string
     */
    protected $translationDomain = 'admin';

    /**
     * @var string
     */
    protected $classnameLabel = 'PayslipOperatorDefaultLine';

    /**
     * @var string
     */
    protected $baseRoutePattern = 'nominas/conceptos_linea_operario_por_defecto';

    /**
     * @var array
     */
    protected $datagridValues = [
        '_sort_by' => 'id',
        '_sort_order' => 'ASC',
    ];

    /**
     * Methods.
     */

    /**
     * @throws Exception
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('admin.with.general', $this->getFormMdSuccessBoxArray(4))
            ->add(
                'operator',
                null,
                [
                    'label' => 'admin.label.operator',
                    'attr' => [
                        'hidden' => 'true',
                    ],
                ]
            )
            ->add(
                'payslipLineConcept',
                null,
                [
                    'label' => 'admin.label.payslip_line_concept',
                ]
            )
            ->add(
                'units',
                null,
                [
                    'label' => 'admin.label.units',
                ]
            )
            ->add(
                'priceUnit',
                null,
                [
                    'label' => 'admin.label.price_unit',
                ]
            )
            ->add(
                'amount',
                null,
                [
                    'label' => 'admin.label.amount',
                    'disabled' => true,
                ]
            )
            ->end()
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add(
                'id',
                null,
                [
                    'label' => 'Id',
                ]
            )
            ->add(
                'operator',
                null,
                [
                    'label' => 'admin.label.operator',
                ]
            )
            ->add(
                'payslipLineConcept',
                null,
                [
                    'label' => 'admin.label.payslip_line_concept',
                ]
            )
            ->add(
                'units',
                null,
                [
                    'label' => 'admin.label.units',
                ]
            )
            ->add(
                'priceUnit',
                null,
                [
                    'label' => 'admin.label.price_unit',
                ]
            )
            ->add(
                'amount',
                null,
                [
                    'label' => 'admin.label.amount',
                ]
            )
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add(
                'id',
                null,
                [
                    'label' => 'Id',
                ]
            )
            ->add(
                'operator',
                null,
                [
                    'label' => 'admin.label.operator',
                ]
            )
            ->add(
                'payslipLineConcept',
                null,
                [
                    'label' => 'admin.label.payslip_line_concept',
                ]
            )
            ->add(
                'units',
                null,
                [
                    'label' => 'admin.label.units',
                ]
            )
            ->add(
                'priceUnit',
                null,
                [
                    'label' => 'admin.label.price_unit',
                ]
            )
            ->add(
                'amount',
                null,
                [
                    'label' => 'admin.label.amount',
                ]
            )
            ->add(
                '_action',
                'actions',
                [
                    'actions' => [
                        'edit' => ['template' => 'admin/buttons/list__action_edit_button.html.twig'],
                        'delete' => ['template' => 'admin/buttons/list__action_delete_button.html.twig'],
                    ],
                    'label' => 'admin.actions',
                ]
            )
        ;
    }
}
