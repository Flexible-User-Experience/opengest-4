<?php

namespace App\Admin\Sale;

use App\Admin\AbstractBaseAdmin;
use App\Entity\Enterprise\ActivityLine;
use App\Entity\Enterprise\CollectionDocumentType;
use App\Entity\Operator\Operator;
use App\Entity\Partner\PartnerBuildingSite;
use App\Entity\Partner\PartnerOrder;
use App\Entity\Sale\SaleDeliveryNote;
use App\Entity\Sale\SaleDeliveryNoteLine;
use App\Entity\Sale\SaleServiceTariff;
use App\Entity\Vehicle\Vehicle;
use App\Enum\UserRolesEnum;
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
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\DoctrineORMAdminBundle\Filter\DateRangeFilter;
use Sonata\DoctrineORMAdminBundle\Filter\ModelFilter;
use Sonata\Form\Type\BooleanType;
use Sonata\Form\Type\CollectionType;
use Sonata\Form\Type\DatePickerType;
use Sonata\Form\Type\DateRangePickerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class SaleDeliveryNoteAdmin.
 *
 * @category    Admin
 *
 * @auhtor      Rubèn Hierro <info@rubenhierro.com>
 */
class SaleDeliveryNoteAdmin extends AbstractBaseAdmin
{
    /**
     * @var string
     */
    protected $classnameLabel = 'Albarà';

    /**
     * @var string
     */
    protected $baseRoutePattern = 'vendes/albara';

    /**
     * Methods.
     */
    protected function configureDefaultSortValues(array &$sortValues): void
    {
        $sortValues[DatagridInterface::SORT_ORDER] = 'DESC';
        $sortValues[DatagridInterface::SORT_BY] = 'date';
    }

    public function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection
            ->add('pdf', $this->getRouterIdParameter().'/pdf')
        ;
    }

    public function configureExportFields(): array
    {
        return [
            'id',
            'dateToString',
            'saleRequestNumber',
            'isInvoiced',
            'saleInvoice',
            'order',
            'deliveryNoteReference',
            'saleServiceTariff',
            'serviceDescription',
            'partner.name',
            'partner.cifNif',
            'place',
            'buildingSite',
            'vehicle',
            'secondaryVehicle',
            'activityLine',
            'operator',
            'miniumHours',
            'hourPrice',
            'displacement',
            'miniumHolidayHours',
            'totalLines',
            'discount',
            'baseAmount',
            'finalTotal',
            'observations',
        ];
    }

    /**
     * @param array $actions
     */
    public function configureBatchActions($actions): array
    {
        if ($this->hasRoute('edit') && $this->hasAccess('edit')) {
            $actions['generateSaleInvoiceFromDeliveryNotes'] = [
                'label' => 'admin.action.generate_invoice_from_selected',
                'ask_confirmation' => false,
            ];
            $actions['generateStandardPrint'] = [
                'label' => 'admin.action.generate_standard_print_template_delivery_notes',
                'ask_confirmation' => false,
            ];
            $actions['generateDriverPrint'] = [
                'label' => 'admin.action.generate_driver_print_template_delivery_notes',
                'ask_confirmation' => false,
            ];
            $actions['generateStandardMail'] = [
                'label' => 'admin.action.generate_standard_mail_template_delivery_notes',
                'ask_confirmation' => false,
            ];
            $actions['generateDriverMail'] = [
                'label' => 'admin.action.generate_driver_mail_template_delivery_notes',
                'ask_confirmation' => false,
            ];
            $actions['deliveryNotesByClient'] = [
                'label' => 'admin.action.generate_delivery_notes_by_client',
                'ask_confirmation' => false,
            ];
            $actions['deliveryNotesList'] = [
                'label' => 'admin.action.generate_delivery_notes_list',
                'ask_confirmation' => false,
            ];
        }

        return $actions;
    }

    /**
     * @throws Exception
     */
    protected function configureFormFields(FormMapper $formMapper): void
    {
        if ($this->id($this->getSubject())) { // is edit mode
            $formMapper
                ->tab('Cabecera')
                    ->with('admin.with.delivery_note', $this->getFormMdSuccessBoxArray(4))
                    ->add(
                        'id',
                        null,
                        [
                            'label' => 'admin.label.delivery_note_id',
                            'required' => true,
                            'disabled' => true,
                        ]
                    )
                    ->end()
                ->end()
            ;
        }
        //estava aqui
        $formMapper
            ->tab('Cabecera')
                ->with('admin.with.delivery_note', $this->getFormMdSuccessBoxArray(3))
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
                    'isInvoiced',
                    BooleanType::class,
                    [
                        'label' => 'admin.label.invoiced',
                        'disabled' => true,
                        'transform' => true,
                    ]
                )
                ->add(
                    'saleInvoice',
                    TextType::class,
                    [
                        'label' => 'admin.with.sale_invoice',
                        'disabled' => true,
                    ]
                )
                ->add(
                    'partner',
                    ModelAutocompleteType::class,
                    [
                        'property' => 'name',
                        'label' => 'admin.label.partner',
                        'required' => true,
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
                    'cifNif',
                    TextType::class,
                    [
                        'label' => 'admin.label.CIF',
                        'required' => false,
                        'mapped' => false,
                        'disabled' => true,
                        'help' => '<i id="cif-nif-icon" class="fa fa-refresh fa-spin fa-fw hidden text-info"></i>',
                        'help_html' => true,
                    ]
                )
                ->add(
                    'deliveryNoteReference',
                    null,
                    [
                        'label' => 'admin.label.delivery_note_reference',
                        'required' => false,
                        'disabled' => false,
                    ]
                )
            ;
        if ($this->id($this->getSubject())) {
            $formMapper
                    ->add(
                        'buildingSite',
                        EntityType::class,
                        [
                            'class' => PartnerBuildingSite::class,
                            'label' => 'admin.label.partner_building_site',
                            'required' => false,
                            'query_builder' => $this->rm->getPartnerBuildingSiteRepository()
                                ->getEnabledFilteredByPartnerSortedByNameQB($this->getSubject()->getPartner()),
                        ]
                    )
                    ->add(
                        'order',
                        EntityType::class,
                        [
                            'class' => PartnerOrder::class,
                            'label' => 'admin.label.order',
                            'required' => false,
                            'query_builder' => $this->rm->getPartnerOrderRepository()
                                ->getEnabledFilteredByPartnerSortedByNumberQB($this->getSubject()->getPartner()),
                        ]
                    );
        }
        $formMapper
                ->end()
            ->end();
        if (false == $this->getSubject()->getSaleRequestHasDeliveryNotes()->isEmpty()) {
            $formMapper
                    ->tab('Cabecera')
                    ->with('Servicio', $this->getFormMdSuccessBoxArray(3))
                    ->add(
                        'saleServiceTariff',
                        EntityType::class,
                        [
                            'class' => SaleServiceTariff::class,
                            'label' => 'admin.label.sale_serivce_tariff',
                            'required' => true,
                            'query_builder' => $this->rm->getSaleServiceTariffRepository()->getEnabledSortedByNameQB(),
                        ]
                    )
                    ->end()
                    ->end();
        } else {
            $formMapper
                    ->tab('Cabecera')
                    ->with('Servicio', $this->getFormMdSuccessBoxArray(3))
                    ->add(
                        'saleServiceTariff',
                        EntityType::class,
                        [
                            'class' => SaleServiceTariff::class,
                            'label' => 'admin.label.sale_serivce_tariff',
                            'required' => false,
                            'query_builder' => $this->rm->getSaleServiceTariffRepository()->getEnabledSortedByNameQB(),
                        ]
                    )
                    ->end()
                    ->end();
        }
        $formMapper
                ->tab('Cabecera')
                ->with('Servicio', $this->getFormMdSuccessBoxArray(3))
                ->add(
                    'vehicle',
                    EntityType::class,
                    [
                        'class' => Vehicle::class,
                        'label' => 'admin.label.vehicle',
                        'required' => false,
                        'query_builder' => $this->rm->getVehicleRepository()->getFilteredByEnterpriseEnabledSortedByNameQB($this->getUserLogedEnterprise()),
                    ]
                )
                ->add(
                    'secondaryVehicle',
                    EntityType::class,
                    [
                        'class' => Vehicle::class,
                        'label' => 'admin.label.secondary_vehicle',
                        'required' => false,
                        'query_builder' => $this->rm->getVehicleRepository()->getFilteredByEnterpriseEnabledSortedByNameQB($this->getUserLogedEnterprise()),
                    ]
                )
                ->add(
                    'operator',
                    EntityType::class,
                    [
                        'class' => Operator::class,
                        'label' => 'admin.label.operator',
                        'required' => false,
                        'query_builder' => $this->rm->getOperatorRepository()->getFilteredByEnterpriseEnabledSortedByNameQB($this->getUserLogedEnterprise()),
                    ]
                )
                ->end()
                ->end();
        if (false == $this->getSubject()->getSaleRequestHasDeliveryNotes()->isEmpty()) {
            $formMapper
                ->tab('Cabecera')
                ->with('Servicio', $this->getFormMdSuccessBoxArray(3))
                ->add(
                    'serviceDescription',
                    TextareaType::class,
                    [
                        'label' => 'admin.label.service_description',
                        'required' => true,
                        'attr' => [
                            'style' => 'resize: vertical',
                            'rows' => 7,
                        ],
                    ]
                )
                ->end()
                ->end();
        } else {
            $formMapper
                ->tab('Cabecera')
                ->with('Servicio', $this->getFormMdSuccessBoxArray(3))
                ->add(
                    'serviceDescription',
                    TextareaType::class,
                    [
                        'label' => 'admin.label.service_description',
                        'required' => false,
                        'attr' => [
                            'style' => 'resize: vertical',
                            'rows' => 7,
                        ],
                    ]
                )
                ->end()
                ->end();
        }
        $formMapper
                ->tab('Cabecera')
                ->with('Servicio', $this->getFormMdSuccessBoxArray(3))
                ->add(
                    'place',
                    TextareaType::class,
                    [
                        'label' => 'admin.label.place',
                        'required' => false,
                        'attr' => [
                            'style' => 'resize: vertical',
                            'rows' => 3,
                        ],
                    ]
                )
                ->end()
            ->end()
        ;
        if (false == $this->getSubject()->getSaleRequestHasDeliveryNotes()->isEmpty()) {
            $formMapper
                ->tab('Cabecera')
                    ->with('Tarifa', $this->getFormMdSuccessBoxArray(3))
                    ->add(
                        'miniumHours',
                        NumberType::class,
                        [
                            'label' => 'admin.label.minimum_hours',
                            'disabled' => true,
                        ]
                    )
                    ->add(
                        'hourPrice',
                        NumberType::class,
                        [
                            'label' => 'admin.label.price_hour',
                            'disabled' => true,
                        ]
                    )
                    ->add(
                        'displacement',
                        NumberType::class,
                        [
                            'label' => 'admin.label.displacement',
                            'disabled' => true,
                        ]
                    )
                    ->add(
                        'miniumHolidayHours',
                        NumberType::class,
                        [
                            'label' => 'admin.label.minimum_holiday_hours',
                            'disabled' => true,
                        ]
                    )
                    ->add(
                        'increaseForHolidays',
                        NumberType::class,
                        [
                            'label' => 'admin.label.increase_for_holidays',
                            'disabled' => true,
                        ]
                    )
                    ->add(
                        'increaseForHolidaysPercentage',
                        PercentType::class,
                        [
                            'label' => 'admin.label.increase_for_holidays_percentage',
                            'disabled' => true,
                        ]
                    )
                    ->end()
                    ->with('admin.label.contact', $this->getFormMdSuccessBoxArray(3))
                    ->add(
                        'contactPersonName',
                        TextType::class,
                        [
                            'label' => 'admin.label.contact_person_name',
                            'disabled' => true,
                        ]
                    )
                    ->add(
                        'contactPersonPhone',
                        TextType::class,
                        [
                            'label' => 'admin.label.contact_person_phone',
                            'disabled' => true,
                        ]
                    )
                    ->add(
                        'contactPersonEmail',
                        TextType::class,
                        [
                            'label' => 'admin.label.contact_person_email',
                            'disabled' => true,
                        ]
                    )
                    ->end()
                ->end()
                ;
        }
        $formMapper
            ->tab('Cabecera')
                ->with('Otros', $this->getFormMdSuccessBoxArray(3))
                ->add(
                    'activityLine',
                    EntityType::class,
                    [
                        'class' => ActivityLine::class,
                        'label' => 'admin.label.activity_line',
                        'required' => false,
                        'query_builder' => $this->rm->getActivityLineRepository()->getFilteredByEnterpriseEnabledSortedByNameQB($this->getUserLogedEnterprise()),
                    ]
                )
                ->end()
            ->end()
        ;
        if (false == $this->getSubject()->getSaleRequestHasDeliveryNotes()->isEmpty()) {
            $formMapper
                ->tab('Cabecera')
                ->with('Otros', $this->getFormMdSuccessBoxArray(3))
                ->add(
                    'saleRequestNumber',
                    TextType::class,
                    [
                        'label' => 'admin.label.sale_request_id',
                        'required' => false,
                        'disabled' => true,
                    ]
                )
                ->end()
                ->end()
            ;
        }
        $formMapper
            ->tab('Cabecera')
            ->with('Otros', $this->getFormMdSuccessBoxArray(3))
                ->add(
                    'wontBeInvoiced',
                    CheckboxType::class,
                    [
                        'label' => 'admin.label.no_invoice',
                        'required' => false,
                    ]
                )
                ->add(
                    'observations',
                    TextareaType::class,
                    [
                        'label' => 'admin.label.comments',
                        'required' => false,
                        'attr' => [
                            'style' => 'resize: vertical',
                            'rows' => 3,
                        ],
                    ]
                )
                ->end()
            ->end()
        ;

        if ($this->id($this->getSubject())) { // is edit mode, disable on new subjetcs
            $formMapper
                ->tab('Líneas')
                    ->with('admin.label.lines', $this->getFormMdSuccessBoxArray(10))
                    ->add(
                        'saleDeliveryNoteLines',
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
                ->end()
            ;
        }
        $formMapper
            ->tab('Líneas')
                ->with('admin.label.amount', $this->getFormMdSuccessBoxArray(2))
                ->add(
                    'totalLines',
                    NumberType::class,
                    [
                        'label' => 'admin.label.total_lines',
                        'disabled' => true,
                    ]
                )
                ->add(
                    'discountTotal',
                    NumberType::class,
                    [
                        'label' => 'admin.label.discount_total',
                        'required' => true,
                        'disabled' => true,
                        'scale' => 2,
                    ]
                )
                ->add(
                    'baseAmount',
                    null,
                    [
                        'label' => 'admin.label.base_amount',
                        'required' => true,
                        'disabled' => true,
                        'scale' => 2,
                    ]
                )
                ->add(
                    'ivaTotal',
                    NumberType::class,
                    [
                        'label' => 'admin.label.iva_total',
                        'required' => true,
                        'disabled' => true,
                        'scale' => 2,
                    ]
                )
                ->add(
                    'irpfTotal',
                    NumberType::class,
                    [
                        'label' => 'admin.label.irpf_total',
                        'required' => true,
                        'disabled' => true,
                        'scale' => 2,
                    ]
                )
                ->add(
                    'finalTotal',
                    NumberType::class,
                    [
                        'label' => 'admin.label.final_total_with_taxes',
                        'disabled' => true,
                        'scale' => 2,
                    ]
                )
                ->add(
                    'collectionDocument',
                    EntityType::class,
                    [
                        'class' => CollectionDocumentType::class,
                        'label' => 'admin.label.payment_document',
                        'required' => false,
                        'query_builder' => $this->rm->getCollectionDocumentTypeRepository()->getFilteredByEnterpriseEnabledSortedByNameQB($this->getUserLogedEnterprise()),
                    ]
                )
                ->add(
                    'collectionTerm',
                    null,
                    [
                        'label' => 'admin.label.collection_term_1',
                        'required' => false,
                    ]
                )
                ->add(
                    'collectionTerm2',
                    null,
                    [
                        'label' => 'admin.label.collection_term_2',
                        'required' => false,
                    ]
                )
                ->add(
                    'collectionTerm3',
                    null,
                    [
                        'label' => 'admin.label.collection_term_3',
                        'required' => false,
                    ]
                )
                ->end()
            ->end()
            ->tab('Partes de trabajo')
                ->with('Líneas', $this->getFormMdSuccessBoxArray(12))
                    ->add(
                        'operatorWorkRegisters',
                        CollectionType::class,
                        [
                            'btn_add' => false,
                            'type_options' => [
                                'delete' => false,
                            ],
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
            ->end()
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
//        if ($this->acs->isGranted(UserRolesEnum::ROLE_ADMIN)) {
//            $datagridMapper
//                ->add(
//                    'enterprise',
//                    null,
//                    [
//                        'label' => 'Empresa',
//                    ]
//                )
//            ;
//        }
        $datagridMapper
            ->add(
            'id',
            null,
                [
                    'label' => 'Id',
                ]
            )
//            ->add(
//                'saleRequest.id',
//                null,
//                [
//                    'label' => 'admin.label.sale_request',
//                ]
//            )
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
                'buildingSite',
                null,
                [
                    'label' => 'admin.label.partner_building_site',
                ]
            )
            ->add(
                'order',
                null,
                [
                    'label' => 'admin.label.order',
                ]
            )
            ->add(
                'serviceDescription',
                null,
                [
                    'label' => 'admin.label.service_description',
                ]
            )
            ->add(
                'saleServiceTariff',
                null,
                [
                    'label' => 'admin.label.tonnage',
                ]
            )
            ->add(
                'deliveryNoteReference',
                null,
                [
                    'label' => 'admin.label.delivery_note_reference',
                ]
            )
            ->add(
                'baseAmount',
                null,
                [
                    'label' => 'admin.label.base_amount',
                ]
            )
            ->add(
                'discount',
                null,
                [
                    'label' => 'admin.label.discount',
                ]
            )
//            ->add(
//                'collectionTerm',
//                null,
//                [
//                    'label' => 'admin.label.expiry_date',
//                ]
//            )
//            ->add(
//                'collectionDocument',
//                null,
//                [
//                    'label' => 'admin.label.payment_document_type',
//                    'field_type' => null,
//                    'field_options' => [
//                            'query_builder' => $this->rm->getCollectionDocumentTypeRepository()->getFilteredByEnterpriseEnabledSortedByNameQB($this->getUserLogedEnterprise()),
//                        ],
//                ]
//            )
            ->add(
                'activityLine',
                null,
                [
                    'label' => 'admin.label.activity_line',
                ]
            )
            ->add(
                'vehicle',
                null,
                [
                    'label' => 'admin.label.vehicle',
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
                'wontBeInvoiced',
                null,
                [
                    'label' => 'admin.label.no_invoice',
                ]
            )
            ->add(
                'isInvoiced',
                null,
                [
                    'label' => 'admin.label.invoiced',
                ]
            )
        ;
    }

    public function configureQuery(ProxyQueryInterface $query): ProxyQueryInterface
    {
        $queryBuilder = parent::configureQuery($query);
        $queryBuilder
//            ->join($queryBuilder->getRootAliases()[0].'.enterprise', 'e')
            ->leftJoin($queryBuilder->getRootAliases()[0].'.partner', 'pa')
//            ->orderBy('e.name', 'ASC')
        ;
        if (!$this->acs->isGranted(UserRolesEnum::ROLE_ADMIN)) {
            $queryBuilder
                ->andWhere($queryBuilder->getRootAliases()[0].'.enterprise = :enterprise')
                ->setParameter('enterprise', $this->getUserLogedEnterprise())
            ;
        }

        return $queryBuilder;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
//        if ($this->acs->isGranted(UserRolesEnum::ROLE_ADMIN)) {
//            $listMapper
//                ->add(
//                    'enterprise',
//                    null,
//                    array(
//                        'label' => 'Empresa',
//                    )
//                )
//            ;
//        }
        $listMapper
            ->add(
                'id',
                null,
                [
                    'label' => 'Id',
                ]
            )
//            ->add(
//                'saleRequest',
//                null,
//                [
//                    'template' => 'admin/cells/list__cell_sale_delivery_note_sale_request.html.twig',
//                    'label' => 'admin.label.sale_request',
//                ]
//            )
            ->add(
                'saleInvoice',
                null,
                [
                    'template' => 'admin/cells/list__cell_sale_invoice_sale_delivery_note.html.twig',
                    'label' => 'admin.with.sale_invoice',
                ]
            )
            ->add(
                'date',
                null,
                [
                    'label' => 'admin.label.delivery_note_date',
                    'format' => 'd/m/Y',
                ]
            )
            ->add(
                'partner',
                null,
                [
                    'label' => 'admin.label.partner',
                    'admin_code' => 'app.admin.partner',
                ]
            )
            ->add(
                'buildingSite',
                null,
                [
                    'label' => 'admin.label.partner_building_site',
                ]
            )
            ->add(
                'saleServiceTariff',
                null,
                [
                    'label' => 'admin.label.tonnage',
                ]
            )
            ->add(
                'vehicle',
                null,
                [
                    'label' => 'admin.label.vehicle',
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
                'baseAmount',
                null,
                [
                    'label' => 'admin.label.base_amount',
                    'template' => 'admin/cells/list__cell_base_amount_currency_number.html.twig',
                ]
            )
            ->add(
                'isInvoiced',
                'boolean',
                [
                    'label' => 'admin.label.invoiced',
                    'transform' => true,
                ]
            )
            ->add(
                '_action',
                'actions',
                [
                    'actions' => [
//                        'show' => ['template' => 'admin/buttons/list__action_show_button.html.twig'],
                        'edit' => ['template' => 'admin/buttons/list__action_edit_button.html.twig'],
                        'pdf' => ['template' => 'admin/buttons/list__action_pdf_delivery_note_button.html.twig'],
                        'delete' => ['template' => 'admin/buttons/list__action_delete_sale_delivery_note_button.html.twig'],
                    ],
                    'label' => 'admin.actions',
                    'header_style' => 'width:120px;',
                ]
            )
        ;
    }

    /**
     * @param SaleDeliveryNote $object
     *
     * @throws NonUniqueResultException
     */
    public function prePersist($object): void
    {
        $object->setEnterprise($this->getUserLogedEnterprise());
        $object->setDeliveryNoteReference($this->dnm->getLastDeliveryNoteByenterprise($this->getUserLogedEnterprise()));
    }

    /**
     * @param SaleDeliveryNote $object
     */
    public function postUpdate($object): void
    {
        $totalPrice = 0;
        /** @var SaleDeliveryNoteLine $deliveryNoteLine */
        foreach ($object->getSaleDeliveryNoteLines() as $deliveryNoteLine) {
            $base = $deliveryNoteLine->getUnits() * $deliveryNoteLine->getPriceUnit() * (1 - $deliveryNoteLine->getDiscount() / 100);
//            $iva = $base * ($deliveryNoteLine->getIva() / 100);
//            $irpf = $base * ($deliveryNoteLine->getIrpf() / 100);
//            $deliveryNoteLine->setTotal($base + $iva - $irpf);
            $deliveryNoteLine->setTotal($base);
            $subtotal = $deliveryNoteLine->getTotal();
            $totalPrice = $totalPrice + $subtotal;
        }
        $object->setBaseAmount($totalPrice * (1 - $object->getDiscount() / 100));

        $this->em->flush();
    }
}
