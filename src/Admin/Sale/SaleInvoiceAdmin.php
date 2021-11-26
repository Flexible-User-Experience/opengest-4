<?php

namespace App\Admin\Sale;

use App\Admin\AbstractBaseAdmin;
use App\Entity\Sale\SaleDeliveryNote;
use App\Entity\Sale\SaleInvoice;
use App\Entity\Setting\SaleInvoiceSeries;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Exception;
use Sonata\AdminBundle\Admin\AbstractAdmin as Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\AdminBundle\Form\Type\Operator\EqualOperatorType;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\DoctrineORMAdminBundle\Filter\DateRangeFilter;
use Sonata\DoctrineORMAdminBundle\Filter\ModelAutocompleteFilter;
use Sonata\Form\Type\BooleanType;
use Sonata\Form\Type\DatePickerType;
use Sonata\Form\Type\DateRangePickerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

/**
 * Class SaleInvoicedmin.
 *
 * @category    Admin
 */
class SaleInvoiceAdmin extends AbstractBaseAdmin
{
    /**
     * @var string
     */
    protected $translationDomain = 'admin';

    /**
     * @var string
     */
    protected $classnameLabel = 'Factura';

    /**
     * @var string
     */
    protected $baseRoutePattern = 'vendes/factura';

    /**
     * @var array
     */
    protected $datagridValues = [
        '_sort_by' => 'date',
        '_sort_order' => 'DESC',
    ];

    /**
     * Methods.
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);
        $collection
            ->add('pdf', $this->getRouterIdParameter().'/pdf')
            ->add('pdfWithBackground', $this->getRouterIdParameter().'/pdf-with-background')
            ->add('count', $this->getRouterIdParameter().'/to-count')
            ->add('setHasNotBeenCounted', $this->getRouterIdParameter().'/descontabilizar')
//            ->remove('delete')
            ->remove('create')
        ;
    }

    public function getExportFields(): array
    {
        return [
            'id',
            'series',
            'invoiceNumber',
            'dateFormatted',
            'hasBeenCounted',
            'partner.name',
            'partner.cifNif',
            'discount',
            'baseTotal',
            'iva',
            'irpf',
            'total',
        ];
    }

    /**
     * @throws Exception
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('admin.with.general', $this->getFormMdSuccessBoxArray(4))
            ->add(
                'invoiceNumber',
                null,
                [
                    'label' => 'admin.label.invoice_number_long',
                ]
            )
            ->add(
                'series',
                EntityType::class,
                [
                    'label' => 'admin.label.series',
                    'class' => SaleInvoiceSeries::class,
                    'query_builder' => $this->rm->getSaleInvoiceSeriesRepository()->getEnabledSortedByNameQB(),
                    'choice_label' => 'name',
                ]
            )
            ->add(
                'date',
                DatePickerType::class,
                [
                    'label' => 'admin.label.date',
                    'format' => 'd/M/y',
                    'required' => true,
                ]
            )
            ->add(
                'partner',
                ModelAutocompleteType::class,
                [
                    'property' => 'name',
                    'label' => 'admin.label.partner',
                    'callback' => function ($admin, $property, $value) {
                        /** @var Admin $admin */
                        $datagrid = $admin->getDatagrid();
                        /** @var QueryBuilder $queryBuilder */
                        $queryBuilder = $datagrid->getQuery();
                        $queryBuilder
                            ->andWhere($queryBuilder->getRootAliases()[0].'.enterprise = :enterprise')
                            ->setParameter('enterprise', $this->getUserLogedEnterprise())
                        ;
                        $datagrid->setValue($property, null, $value);
                    },
                ],
                [
                    'admin_code' => 'app.admin.partner',
                ]
            )
            ->add(
                'discount',
                null,
                [
                    'label' => 'admin.label.discount',
                    'required' => false,
                ]
            )
            ->add(
                'hasBeenCounted',
                null,
                [
                    'label' => 'admin.label.has_been_counted_long',
                    'required' => false,
                ]
            )
            ->end()
            ->with('admin.label.amount', $this->getFormMdSuccessBoxArray(4))
            ->add(
                'baseTotal',
                null,
                [
                    'label' => 'admin.label.base_amount',
                    'required' => false,
                    'disabled' => true,
                    'scale' => 2,
                    'grouping' => true,
                ]
            )
            ->add(
                'iva',
                null,
                [
                    'label' => 'admin.label.iva_amount',
                    'required' => false,
                    'disabled' => true,
                    'scale' => 2,
                    'grouping' => true,
                ]
            )
            ->add(
                'irpf',
                null,
                [
                    'label' => 'admin.label.irpf_amount',
                    'required' => false,
                    'disabled' => true,
                    'scale' => 2,
                    'grouping' => true,
                ]
            )
            ->add(
                'total',
                null,
                [
                    'label' => 'admin.label.total',
                    'required' => false,
                    'disabled' => true,
                    'scale' => 2,
                    'grouping' => true,
                ]
            )
            ->end()
        ;
        if ($this->id($this->getSubject())) { // is edit mode
            $formMapper
                ->with('admin.with.delivery_notes', $this->getFormMdSuccessBoxArray(4))
                ->add(
                    'deliveryNotes',
                    EntityType::class,
                    [
                        'label' => 'admin.label.delivery_notes',
                        'required' => false,
                        'class' => SaleDeliveryNote::class,
                        'multiple' => true,
                        'query_builder' => $this->rm->getSaleDeliveryNoteRepository()->getFilteredByEnterpriseSortedByNameQB($this->getUserLogedEnterprise()),
                        'by_reference' => false,
                    ]
                )
                ->end()
            ;
        } else { // is create mode
            $formMapper
                ->with('admin.with.delivery_notes', $this->getFormMdSuccessBoxArray(4))
                ->add(
                    'deliveryNotes',
                    EntityType::class,
                    [
                        'label' => 'admin.label.delivery_notes',
                        'required' => false,
                        'class' => SaleDeliveryNote::class,
                        'multiple' => true,
                        'expanded' => true,
                        'query_builder' => $this->rm->getSaleDeliveryNoteRepository()->getEmptyQueryBuilder(),
                        'by_reference' => false,
                    ]
                )
                ->end()
            ;
        }
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add(
                'series',
                null,
                [
                    'label' => 'admin.label.series',
                ],
                EntityType::class,
                [
                    'class' => SaleInvoiceSeries::class,
                    'choice_label' => 'name',
                    'query_builder' => $this->rm->getSaleInvoiceSeriesRepository()->getEnabledSortedByNameQB(),
                ]
            )
            ->add(
                'invoiceNumber',
                null,
                [
                    'label' => 'admin.label.invoice_number',
                ]
            )
            ->add(
                'date',
                DateRangeFilter::class,
                [
                    'label' => 'admin.label.date',
                ],
                DateRangePickerType::class,
                [
                    'field_options_start' => [
                        'label' => 'Desde',
                        'format' => 'dd/MM/yyyy',
                    ],
                    'field_options_end' => [
                        'label' => 'Hasta',
                        'format' => 'dd/MM/yyyy',
                    ],
                ]
            )
            ->add(
                'partner',
                ModelAutocompleteFilter::class,
                [
                    'label' => 'admin.label.partner',
                    'admin_code' => 'app.admin.partner',
                ],
                null,
                [
                    'property' => 'name',
                ]
            )
            ->add(
                'total',
                null,
                [
                    'label' => 'admin.label.total',
                ]
            )
            ->add(
                'hasBeenCounted',
                null,
                [
                    'label' => 'admin.label.has_been_counted',
                ]
            )
        ;
    }

    protected function configureDefaultFilterValues(array &$filterValues)
    {
        $filterValues['hasBeenCounted'] = [
            'type' => EqualOperatorType::TYPE_EQUAL,
            'value' => BooleanType::TYPE_NO,
        ];
    }

    /**
     * @param string $context
     *
     * @return QueryBuilder
     */
    public function createQuery($context = 'list')
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = parent::createQuery($context);
        $queryBuilder
            ->join($queryBuilder->getRootAliases()[0].'.partner', 'p')
            ->andWhere('p.enterprise = :enterprise')
            ->setParameter('enterprise', $this->getUserLogedEnterprise())
        ;

        return $queryBuilder;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add(
                'series',
                null,
                [
                    'label' => 'admin.label.series',
                    'editable' => false,
                    'associated_property' => 'name',
                    'sortable' => true,
                    'sort_field_mapping' => ['fieldName' => 'name'],
                    'sort_parent_association_mappings' => [['fieldName' => 'series']],
                ]
            )
            ->add(
                'invoiceNumber',
                null,
                [
                    'label' => 'admin.label.invoice_number',
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
                'partner',
                null,
                [
                    'label' => 'admin.label.partner',
                    'editable' => false,
                    'associated_property' => 'name',
                    'sortable' => true,
                    'sort_field_mapping' => ['fieldName' => 'name'],
                    'sort_parent_association_mappings' => [['fieldName' => 'partner']],
                    'admin_code' => 'partner_admin',
                ]
            )
            ->add(
                'total',
                null,
                [
                    'label' => 'admin.label.total',
                    'template' => 'admin/cells/list__cell_total_currency_number.html.twig',
                ]
            )
            ->add(
                'hasBeenCounted',
                null,
                [
                    'label' => 'admin.label.has_been_counted',
                ]
            )
            ->add(
                '_action',
                'actions',
                [
                    'actions' => [
                        'show' => ['template' => 'admin/buttons/list__action_show_button.html.twig'],
                        'edit' => ['template' => 'admin/buttons/list__action_edit_button.html.twig'],
                        'pdf' => ['template' => 'admin/buttons/list__action_pdf_invoice_button.html.twig'],
                        'pdfWithBackground' => ['template' => 'admin/buttons/list__action_pdf_invoice_with_background_button.html.twig'],
                        'count' => ['template' => 'admin/buttons/list__action_pdf_invoice_to_count_button.html.twig'],
//                        'delete' => ['template' => 'admin/buttons/list__action_delete_button.html.twig'],
                    ],
                    'label' => 'admin.actions',
                ]
            )
        ;
    }

    /**
     * @param SaleInvoice $object
     *
     * @throws NonUniqueResultException
     */
    public function prePersist($object)
    {
        $object->setInvoiceNumber($this->im->getLastInvoiceNumberBySerieAndEnterprise($object->getSeries(), $this->getUserLogedEnterprise()));
    }

    /**
     * @param SaleInvoice $object
     */
    public function postUpdate($object)
    {
        $this->im->calculateInvoiceImportsFromDeliveryNotes($object, $object->getDeliveryNotes());

        $this->em->flush();
    }
}
