<?php

namespace App\Admin\Payslip;

use App\Admin\AbstractBaseAdmin;
use App\Entity\Operator\Operator;
use App\Entity\Payslip\Payslip;
use App\Entity\Payslip\PayslipLine;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\DoctrineORMAdminBundle\Filter\DateFilter;
use Sonata\DoctrineORMAdminBundle\Filter\ModelAutocompleteFilter;
use Sonata\Form\Type\CollectionType;
use Sonata\Form\Type\DatePickerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

/**
 * Class PayslipAdmin.
 *
 * @category    Admin
 */
class PayslipAdmin extends AbstractBaseAdmin
{
    /**
     * @var string
     */
    protected $translationDomain = 'admin';

    /**
     * @var string
     */
    protected $classnameLabel = 'Payslip';

    /**
     * @var string
     */
    protected $baseRoutePattern = 'nominas/nominas';

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
    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        parent::configureRoutes($collection);
        $collection
            ->add('pdf', $this->getRouterIdParameter().'/pdf')
            ->remove('create')
        ;
    }

    public function configureExportFields(): array
    {
        return [
            'id',
            'operator',
            'fromDateFormatted',
            'toDateFormatted',
            'year',
            'expenses',
            'socialSecurityCost',
            'otherCosts',
            'totalAmount',
        ];
    }

    /**
     * @throws Exception
     */
    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->with('admin.with.general', $this->getFormMdSuccessBoxArray(4))
        ;
        if ($this->getRootCode() === $this->getCode()) { //show only if is not embedded in another admin
            $formMapper
                ->add(
                    'operator',
                    EntityType::class,
                    [
                        'class' => Operator::class,
                        'label' => 'admin.label.operator',
                    ]
                );
        }
        $formMapper
            ->add(
                'fromDate',
                DatePickerType::class,
                [
                    'label' => 'admin.label.from',
                    'format' => 'dd/MM/yyyy',
                    'required' => true,
                    'dp_default_date' => (new \DateTime())->format('d/m/Y'),
                ]
            )
            ->add(
                'toDate',
                DatePickerType::class,
                [
                    'label' => 'admin.label.to',
                    'format' => 'dd/MM/yyyy',
                    'required' => true,
                    'dp_default_date' => (new \DateTime())->format('d/m/Y'),
                ]
            )
            ->add(
                'year',
                NumberType::class,
                [
                    'label' => 'admin.label.year',
                    'required' => false,
                ]
            )
            ->end()
            ->with('admin.label.amount', $this->getFormMdSuccessBoxArray(4))
            ->add(
                'expenses',
                NumberType::class,
                [
                    'label' => 'admin.label.expenses',
                    'required' => false,
                ]
            )
            ->add(
                'socialSecurityCost',
                NumberType::class,
                [
                    'label' => 'admin.label.social_security_cost',
                    'required' => false,
                ]
            )
            ->add(
                'extraPay',
                NumberType::class,
                [
                    'label' => 'admin.label.extra_pay',
                    'required' => false,
                    'disabled' => true,
                ]
            )
            ->add(
                'otherCosts',
                NumberType::class,
                [
                    'label' => 'admin.label.other_costs',
                    'required' => false,
                ]
            )
            ->add(
                'totalAmount',
                NumberType::class,
                [
                    'label' => 'admin.label.total',
                    'required' => false,
                ]
            )
            ->end()
            ;
        if ($this->getRootCode() === $this->getCode()) { //show only if is not embedded in another admin
            $formMapper
            ->with('admin.label.lines', $this->getFormMdSuccessBoxArray(12))
                ->add(
                    'payslipLines',
                    CollectionType::class,
                    [
                        'required' => false,
                        'error_bubbling' => true,
                        'label' => false,
                        'type_options' => [
//                            'delete' => false,
                        ],
                    ],
                    [
                        'edit' => 'inline',
                        'inline' => 'table',
                    ]
                )
                ->end()
            ->end();
        }
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
                'operator',
                ModelAutocompleteFilter::class,
                [
                    'label' => 'admin.label.operator',
                ],
                null,
                [
                    'property' => 'name',
                ]
            )
            ->add(
                'fromDate',
                DateFilter::class,
                [
                    'label' => 'admin.label.from',
                    'field_type' => DatePickerType::class,
                    'format' => 'd/m/Y',
                ],
                null,
                [
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy',
                ]
            )
            ->add(
                'toDate',
                DateFilter::class,
                [
                    'label' => 'admin.label.to',
                    'field_type' => DatePickerType::class,
                    'format' => 'd/m/Y',
                ],
                null,
                [
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy',
                ]
            )
            ->add(
                'year',
                null,
                [
                    'label' => 'admin.label.year',
                ]
            )
            ->add(
                'expenses',
                null,
                [
                    'label' => 'admin.label.expenses',
                ]
            )
            ->add(
                'socialSecurityCost',
                null,
                [
                    'label' => 'admin.label.social_security_cost',
                ]
            )
            ->add(
                'extraPay',
                null,
                [
                    'label' => 'admin.label.extra_pay',
                ]
            )
            ->add(
                'otherCosts',
                null,
                [
                    'label' => 'admin.label.other_costs',
                ]
            )
            ->add(
                'totalAmount',
                null,
                [
                    'label' => 'admin.label.total',
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
                'operator',
                null,
                [
                    'label' => 'admin.label.operator',
                ]
            )
            ->add(
                'fromDate',
                null,
                [
                    'label' => 'admin.label.from',
                    'format' => 'd/m/y',
                ]
            )
            ->add(
                'toDate',
                null,
                [
                    'label' => 'admin.label.to',
                    'format' => 'd/m/y',
                ]
            )
            ->add(
                'year',
                null,
                [
                    'label' => 'admin.label.year',
                ]
            )
            ->add(
                'expenses',
                null,
                [
                    'label' => 'admin.label.expenses',
                ]
            )
            ->add(
                'socialSecurityCost',
                null,
                [
                    'label' => 'admin.label.social_security_cost',
                ]
            )
            ->add(
                'extraPay',
                null,
                [
                    'label' => 'admin.label.extra_pay',
                ]
            )
            ->add(
                'otherCosts',
                null,
                [
                    'label' => 'admin.label.other_costs',
                ]
            )
            ->add(
                'totalAmount',
                null,
                [
                    'label' => 'admin.label.total',
                ]
            )
            ->add(
                '_action',
                'actions',
                [
                    'actions' => [
                        'edit' => ['template' => 'admin/buttons/list__action_edit_button.html.twig'],
                        'delete' => ['template' => 'admin/buttons/list__action_delete_button.html.twig'],
                        'pdf' => ['template' => 'admin/buttons/list__action_pdf_delivery_note_button.html.twig'],
                    ],
                    'label' => 'admin.actions',
                ]
            )
        ;
    }

    /**
     * @param Payslip $object
     *
     * @throws NonUniqueResultException
     */
    public function preUpdate($object)
    {
        $payslipLines = $object->getPayslipLines();
        $totalAmount = 0;
        /** @var PayslipLine $payslipLine */
        foreach ($payslipLines as $payslipLine) {
            $amount = $payslipLine->getUnits() * $payslipLine->getPriceUnit();
            $payslipLine->setAmount($amount);
            $totalAmount += $amount;
        }
        $object->setTotalAmount($totalAmount);
        $this->em->flush();
    }
}
