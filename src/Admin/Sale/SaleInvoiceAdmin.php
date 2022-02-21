<?php

namespace App\Admin\Sale;

use App\Admin\AbstractBaseAdmin;
use App\Entity\Enterprise\CollectionDocumentType;
use App\Entity\Partner\PartnerDeliveryAddress;
use App\Entity\Sale\SaleDeliveryNote;
use App\Entity\Sale\SaleInvoice;
use App\Entity\Setting\SaleInvoiceSeries;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Exception;
use Sonata\AdminBundle\Admin\AbstractAdmin as Admin;
use Sonata\AdminBundle\Datagrid\DatagridInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\AdminBundle\Form\Type\Operator\EqualOperatorType;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\DoctrineORMAdminBundle\Filter\DateRangeFilter;
use Sonata\DoctrineORMAdminBundle\Filter\ModelFilter;
use Sonata\Form\Type\BooleanType;
use Sonata\Form\Type\CollectionType;
use Sonata\Form\Type\DatePickerType;
use Sonata\Form\Type\DateRangePickerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

/**
 * Class SaleInvoiceAmin.
 *
 * @category    Admin
 */
class SaleInvoiceAdmin extends AbstractBaseAdmin
{
    /**
     * @var string
     */
    protected $classnameLabel = 'Factura';

    /**
     * @var string
     */
    protected $baseRoutePattern = 'vendes/factura';

    /**
     * Methods.
     */
    protected function configureDefaultSortValues(array &$sortValues): void
    {
        $sortValues[DatagridInterface::PAGE] = 1;
        $sortValues[DatagridInterface::SORT_ORDER] = 'DESC';
        $sortValues[DatagridInterface::SORT_BY] = 'date';
    }

    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection
            ->add('pdf', $this->getRouterIdParameter().'/pdf')
            ->add('pdfWithBackground', $this->getRouterIdParameter().'/pdf-with-background')
            ->add('count', $this->getRouterIdParameter().'/to-count')
            ->add('setHasNotBeenCounted', $this->getRouterIdParameter().'/descontabilizar')
            ->remove('show')
            ->remove('create')
        ;
    }

    /**
     * @param array $actions
     */
    public function configureBatchActions($actions): array
    {
        if ($this->hasRoute('edit') && $this->hasAccess('edit')) {
            $actions['generatePdfsForEmail'] = [
                'label' => 'admin.action.generate_pdfs_for_email',
                'ask_confirmation' => false,
            ];
            $actions['generatePdfsToPrint'] = [
                'label' => 'admin.action.generate_pdfs_to_print',
                'ask_confirmation' => false,
            ];
            $actions['invoiceList'] = [
                'label' => 'admin.action.generate_invoice_list',
                'ask_confirmation' => false,
            ];
        }

        return $actions;
    }

    public function configureExportFields(): array
    {
        return [
            'id',
            'series',
            'invoiceNumber',
            'dateFormatted',
            'hasBeenCounted',
            'partner.code',
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
    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->with('admin.with.general', $this->getFormMdSuccessBoxArray(3))
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
            ->with('admin.label.amount', $this->getFormMdSuccessBoxArray(3))
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
            ->add(
                'collectionDocumentType',
                EntityType::class,
                [
                    'class' => CollectionDocumentType::class,
                    'label' => 'admin.label.collection_document_type',
                    'required' => false,
                ]
            )
            ->end()
        ;
        if ($this->id($this->getSubject())) { // is edit mode
            $formMapper
                ->with('admin.label.delivery_address', $this->getFormMdSuccessBoxArray(3))
                ->add(
                    'deliveryAddress',
                    EntityType::class,
                    [
                        'label' => 'admin.label.delivery_address',
                        'required' => false,
                        'class' => PartnerDeliveryAddress::class,
                        'query_builder' => $this->rm->getPartnerDeliveryAddressRepository()->getFilteredByPartnerSortedByNameQB($this->getSubject()->getPartner()->getId()),
                        'placeholder' => '--- Seleccione una dirección de envio alternativa ---',
    //                    'choice_label' => 'name',
                    ]
                )
                ->end()
                    ->with('admin.with.delivery_notes', $this->getFormMdSuccessBoxArray(3))
                    ->add(
                        'deliveryNotes',
                        EntityType::class,
                        [
                            'label' => 'admin.label.delivery_notes',
                            'required' => false,
                            'class' => SaleDeliveryNote::class,
                            'multiple' => true,
                            'query_builder' => $this->rm->getSaleDeliveryNoteRepository()->getFilteredByEnterpriseAndPartnerSortedByNameQB(
                                $this->getUserLogedEnterprise(),
                                $this->getSubject()->getPartner()
                            ),
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
        if ($this->id($this->getSubject())) {
            $formMapper
            ->with('admin.label.due_dates', $this->getFormMdSuccessBoxArray(6))
                ->add(
                    'saleInvoiceDueDates',
                    CollectionType::class,
                    [
                        'required' => false,
                        'error_bubbling' => true,
                        'label' => false,
                    ],
                    [
                        'edit' => 'inline',
                        'inline' => 'table',
                    ]
                )
                ->end()
                ;
        }
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add(
                'series',
                null,
                [
                    'label' => 'admin.label.series',
                    'field_type' => EntityType::class,
                    'field_options' => [
                            'class' => SaleInvoiceSeries::class,
                            'choice_label' => 'name',
                            'query_builder' => $this->rm->getSaleInvoiceSeriesRepository()->getEnabledSortedByNameQB(),
                    ],
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
                'partner.code',
                null,
                [
                    'label' => 'admin.label.partner_code',
                ]
            )
            ->add(
                'partner',
                ModelFilter::class,
                [
                    'label' => 'admin.label.partner',
                    'admin_code' => 'app.admin.partner',
                    'field_type' => ModelAutocompleteType::class,
                    'field_options' => [
                        'property' => 'name',
                    ],
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

    protected function configureDefaultFilterValues(array &$filterValues): void
    {
        $filterValues['hasBeenCounted'] = [
            'type' => EqualOperatorType::TYPE_EQUAL,
            'value' => BooleanType::TYPE_NO,
        ];
    }

    public function configureQuery(ProxyQueryInterface $query): ProxyQueryInterface
    {
        $queryBuilder = parent::configureQuery($query);
        $queryBuilder
            ->join($queryBuilder->getRootAliases()[0].'.partner', 'p')
            ->andWhere('p.enterprise = :enterprise')
            ->setParameter('enterprise', $this->getUserLogedEnterprise())
        ;

        return $queryBuilder;
    }

    protected function configureListFields(ListMapper $listMapper): void
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
                'partner.code',
                null,
                [
                    'label' => 'admin.label.partner_code',
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
                    'admin_code' => 'app.admin.partner',
                ]
            )
            ->add(
                'deliveryAddress',
                null,
                [
                    'label' => 'admin.label.delivery_address',
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
    public function prePersist($object): void
    {
        $object->setInvoiceNumber($this->im->getLastInvoiceNumberBySerieAndEnterprise($object->getSeries(), $this->getUserLogedEnterprise()));
    }

    /**
     * @param SaleInvoice $object
     */
    public function preUpdate($object): void
    {
        /** @var SaleInvoice $originalObject */
        $originalObject = $this->em->getUnitOfWork()->getOriginalEntityData($object);
        if (
            ($object->getInvoiceNumber() != $originalObject['invoiceNumber'])
            ||
            ($object->getSeries() != $originalObject['series'])) {
            if (!$this->im->checkIfNumberIsAllowedBySerieAndEnterprise($object->getSeries(), $object->getPartner()->getEnterprise(), $object->getInvoiceNumber())) {
                $this->getRequest()->getSession()->getFlashBag()->add('warning', 'No se ha modificado el numero y/o serie de factura porque el '.$object->getInvoiceNumber().' no está permitido');
                $object->setInvoiceNumber($originalObject['invoiceNumber']);
                $object->setSeries($originalObject['series']);
            }
        }
    }

    /**
     * @param SaleInvoice $object
     */
    public function postUpdate($object): void
    {
        $this->im->calculateInvoiceImportsFromDeliveryNotes($object, $object->getDeliveryNotes());

        $this->em->flush();
    }
}
