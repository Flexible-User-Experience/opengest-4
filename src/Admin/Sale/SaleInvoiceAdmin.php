<?php

namespace App\Admin\Sale;

use App\Admin\AbstractBaseAdmin;
use App\Entity\Enterprise\CollectionDocumentType;
use App\Entity\Partner\PartnerBuildingSite;
use App\Entity\Partner\PartnerDeliveryAddress;
use App\Entity\Partner\PartnerOrder;
use App\Entity\Partner\PartnerType;
use App\Entity\Sale\SaleDeliveryNote;
use App\Entity\Sale\SaleInvoice;
use App\Entity\Setting\City;
use App\Entity\Setting\SaleInvoiceSeries;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Exception;
use Sonata\AdminBundle\Admin\AbstractAdmin as Admin;
use Sonata\AdminBundle\Datagrid\DatagridInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Filter\Model\FilterData;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\AdminBundle\Form\Type\Operator\EqualOperatorType;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\DoctrineORMAdminBundle\Filter\CallbackFilter;
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
     * Methods.
     */
    public function generateBaseRoutePattern(bool $isChildAdmin = false): string
    {
        return 'vendes/factura';
    }

    protected function configureDefaultSortValues(array &$sortValues): void
    {
        $sortValues[DatagridInterface::PAGE] = 1;
        $sortValues[DatagridInterface::SORT_ORDER] = 'DESC';
        $sortValues[DatagridInterface::SORT_BY] = 'invoiceNumber';
    }

    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection
            ->add('pdf', $this->getRouterIdParameter().'/pdf')
            ->add('pdfWithBackground', $this->getRouterIdParameter().'/pdf-with-background')
            ->add('count', $this->getRouterIdParameter().'/to-count')
            ->add('clone', $this->getRouterIdParameter().'/clone')
            ->add('setHasNotBeenCounted', $this->getRouterIdParameter().'/descontabilizar')
            ->add('getJsonNextInvoiceNumberForSeriesIdAndInvoice', $this->getRouterIdParameter().'/get-json-next-invoice-number-for-series-id-and-invoice')
            ->add('getJsonAvailableInvoiceNumbersForSeries', $this->getRouterIdParameter().'/get-json-available-invoice-numbers-for-serie')
            ->add('createEInvoice', $this->getRouterIdParameter().'/generar-e-factura')
            ->remove('show')
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
            $newActions['generatePdfsForEmail'] = [
                'label' => 'admin.action.generate_pdfs_for_email',
                'ask_confirmation' => false,
            ];
            $newActions['generatePdfsToPrint'] = [
                'label' => 'admin.action.generate_pdfs_to_print',
                'ask_confirmation' => false,
            ];
            $newActions['invoiceListByClient'] = [
                'label' => 'admin.action.generate_invoice_list_by_client',
                'ask_confirmation' => false,
            ];
            $newActions['invoiceList'] = [
                'label' => 'admin.action.generate_invoice_list',
                'ask_confirmation' => false,
            ];
        }

        return array_merge($newActions, $actions);
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
            'discountFormatted',
            'baseTotalFormatted',
            'ivaFormatted',
            'irpfFormatted',
            'totalFormatted',
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
                    'disabled' => true,
                    'label' => 'admin.label.partner',
                    'callback' => function ($admin, $property, $value) {
                        /** @var Admin $admin */
                        $datagrid = $admin->getDatagrid();
                        /** @var QueryBuilder $queryBuilder */
                        $queryBuilder = $datagrid->getQuery();
                        $queryBuilder
                            ->andWhere($queryBuilder->getRootAliases()[0].'.enterprise = :enterprise')
                            ->andWhere($queryBuilder->getRootAliases()[0].'.type = :type')
                            ->andWhere($queryBuilder->getRootAliases()[0].'.enabled = :enabled')
                            ->setParameter('enterprise', $this->getUserLogedEnterprise())
                            ->setParameter('type', $this->getModelManager()->find(PartnerType::class, 1))
                            ->setParameter('enabled', true)
                        ;
                        $datagrid->setValue($property, null, $value);
                    },
                ],
                [
                    'admin_code' => 'app.admin.partner',
                ]
            )
            ->add(
                'partnerName',
                null,
                [
                    'label' => 'admin.label.partner',
                    'required' => true,
                    'disabled' => false,
                ]
            )
            ->add(
                'partnerCifNif',
                null,
                [
                    'label' => 'CIF/NIF',
                    'required' => true,
                    'disabled' => false,
                ]
            )
            ->add(
                'partnerMainAddress',
                null,
                [
                    'label' => 'admin.label.main_address',
                    'required' => true,
                    'disabled' => false,
                ]
            )
            ->add(
                'partnerMainCity',
                EntityType::class,
                [
                    'class' => City::class,
                    'label' => 'admin.label.main_city',
                    'required' => true,
                    'query_builder' => $this->rm->getCityRepository()->getCitiesSortedByNameQB(),
                ]
            )
        ;
        if ($this->id($this->getSubject())) { // is edit mode
            if ($this->getSubject()->getPartnerMainCity()) {
                $formMapper
                    ->add(
                        'partnerMainCity.province.countryName',
                        null,
                        [
                            'label' => 'admin.label.country_name',
                            'required' => false,
                            'disabled' => true,
                        ]
                    )
                    ->add(
                        'partnerMainCity.province',
                        null,
                        [
                            'label' => 'admin.label.province',
                            'required' => false,
                            'disabled' => true,
                        ]
                    )
                ;
            }
        }
        $formMapper
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
        ;
        if ($this->getSubject()->getIva21() > 0) {
            $formMapper
                ->add(
                    'iva21',
                    null,
                    [
                        'label' => 'admin.label.iva21_amount',
                        'required' => false,
                        'disabled' => true,
                        'scale' => 2,
                        'grouping' => true,
                    ]
                )
            ;
        }
        if ($this->getSubject()->getIva10() > 0) {
            $formMapper
                ->add(
                    'iva10',
                    null,
                    [
                        'label' => 'admin.label.iva10_amount',
                        'required' => false,
                        'disabled' => true,
                        'scale' => 2,
                        'grouping' => true,
                    ]
                )
            ;
        }
        if ($this->getSubject()->getIva4() > 0) {
            $formMapper
                ->add(
                    'iva4',
                    null,
                    [
                        'label' => 'admin.label.iva4_amount',
                        'required' => false,
                        'disabled' => true,
                        'scale' => 2,
                        'grouping' => true,
                    ]
                )
            ;
        }
        if ($this->getSubject()->getIva0() > 0) {
            $formMapper
                ->add(
                    'iva0',
                    null,
                    [
                        'label' => 'admin.label.iva0_amount',
                        'required' => false,
                        'disabled' => true,
                        'scale' => 2,
                        'grouping' => true,
                    ]
                )
            ;
        }
        $formMapper
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
        ;
        if ($this->getSubject()->getFirstDeliveryNote()) {
            if ($this->getSubject()->getFirstDeliveryNote()->getCollectionTerm()) {
                $formMapper
                    ->add(
                        'firstDeliveryNote.collectionTerm',
                        null,
                        [
                            'label' => 'admin.label.collection_term_1',
                            'required' => false,
                            'disabled' => true,
                        ]
                    );
            }
            if ($this->getSubject()->getFirstDeliveryNote()->getCollectionTerm2()) {
                $formMapper
                    ->add(
                        'firstDeliveryNote.collectionTerm2',
                        null,
                        [
                            'label' => 'admin.label.collection_term_2',
                            'required' => false,
                            'disabled' => true,
                        ]
                    );
            }
            if ($this->getSubject()->getFirstDeliveryNote()->getCollectionTerm3()) {
                $formMapper
                    ->add(
                        'firstDeliveryNote.collectionTerm3',
                        null,
                        [
                            'label' => 'admin.label.collection_term_3',
                            'required' => false,
                            'disabled' => true,
                        ]
                    );
            }
        }
        $formMapper
            ->add(
                'partner.payDay1',
                null,
                [
                    'label' => 'admin.label.pay_day_1',
                    'required' => false,
                    'disabled' => true,
                ]
            );
        if ($this->getSubject()->getPartner()) {
            if ($this->getSubject()->getPartner()->getPayDay2()) {
                $formMapper
                    ->add(
                        'partner.payDay2',
                        null,
                        [
                            'label' => 'admin.label.pay_day_2',
                            'required' => false,
                            'disabled' => true,
                        ]
                    );
            }
            if ($this->getSubject()->getPartner()->getPayDay3()) {
                $formMapper
                    ->add(
                        'partner.payDay3',
                        null,
                        [
                            'label' => 'admin.label.pay_day_3',
                            'required' => false,
                            'disabled' => true,
                        ]
                    );
            }
        }
        $formMapper
            ->add(
                'collectionDocumentType',
                EntityType::class,
                [
                    'class' => CollectionDocumentType::class,
                    'label' => 'admin.label.collection_document_type',
                    'required' => false,
                    'disabled' => true,
                ]
            );
        if ($this->getSubject()->getCollectionDocumentType()) {
            if (str_contains('transferencia', strtolower($this->getSubject()->getCollectionDocumentType()->getName()))) {
                if ($this->getSubject()->getPartner()->getTransferAccount()) {
                    $formMapper
                        ->add(
                            'partner.transferAccount.name',
                            null,
                            [
                                'label' => 'admin.label.transference_bank',
                                'required' => false,
                                'disabled' => true,
                            ]
                        )
                    ;
                }
            } elseif (str_contains('recibo', strtolower($this->getSubject()->getCollectionDocumentType()->getName()))) {
                $formMapper
                    ->add(
                        'partnerIban',
                        null,
                        [
                            'label' => 'IBAN',
                            'required' => false,
                            'disabled' => false,
                        ]
                    )
                    ->add(
                        'partnerSwift',
                        null,
                        [
                            'label' => 'SWIFT',
                            'required' => false,
                            'disabled' => false,
                        ]
                    )
                ;
            }
        }
        $formMapper
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
                ->with('observations', $this->getFormMdSuccessBoxArray(6))
                ->add(
                    'observations',
                    null,
                    [
                        'required' => false,
                        'label' => false,
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

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
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
                    'show_filter' => true,
                ],
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
                'partner',
                ModelFilter::class,
                [
                    'label' => 'admin.label.partner',
                    'admin_code' => 'app.admin.partner',
                    'field_type' => ModelAutocompleteType::class,
                    'field_options' => [
                        'property' => 'name',
                        'callback' => $this->partnerModelAutocompleteCallback(),
                    ],
                    'show_filter' => true,
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
            ->add('order',
                CallbackFilter::class,
                [
                    'callback' => static function (ProxyQueryInterface $query, string $alias, string $field, FilterData $data): bool {
                        if (!$data->hasValue()) {
                            return false;
                        }
                        $query
                            ->leftJoin(sprintf('%s.deliveryNotes', $alias), 'dn')
                            ->andWhere('dn.order = :order')
                            ->setParameter('order', $data->getValue());

                        return true;
                    },
                    'field_type' => EntityType::class,
                    'field_options' => [
                        'class' => PartnerOrder::class,
                        'choice_label' => 'number',
                        'query_builder' => $this->rm->getPartnerOrderRepository()->getEnabledSortedByNumberQB(),
                    ],
                ]
            )
            ->add('buildingSite',
                CallbackFilter::class,
                [
                    'callback' => static function (ProxyQueryInterface $query, string $alias, string $field, FilterData $data): bool {
                        if (!$data->hasValue()) {
                            return false;
                        }
                        $query
                            ->leftJoin(sprintf('%s.deliveryNotes', $alias), 'dn2')
                            ->andWhere('dn2.buildingSite = :buildingSite')
                            ->setParameter('buildingSite', $data->getValue());

                        return true;
                    },
                    'field_type' => EntityType::class,
                    'field_options' => [
                        'class' => PartnerBuildingSite::class,
                        'choice_label' => 'name',
                        'query_builder' => $this->rm->getPartnerBuildingSiteRepository()->getEnabledSortedByNameQB(),
                    ],
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
                'partnerName',
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
                'saleInvoiceGenerated',
                null,
                [
                    'label' => 'admin.label.sale_invoice_generated',
                ]
            )
            ->add(
                '_action',
                'actions',
                [
                    'actions' => [
                        'show' => ['template' => 'admin/buttons/list__action_show_button.html.twig'],
                        'edit' => ['template' => 'admin/buttons/list__action_edit_button.html.twig'],
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
     *
     * @throws NonUniqueResultException
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
        if (($originalObject['date'] !== $object->getDate()) || ($originalObject['collectionDocumentType'] !== $object->getCollectionDocumentType())) {
            $this->refreshDueDates($object);
        }
    }

    /**
     * @param SaleInvoice $object
     */
    public function postUpdate($object): void
    {
        /** @var SaleInvoice $originalObject */
        $originalObject = $this->em->getUnitOfWork()->getOriginalEntityData($object);
        $this->im->calculateInvoiceImportsFromDeliveryNotes($object, $object->getDeliveryNotes());
        /** @var SaleDeliveryNote $saleDeliveryNote */
        foreach ($object->getDeliveryNotes() as $saleDeliveryNote) {
            $saleDeliveryNote->setIsInvoiced(true);
            $this->em->persist($saleDeliveryNote);
        }
        if ($originalObject['total'] !== $object->getTotal()) {
            $this->refreshDueDates($object);
        }

        $this->em->flush();
    }

    private function refreshDueDates(SaleInvoice $saleInvoice)
    {
        foreach ($saleInvoice->getSaleInvoiceDueDates() as $dueDate) {
            $this->em->remove($dueDate);
            $this->em->flush();
        }
        $this->im->createDueDatesFromSaleInvoice($saleInvoice);
    }
}
