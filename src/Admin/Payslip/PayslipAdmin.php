<?php

namespace App\Admin\Payslip;

use App\Admin\AbstractBaseAdmin;
use App\Entity\Operator\Operator;
use App\Entity\Payslip\Payslip;
use App\Entity\Payslip\PayslipLine;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use Sonata\AdminBundle\Datagrid\DatagridInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\DoctrineORMAdminBundle\Filter\DateRangeFilter;
use Sonata\DoctrineORMAdminBundle\Filter\ModelFilter;
use Sonata\Form\Type\CollectionType;
use Sonata\Form\Type\DatePickerType;
use Sonata\Form\Type\DateRangePickerType;
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
    protected $classnameLabel = 'Payslip';

    /**
     * @var string
     */
    protected $baseRoutePattern = 'nominas/nominas';

    /**
     * Methods.
     */
    protected function configureDefaultSortValues(array &$sortValues): void
    {
        $sortValues[DatagridInterface::SORT_ORDER] = 'DESC';
        $sortValues[DatagridInterface::SORT_BY] = 'toDate';
    }

    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        parent::configureRoutes($collection);
        $collection
//            ->add('pdf', $this->getRouterIdParameter().'/pdf')
            ->add('batch')
            ->add('generatePaymentDocuments', 'generate-payment-documents')
            ->remove('create')
        ;
    }

    /**
     * @param array $actions
     */
    public function configureBatchActions($actions): array
    {
        $newActions['selectOption'] = [
            'label' => 'admin.action.select_option',
            'ask_confirmation' => false,
        ];
        if ($this->hasRoute('edit') && $this->hasAccess('edit')) {
            $newActions['generatePayslip'] = [
                'label' => 'admin.action.generate_payslip',
                'ask_confirmation' => false,
            ];
            $newActions['generatePaymentDocuments'] = [
                'label' => 'admin.action.generate_payslip_documents',
                'ask_confirmation' => false,
            ];
        }

        return array_merge($newActions, $actions);
    }

    public function configureExportFields(): array
    {
        $asd = 1;

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
                    'label' => 'admin.label.payslip_total',
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
                ModelFilter::class,
                [
                    'label' => 'admin.label.operator',
                    'field_type' => ModelAutocompleteType::class,
                    'field_options' => [
                        'property' => ['name', 'surname1', 'surname2'],
                    ],
                ]
            )
            ->add(
                'fromDate',
                DateRangeFilter::class,
                [
                    'label' => '1r día nómina',
                    'field_type' => DateRangePickerType::class,
                    'field_options' => [
                        'field_options_start' => [
                            'label' => 'Desde',
                            'format' => 'dd/MM/yyyy',
                        ],
                        'field_options_end' => [
                            'label' => 'Hasta',
                            'format' => 'dd/MM/yyyy',
                        ],
                    ],
                ]
            )
            ->add(
                'toDate',
                DateRangeFilter::class,
                [
                    'label' => 'Último día nómina',
                    'field_type' => DateRangePickerType::class,
                    'field_options' => [
                        'field_options_start' => [
                            'label' => 'Desde',
                            'format' => 'dd/MM/yyyy',
                        ],
                        'field_options_end' => [
                            'label' => 'Hasta',
                            'format' => 'dd/MM/yyyy',
                        ],
                    ],
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
                    'label' => 'admin.label.payslip_total',
                ]
            )
        ;
    }

    protected function configureQuery(ProxyQueryInterface $query): ProxyQueryInterface
    {
        $rootAlias = current($query->getRootAliases());
        $query
            ->addOrderBy($rootAlias.'.operator', 'ASC')
        ;

        return $query;
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
                    'label' => 'admin.label.payslip_total',
                ]
            )
            ->add(
                '_action',
                'actions',
                [
                    'actions' => [
                        'edit' => ['template' => 'admin/buttons/list__action_edit_button.html.twig'],
                        'delete' => ['template' => 'admin/buttons/list__action_delete_button.html.twig'],
//                        'pdf' => ['template' => 'admin/buttons/list__action_pdf_delivery_note_button.html.twig'],
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
    public function preUpdate($object): void
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
