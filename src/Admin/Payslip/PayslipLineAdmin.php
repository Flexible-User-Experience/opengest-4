<?php

namespace App\Admin\Payslip;

use App\Admin\AbstractBaseAdmin;
use App\Entity\Payslip\Payslip;
use App\Entity\Payslip\PayslipLineConcept;
use Exception;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

/**
 * Class PayslipOperatorDefaultLineAdmin.
 *
 * @category    Admin
 */
class PayslipLineAdmin extends AbstractBaseAdmin
{
    /**
     * @var string
     */
    protected $translationDomain = 'admin';

    /**
     * @var string
     */
    protected $classnameLabel = 'PayslipLineAdmin';

    /**
     * @var string
     */
    protected $baseRoutePattern = 'nominas/lineas';

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
                'payslip',
                EntityType::class,
                [
                    'class' => Payslip::class,
                    'label' => 'admin.label.payslip',
                    'attr' => [
                        'hidden' => 'true',
                    ],
                ]
            )
            ->add(
                'payslipLineConcept',
                EntityType::class,
                [
                    'class' => PayslipLineConcept::class,
                    'label' => 'admin.label.payslip_line_concept',
                    'required' => true,
                    'query_builder' => $this->rm->getPayslipLineConceptRepository()->getPayslipLineConceptsEnabledSortedByNameQB(),
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
                'payslip',
                null,
                [
                    'label' => 'admin.label.payslip',
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
                'payslip',
                null,
                [
                    'label' => 'admin.label.payslip',
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
