<?php

namespace App\Admin\Sale;

use App\Admin\AbstractBaseAdmin;
use App\Entity\Operator\Operator;
use App\Entity\Partner\PartnerBuildingSite;
use App\Entity\Partner\PartnerOrder;
use App\Entity\Sale\SaleDeliveryNote;
use App\Entity\Sale\SaleRequest;
use App\Entity\Sale\SaleRequestHasDeliveryNote;
use App\Entity\Sale\SaleServiceTariff;
use App\Entity\Setting\User;
use App\Entity\Vehicle\Vehicle;
use App\Enum\SaleRequestStatusEnum;
use App\Enum\UserRolesEnum;
use DateTime;
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
use Sonata\Form\Type\CollectionType;
use Sonata\Form\Type\DatePickerType;
use Sonata\Form\Type\DateRangePickerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;

/**
 * Class SaleRequestAdmin.
 *
 * @category    Admin
 *
 * @auhtor      Rubèn Hierro <info@rubenhierro.com>
 */
class SaleRequestAdmin extends AbstractBaseAdmin
{
    /**
     * @var string
     */
    protected $classnameLabel = 'sale_request';

    /**
     * @var string
     */
    protected $baseRoutePattern = 'vendes/peticio';

    /**
     * Methods.
     */
    protected function configureDefaultSortValues(array &$sortValues): void
    {
        $sortValues[DatagridInterface::SORT_ORDER] = 'DESC';
        $sortValues[DatagridInterface::SORT_BY] = 'requestDate';
    }

    public function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection
            ->add('pdf', $this->getRouterIdParameter().'/pdf')
            ->add('clone', $this->getRouterIdParameter().'/clone')
            ->add('generateDeliveryNoteFromSaleRequest', $this->getRouterIdParameter().'/generateDeliveryNote')
            ->add('calendar', 'calendario')
            ->remove('show')
        ;
    }

    public function configureExportFields(): array
    {
        return [
            'id',
            'requestDateFormatted',
            'service',
            'serviceDescription',
            'serviceDateString',
            'partner.name',
            'partner.cifNif',
            'place',
            'buildingSite',
            'vehicle',
            'secondaryVehicle',
            'operator',
            'miniumHours',
            'hourPrice',
            'displacement',
            'miniumHolidayHours',
            'status',
            'onlyDeliveryNote',
            'observations',
        ];
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
            $newActions['generatedeliverynotefromsalerequests'] = [
                'label' => 'admin.action.generate_delivery_note_from_selected',
                'ask_confirmation' => false,
            ];
        }

        return array_merge($newActions, $actions);
    }

    /**
     * @throws Exception
     */
    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->tab('General')
            ->with('admin.label.sale_request', $this->getFormMdSuccessBoxArray(3))
            ->add(
                'status',
                ChoiceType::class,
                [
                    'choices' => SaleRequestStatusEnum::getEnumArray(),
                    'label' => 'admin.label.status',
                ]
            )
            ->add(
                'partner',
                ModelAutocompleteType::class,
                [
                    'property' => 'name',
                    'label' => 'admin.label.partner',
                    'required' => true,
                    'callback' => $this->partnerModelAutocompleteCallback(),
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
                'buildingSite',
                EntityType::class,
                [
                    'class' => PartnerBuildingSite::class,
                    'label' => 'admin.label.partner_building_site',
                    'required' => false,
                    'query_builder' => $this->rm->getPartnerBuildingSiteRepository()->getEnabledSortedByNameQB(),
                ]
            )
            ->add(
                'serviceDate',
                DatePickerType::class,
                [
                    'label' => 'admin.label.service_date',
                    'format' => 'd/M/y',
                    'required' => true,
                ]
            )
            ->add(
                'serviceTime',
                TimeType::class,
                [
                    'label' => 'admin.label.service_time',
                    'required' => false,
                    'widget' => 'single_text',
                ]
            )
            ->end()
            ->with('admin.label.service', $this->getFormMdSuccessBoxArray(3))
            ->add(
                'service',
                EntityType::class,
                [
                    'class' => SaleServiceTariff::class,
                    'label' => 'admin.label.sale_serivce_tariff',
                    'required' => true,
                    'placeholder' => '---seleccione una opción---',
                    'query_builder' => $this->rm->getSaleServiceTariffRepository()->getEnabledSortedByNameQB(),
                ]
            )
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
            ->add(
                'place',
                null,
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
            ->with('admin.label.tariff', $this->getFormMdSuccessBoxArray(3))
            ->add(
                'selectTariff',
                TextType::class,
                [
                    'label' => 'admin.label.tariffs',
                    'required' => false,
                    'mapped' => false,
                    'disabled' => true,
                ]
            )
            ->add(
                'miniumHours',
                null,
                [
                    'label' => 'admin.label.minimum_hours',
                    'required' => false,
                    'help' => '<i id="minium-hours-icon" class="fa fa-refresh fa-spin fa-fw hidden text-info"></i>',
                    'help_html' => true,
                ]
            )
            ->add(
                'hourPrice',
                null,
                [
                    'label' => 'admin.label.price_hour',
                    'required' => false,
                    'help' => '<i id="hour-price-icon" class="fa fa-refresh fa-spin fa-fw hidden text-info"></i>',
                    'help_html' => true,
                ]
            )
            ->add(
                'displacement',
                null,
                [
                    'label' => 'admin.label.displacement',
                    'required' => false,
                    'help' => '<i id="displacement-icon" class="fa fa-refresh fa-spin fa-fw hidden text-info"></i>',
                    'help_html' => true,
                ]
            )
            ->add(
                'miniumHolidayHours',
                null,
                [
                    'label' => 'admin.label.minimum_holiday_hours',
                    'required' => false,
                    'help' => '<i id="minium-holiday-hours-icon" class="fa fa-refresh fa-spin fa-fw hidden text-info"></i>',
                    'help_html' => true,
                ]
            )
            ->add(
                'increaseForHolidays',
                null,
                [
                    'label' => 'admin.label.increase_for_holidays',
                    'required' => false,
                    'help' => '<i id="increase-for-holidays-icon" class="fa fa-refresh fa-spin fa-fw hidden text-info"></i>',
                    'help_html' => true,
                ]
            )
            ->add(
                'increaseForHolidaysPercentage',
                null,
                [
                    'label' => 'admin.label.increase_for_holidays_percentage',
                    'required' => false,
                    'help' => '<i id="increase-for-holidays-percentage-icon" class="fa fa-refresh fa-spin fa-fw hidden text-info"></i>',
                    'help_html' => true,
                ]
            )
            ->end()
            ->with('admin.label.contact', $this->getFormMdSuccessBoxArray(3))
            ->add(
                'selectContactPersonName',
                TextType::class,
                [
                    'label' => 'admin.label.client_contacts',
                    'required' => false,
                    'mapped' => false,
                ]
            )
            ->add(
                'contactPersonName',
                TextType::class,
                [
                    'label' => 'admin.label.contact_person_name',
                    'required' => false,
                ]
            )
            ->add(
                'contactPersonPhone',
                TextType::class,
                [
                    'label' => 'admin.label.contact_person_phone',
                    'required' => false,
                ]
            )
            ->add(
                'contactPersonEmail',
                TextType::class,
                [
                    'label' => 'Email persona contacte',
                    'required' => false,
                ]
            )
            ->end()
            ->with('admin.label.others', $this->getFormMdSuccessBoxArray(3))
            ->add(
                'requestDate',
                DatePickerType::class,
                [
                    'label' => 'admin.label.sale_request_date',
                    'format' => 'd/M/y',
                    'required' => false,
                    'dp_default_date' => (new DateTime())->format('d/m/Y'),
                ]
            )
            ->add(
                'attendedBy',
                EntityType::class,
                [
                    'label' => 'admin.label.attended_by',
                    'required' => false,
                    'class' => User::class,
                    'disabled' => true,
                    'data' => $this->getUser(),
                ]
            )
            ->add(
                'observations',
                null,
                [
                    'label' => 'admin.label.comments',
                    'required' => false,
                    'attr' => [
                        'style' => 'resize: vertical',
                        'rows' => 2,
                    ],
                ]
            )
            ->end()
            ->end()
            ->tab('Documentos')
            ->with('admin.with.sale_request_documents', $this->getFormMdSuccessBoxArray(6))
            ->add(
                'documents',
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

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        if ($this->acs->isGranted(UserRolesEnum::ROLE_SUPER_ADMIN)) {
            $datagridMapper
                ->add(
                    'enterprise',
                    null,
                    [
                        'label' => 'admin.label.company',
                    ]
                )
            ;
        }
        $datagridMapper
            ->add(
                'id',
                null,
                [
                    'label' => 'Id',
                ]
            )
            ->add(
                'attendedBy',
                null,
                [
                    'label' => 'admin.label.attended_by',
                ]
            )
            ->add(
                'status',
                null,
                [
                    'label' => 'admin.label.status',
                    'field_type' => ChoiceType::class,
                    'field_options' => [
                            'choices' => SaleRequestStatusEnum::getEnumArray(),
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
                ]
            )
        ;
        $filterParameters = $datagridMapper->getAdmin()->getFilterParameters();
        $filteredPartner = null;
        if (isset($filterParameters['partner'])) {
            $filteredPartnerId = $filterParameters['partner']['value'];
            $filteredPartner = $this->rm->getPartnerRepository()->find($filteredPartnerId);
        }
        if ($filteredPartner) {
            $datagridMapper
                ->add(
                    'buildingSite',
                    null,
                    [
                        'label' => 'admin.label.partner_building_site',
                        'field_type' => EntityType::class,
                        'field_options' => [
                            'class' => PartnerBuildingSite::class,
                            'query_builder' => $this->rm->getPartnerBuildingSiteRepository()->getEnabledFilteredByPartnerSortedByNameQB($filteredPartner),
                        ],
                    ]
                )
                ->add(
                    'order',
                    null,
                    [
                        'label' => 'admin.label.order',
                        'field_type' => EntityType::class,
                        'field_options' => [
                            'class' => PartnerOrder::class,
                            'query_builder' => $this->rm->getPartnerOrderRepository()->getEnabledFilteredByPartnerSortedByNumberQB($filteredPartner),
                        ],
                    ]
                )
            ;
        } else {
            $datagridMapper
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
            ;
        }
        $datagridMapper
            ->add(
                'invoiceTo',
                ModelFilter::class,
                [
                    'label' => 'admin.label.invoice_to',
                    'admin_code' => 'app.admin.partner',
                    'field_type' => ModelAutocompleteType::class,
                    'field_options' => [
                            'property' => 'name',
                        ],
                ]
            )
            ->add(
                'vehicle',
                null,
                [
                    'label' => 'admin.label.vehicle',
                    'field_type' => EntityType::class,
                    'field_options' => [
                            'class' => Vehicle::class,
                            'query_builder' => $this->rm->getVehicleRepository()->getFilteredByEnterpriseEnabledSortedByNameQB($this->getUserLogedEnterprise()),
                        ],
                ]
            )
            ->add(
                'secondaryVehicle',
                null,
                [
                    'label' => 'admin.label.secondary_vehicle',
                    'field_type' => EntityType::class,
                    'field_options' => [
                            'class' => Vehicle::class,
                            'query_builder' => $this->rm->getVehicleRepository()->getFilteredByEnterpriseEnabledSortedByNameQB($this->getUserLogedEnterprise()),
                        ],
                ]
            )
            ->add(
                'operator',
                null,
                [
                    'label' => 'admin.label.operator',
                    'field_type' => EntityType::class,
                    'field_options' => [
                            'class' => Operator::class,
                            'query_builder' => $this->rm->getOperatorRepository()->getFilteredByEnterpriseEnabledSortedByNameQB($this->getUserLogedEnterprise()),
                        ],
                ]
            )
            ->add(
                'service',
                null,
                [
                    'label' => 'admin.label.sale_serivce_tariff',
                    'field_type' => EntityType::class,
                    'field_options' => [
                            'class' => SaleServiceTariff::class,
                            'query_builder' => $this->rm->getSaleServiceTariffRepository()->getEnabledSortedByNameQB(),
                        ],
                ]
            )
            ->add(
                'hourPrice',
                null,
                [
                    'label' => 'admin.label.price_hour',
                ]
            )
            ->add(
                'miniumHours',
                null,
                [
                    'label' => 'admin.label.minimum_hours',
                ]
            )
            ->add(
                'displacement',
                null,
                [
                    'label' => 'admin.label.displacement',
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
                'place',
                null,
                [
                    'label' => 'admin.label.place',
                ]
            )
            ->add(
                'observations',
                null,
                [
                    'label' => 'admin.label.comments',
                ]
            )
            ->add(
                'requestDate',
                DateRangeFilter::class,
                [
                    'label' => 'admin.label.sale_request_date',
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
                'serviceDate',
                DateRangeFilter::class,
                [
                    'label' => 'admin.label.service_date',
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
        ;
    }

    public function configureQuery(ProxyQueryInterface $query): ProxyQueryInterface
    {
        $queryBuilder = parent::configureQuery($query);
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
        $listMapper
            ->add(
                'id',
                null,
                [
                    'label' => 'Id',
                ]
            )
            ->add(
                'requestDate',
                null,
                [
                    'label' => 'admin.label.sale_request_date',
                    'format' => 'd/m/y',
                ]
            )
            ->add(
                'service',
                null,
                [
                    'label' => 'admin.label.tonnage',
                ]
            )
            ->add(
                'serviceDate',
                null,
                [
                    'label' => 'admin.label.service_date',
                    'format' => 'd/m/y',
                ]
            )
            ->add(
                'serviceTime',
                null,
                [
                    'label' => 'admin.label.service_time',
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
                'status',
                null,
                [
                    'label' => 'admin.label.status',
                    'header_class' => 'text-center',
                    'row_align' => 'center',
                    'template' => 'admin/cells/list__cell_sale_request_status.html.twig',
                    'editable' => false,
                ]
            )
            ->add(
                'onlyDeliveryNote',
                null,
                [
                    'template' => 'admin/cells/list__cell_sale_request_delivery_note.html.twig',
                    'label' => 'admin.with.delivery_note',
                ]
            )
            ->add(
                '_action',
                'actions',
                [
                    'actions' => [
                        'show' => ['template' => 'admin/buttons/list__action_show_button.html.twig'],
                        'edit' => ['template' => 'admin/buttons/list__action_edit_button.html.twig'],
//                        'pdf' => array('template' => 'admin/buttons/list__action_pdf_button.html.twig'),
                        'clone' => ['template' => 'admin/buttons/list__action_clone_button.html.twig'],
                        'generateDeliveryNoteFromSaleRequest' => ['template' => 'admin/buttons/list__action_generate_delivery_note_button.html.twig'],
                        'delete' => ['template' => 'admin/buttons/list__action_delete_sale_request_button.html.twig'],
                    ],
                    'label' => 'admin.actions',
                ]
            )
        ;
    }

    /**
     * @param SaleRequest $object
     *
     * @throws Exception
     */
    public function prePersist($object): void
    {
        $object->setAttendedBy($this->getUser());
        $object->setEnterprise($this->getUserLogedEnterprise());
        $object->setRequestTime(new DateTime());

        if (null == $object->getInvoiceTo()) {
            $object->setInvoiceTo($object->getPartner());
        }
    }

    /**
     * @param SaleRequest $object
     */
    public function preUpdate($object): void
    {
        $saleServiceTariff = $object->getService();
        $saleDeliveryNotes = $object->getSaleRequestHasDeliveryNotes();
        /** @var SaleRequestHasDeliveryNote $deliveryNote */
        foreach ($saleDeliveryNotes as $deliveryNote) {
            $deliveryNote->getSaleDeliveryNote()->setSaleServiceTariff($saleServiceTariff);
        }
    }
}
