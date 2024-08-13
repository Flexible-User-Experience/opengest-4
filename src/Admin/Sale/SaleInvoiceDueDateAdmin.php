<?php

namespace App\Admin\Sale;

use App\Admin\AbstractBaseAdmin;
use App\Entity\Enterprise\EnterpriseTransferAccount;
use App\Entity\Sale\SaleInvoice;
use Sonata\AdminBundle\Datagrid\DatagridInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\Operator\EqualOperatorType;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\DoctrineORMAdminBundle\Filter\DateRangeFilter;
use Sonata\Form\Type\DatePickerType;
use Sonata\Form\Type\DateRangePickerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

/**
 * Class SaleInvoiceDueDateAdmin.
 *
 * @category    Admin
 *
 * @auhtor      Jordi Sort <jordi.sort@mirmit.com>
 */
class SaleInvoiceDueDateAdmin extends AbstractBaseAdmin
{
    /**
     * @var string
     */
    protected $classnameLabel = 'Vencimientos de factura';

    /**
     * Methods.
     */
    public function generateBaseRoutePattern(bool $isChildAdmin = false): string
    {
        return 'ventas/vencimientos-factura';
    }

    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection
            ->remove('delete')
        ;
    }

    protected function configureDefaultSortValues(array &$sortValues): void
    {
        $sortValues[DatagridInterface::SORT_ORDER] = 'DESC';
        $sortValues[DatagridInterface::SORT_BY] = 'date';
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        if ($this->getCode() === $this->getRootCode()) {
            $formMapper
                ->with('Vencimento', $this->getFormMdSuccessBoxArray(3))
                ->add(
                    'saleInvoice',
                    EntityType::class,
                    [
                        'class' => SaleInvoice::class,
                        'label' => 'admin.label.sale_invoice',
                        'required' => true,
                        'disabled' => true,
                        'query_builder' => $this->rm->getSaleInvoiceRepository()->getFilteredByEnterpriseSortedByDateQB($this->getUserLogedEnterprise()),
                    ]
                )
                ->add(
                    'date',
                    DatePickerType::class,

                    [
                        'label' => 'admin.label.date',
                        'format' => 'd/M/y',
                        'required' => true,
                        'disabled' => true,
                    ]
                )
                ->add(
                    'amount',
                    null,
                    [
                        'label' => 'admin.label.amount',
                        'scale' => 2,
                        'required' => true,
                        'disabled' => true,
                    ]
                )
                ->end()
                ->with('Datos de cobro', $this->getFormMdSuccessBoxArray(3))
                ->add(
                    'paid',
                    null,
                    [
                        'label' => 'admin.label.paid',
                    ]
                )
                ->add(
                    'paymentDate',
                    DatePickerType::class,
                    [
                        'label' => 'admin.label.collection_date',
                        'format' => 'd/M/y',
                        'required' => false,
                    ]
                )
                ->add(
                    'enterpriseTransferAccount',
                    EntityType::class,
                    [
                        'label' => 'admin.label.transference_bank',
                        'class' => EnterpriseTransferAccount::class,
                        'required' => false,
                    ]
                )
                ->end()
            ;
        } else {
            $formMapper
                ->with('Vencimiento', $this->getFormMdSuccessBoxArray(3))
                ->add(
                    'saleInvoice',
                    EntityType::class,
                    [
                        'class' => SaleInvoice::class,
                        'label' => 'admin.label.sale_invoice',
                        'required' => true,
                        'query_builder' => $this->rm->getSaleInvoiceRepository()->getRecentFilteredByEnterpriseSortedByDateQB($this->getUserLogedEnterprise()),
                        'attr' => [
                            'hidden' => 'true',
                        ],
                    ]
                )
                ->add(
                    'date',
                    DatePickerType::class,
                    [
                        'label' => 'admin.label.date',
                        'format' => 'dd/MM/yyyy',
                        'required' => true,
                        'dp_default_date' => (new \DateTime())->format('d/m/Y'),
                    ]
                )
                ->add(
                    'amount',
                    null,
                    [
                        'label' => 'admin.label.amount',
                        'required' => true,
                    ]
                )
                ->end();
        }
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add(
                'saleInvoice',
                null,
                [
                    'label' => 'admin.label.sale_invoice',
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
                'date',
                DateRangeFilter::class,
                [
                    'label' => 'admin.label.date',
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
                'paymentDate',
                DateRangeFilter::class,
                [
                    'label' => 'admin.label.collection_date',
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
                'paid',
                null,
                [
                    'label' => 'admin.label.paid',
                    'show' => true,
                ]
            )
            ->add(
                'enterpriseTransferAccount',
                null,
                [
                    'label' => 'admin.label.transference_bank',
                ]
            )
        ;
    }

    protected function configureDefaultFilterValues(array &$filterValues): void
    {
        $filterValues['paid'] = [
            'type' => EqualOperatorType::TYPE_EQUAL,
            'value' => false,
        ];
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add(
                'saleInvoice',
                null,
                [
                    'label' => 'admin.label.sale_invoice',
                ]
            )
            ->add(
                'date',
                null,
                [
                    'label' => 'admin.label.date',
                    'format' => 'd/m/Y',
                ]
            )
            ->add(
                'amount',
                null,
                [
                    'label' => 'admin.label.amount',
                    'template' => 'admin/cells/list__cell_amount_currency_number.html.twig',
                ]
            )
            ->add(
                'paid',
                null,
                [
                    'label' => 'admin.label.paid',
                    'editable' => true,
                ]
            )
            ->add(
                'paymentDate',
                null,
                [
                    'label' => 'admin.label.collection_date',
                    'format' => 'd/m/Y',
                ]
            )
            ->addIdentifier(
                'enterpriseTransferAccount',
                null,
                [
                    'label' => 'admin.label.transference_bank',
                    'sortable' => true,
                ]
            )
            ->add(
                '_action',
                'actions',
                [
                    'actions' => [
                        'edit' => ['template' => 'admin/buttons/list__action_edit_button.html.twig'],
                    ],
                    'label' => 'admin.actions',
                ]
            )
        ;
    }
}
