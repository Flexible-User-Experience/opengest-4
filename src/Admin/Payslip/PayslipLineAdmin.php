<?php

namespace App\Admin\Payslip;

use App\Admin\AbstractBaseAdmin;
use App\Entity\Payslip\Payslip;
use App\Entity\Payslip\PayslipLineConcept;
use Exception;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\TemplateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

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
    protected $classnameLabel = 'PayslipLineAdmin';

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
    public function generateBaseRoutePattern(bool $isChildAdmin = false): string
    {
        return 'nominas/lineas';
    }

    /**
     * @throws Exception
     */
    protected function configureFormFields(FormMapper $formMapper): void
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
                'payslipLineConcept.isDeduction',
                TemplateType::class,
                [
                    'label' => 'isDeduction',
                    'disabled' => true,
                    'template' => 'admin/cells/form__cell_payslip_line_concept_is_deduction.html.twig',
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

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
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

    protected function configureListFields(ListMapper $listMapper): void
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
