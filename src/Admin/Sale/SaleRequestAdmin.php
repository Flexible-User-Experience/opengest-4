<?php

namespace App\Admin\Sale;

use App\Admin\AbstractBaseAdmin;
use App\Entity\Operator\Operator;
use App\Entity\Partner\PartnerBuildingSite;
use App\Entity\Sale\SaleRequest;
use App\Entity\Sale\SaleServiceTariff;
use App\Entity\Setting\User;
use App\Entity\Vehicle\Vehicle;
use App\Enum\SaleRequestStatusEnum;
use App\Enum\UserRolesEnum;
use DateTime;
use Doctrine\ORM\QueryBuilder;
use Exception;
use Sonata\AdminBundle\Admin\AbstractAdmin as Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\DoctrineORMAdminBundle\Filter\DateFilter;
use Sonata\DoctrineORMAdminBundle\Filter\ModelAutocompleteFilter;
use Sonata\Form\Type\DatePickerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
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
    protected $translationDomain = 'admin';

    /**
     * @var string
     */
    protected $classnameLabel = 'Petició';

    /**
     * @var string
     */
    protected $baseRoutePattern = 'vendes/peticio';

    /**
     * @var array
     */
    protected $datagridValues = [
        '_sort_by' => 'requestDate',
        '_sort_order' => 'desc',
    ];

    /**
     * Methods.
     */
    public function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->add('pdf', $this->getRouterIdParameter().'/pdf')
            ->add('clone', $this->getRouterIdParameter().'/clone')
            ->add('generateDeliveryNoteFromSaleRequest', $this->getRouterIdParameter().'/generateDeliveryNote')
            ->remove('show')
        ;
    }

    /**
     * @param array $actions
     *
     * @return array
     */
    public function configureBatchActions($actions)
    {
        if ($this->hasRoute('edit') && $this->hasAccess('edit')) {
//            $actions['generatepdfs'] = array(
//                'label' => 'Imprimir peticions marcades',
//                'translation_domain' => 'messages',
//                'ask_confirmation' => false,
//            );
            $actions['generatedeliverynotefromsalerequests'] = [
                'label' => 'Generar albarans de les peticions marcades',
                'translation_domain' => 'messages',
                'ask_confirmation' => false,
            ];
        }

        return $actions;
    }

    /**
     * @throws Exception
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('Petició', $this->getFormMdSuccessBoxArray(3))
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
                    'label' => 'Client',
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
                ]
            )
            ->add(
                'cifNif',
                TextType::class,
                [
                    'label' => 'CIF',
                    'required' => false,
                    'mapped' => false,
                    'disabled' => true,
                    'help' => '<i id="cif-nif-icon" class="fa fa-refresh fa-spin fa-fw hidden text-info"></i>',
                ]
            )
            ->add(
                'buildingSite',
                EntityType::class,
                [
                    'class' => PartnerBuildingSite::class,
                    'label' => 'Obra',
                    'required' => false,
                    'query_builder' => $this->rm->getPartnerBuildingSiteRepository()->getEnabledSortedByNameQB(),
                ]
            )
            ->add(
                'serviceDate',
                DatePickerType::class,
                [
                    'label' => 'Data servei',
                    'format' => 'd/M/y',
                    'required' => true,
                ]
            )
            ->add(
                'serviceTime',
                TimeType::class,
                [
                    'label' => 'Hora servei',
                    'required' => false,
                    'minutes' => [0, 15, 30, 45],
                ]
            )
            ->add(
                'endServiceTime',
                TimeType::class,
                [
                    'label' => 'Fi hora servei',
                    'required' => false,
                    'minutes' => [0, 15, 30, 45],
                ]
            )
//            ->add(
//                'mainAddress',
//                TextType::class,
//                array(
//                    'label' => 'Adreça principal',
//                    'required' => false,
//                    'mapped' => false,
//                    'disabled' => true,
//                    'help' => '<i id="main-address-icon" class="fa fa-refresh fa-spin fa-fw hidden text-info"></i>',
//                )
//            )
//            ->add(
//                'mainCity',
//                TextType::class,
//                array(
//                    'label' => 'Població',
//                    'required' => false,
//                    'mapped' => false,
//                    'disabled' => true,
//                    'help' => '<i id="main-city-icon" class="fa fa-refresh fa-spin fa-fw hidden text-info"></i>',
//                )
//            )
//            ->add(
//                'province',
//                TextType::class,
//                array(
//                    'label' => 'Província',
//                    'required' => false,
//                    'mapped' => false,
//                    'disabled' => true,
//                    'help' => '<i id="province-icon" class="fa fa-refresh fa-spin fa-fw hidden text-info"></i>',
//                )
//            )
//            ->add(
//                'paymentType',
//                TextType::class,
//                array(
//                    'label' => 'Forma de pagament',
//                    'required' => false,
//                    'mapped' => false,
//                    'disabled' => true,
//                )
//            )
            ->end()
            ->with('Servei', $this->getFormMdSuccessBoxArray(3))
            ->add(
                'service',
                EntityType::class,
                [
                    'class' => SaleServiceTariff::class,
                    'label' => 'admin.label.sale_serivce_tariff',
                    'required' => true,
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
                null,
                [
                    'label' => 'Descripció servei',
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
                    'label' => 'Lloc',
                    'required' => false,
                    'attr' => [
                        'style' => 'resize: vertical',
                        'rows' => 3,
                    ],
                ]
            )
            ->end()
            ->with('Tarifa', $this->getFormMdSuccessBoxArray(3))
            ->add(
                'selectTariff',
                TextType::class,
                [
                    'label' => 'Tarifes',
                    'required' => false,
                    'mapped' => false,
                    'disabled' => true,
                ]
            )
//            ->add(
//                'tariff',
//                EntityType::class,
//                array(
//                    'class' => SaleTariff::class,
//                    'label' => 'Tarifa',
//                    'required' => false,
//                    'query_builder' => $this->rm->getSaleTariffRepository()->getFilteredByEnterpriseEnabledSortedByNameQB($this->getUserLogedEnterprise()),
//                )
//            )
            ->add(
                'miniumHours',
                null,
                [
                    'label' => 'Mínim hores',
                    'required' => false,
                    'help' => '<i id="minium-hours-icon" class="fa fa-refresh fa-spin fa-fw hidden text-info"></i>',
                ]
            )
            ->add(
                'hourPrice',
                null,
                [
                    'label' => 'Preu hora',
                    'required' => false,
                    'help' => '<i id="hour-price-icon" class="fa fa-refresh fa-spin fa-fw hidden text-info"></i>',
                ]
            )
            ->add(
                'displacement',
                null,
                [
                    'label' => 'Desplaçament',
                    'required' => false,
                    'help' => '<i id="displacement-icon" class="fa fa-refresh fa-spin fa-fw hidden text-info"></i>',
                ]
            )
            ->add(
                'miniumHolidayHours',
                null,
                [
                    'label' => 'Minim hores festiu',
                    'required' => false,
                    'help' => '<i id="minium-holiday-hours-icon" class="fa fa-refresh fa-spin fa-fw hidden text-info"></i>',
                ]
            )
            ->add(
                'increaseForHolidays',
                null,
                [
                    'label' => 'Increment per festiu',
                    'required' => false,
                    'help' => '<i id="increase-for-holidays-icon" class="fa fa-refresh fa-spin fa-fw hidden text-info"></i>',
                ]
            )
            ->add(
                'increaseForHolidaysPercentage',
                PercentType::class,
                [
                    'label' => 'Increment per festiu %',
                    'required' => false,
                    'help' => '<i id="increase-for-holidays-percentage-icon" class="fa fa-refresh fa-spin fa-fw hidden text-info"></i>',
                ]
            )
            ->end()
            ->with('Contacte', $this->getFormMdSuccessBoxArray(3))
            ->add(
                'selectContactPersonName',
                TextType::class,
                [
                    'label' => 'Contactes del client',
                    'required' => false,
                    'mapped' => false,
                ]
            )
            ->add(
                'contactPersonName',
                TextType::class,
                [
                    'label' => 'Persona de contacte',
                    'required' => false,
                ]
            )
            ->add(
                'contactPersonPhone',
                TextType::class,
                [
                    'label' => 'Telèfon persona contacte',
                    'required' => false,
                ]
            )
            ->add(
                'invoiceTo',
                ModelAutocompleteType::class,
                [
                    'property' => 'name',
                    'label' => 'Facturar a',
                    'required' => false,
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
                ]
            )
            ->end()
            ->with('Altres', $this->getFormMdSuccessBoxArray(3))
            ->add(
                'requestDate',
                DatePickerType::class,
                [
                    'label' => 'Data petició',
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
                    'label' => 'Observacions',
                    'required' => false,
                    'attr' => [
                        'style' => 'resize: vertical',
                        'rows' => 2,
                    ],
                ]
            )
            ->end()
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        if ($this->acs->isGranted(UserRolesEnum::ROLE_ADMIN)) {
            $datagridMapper
                ->add(
                    'enterprise',
                    null,
                    [
                        'label' => 'Empresa',
                    ]
                )
            ;
        }
        $datagridMapper
            ->add(
                'attendedBy',
                null,
                [
                    'label' => 'Atès per',
                ]
            )
            ->add(
                'status',
                null,
                [
                    'label' => 'admin.label.status',
                ],
                ChoiceType::class,
                [
                    'choices' => SaleRequestStatusEnum::getEnumArray(),
                ]
            )
            ->add(
                'partner',
                ModelAutocompleteFilter::class,
                [
                    'label' => 'Client',
                ],
                null,
                [
                    'property' => 'name',
                ]
            )
            ->add(
                'invoiceTo',
                ModelAutocompleteFilter::class,
                [
                    'label' => 'Facturar a',
                ],
                null,
                [
                    'property' => 'name',
                ]
            )
            ->add(
                'vehicle',
                null,
                [
                    'label' => 'admin.label.vehicle',
                ],
                EntityType::class,
                [
                    'class' => Vehicle::class,
                    'query_builder' => $this->rm->getVehicleRepository()->getFilteredByEnterpriseEnabledSortedByNameQB($this->getUserLogedEnterprise()),
                ]
            )
            ->add(
                'secondaryVehicle',
                null,
                [
                    'label' => 'admin.label.secondary_vehicle',
                ],
                EntityType::class,
                [
                    'class' => Vehicle::class,
                    'query_builder' => $this->rm->getVehicleRepository()->getFilteredByEnterpriseEnabledSortedByNameQB($this->getUserLogedEnterprise()),
                ]
            )
            ->add(
                'operator',
                null,
                [
                    'label' => 'admin.label.operator',
                ],
                EntityType::class,
                [
                    'class' => Operator::class,
                    'query_builder' => $this->rm->getOperatorRepository()->getFilteredByEnterpriseEnabledSortedByNameQB($this->getUserLogedEnterprise()),
                ]
            )
            ->add(
                'service',
                null,
                [
                    'label' => 'admin.label.sale_serivce_tariff',
                ],
                EntityType::class,
                [
                    'class' => SaleServiceTariff::class,
                    'query_builder' => $this->rm->getSaleServiceTariffRepository()->getEnabledSortedByNameQB(),
                ]
            )
            ->add(
                'hourPrice',
                null,
                [
                    'label' => 'Preu hora',
                ]
            )
            ->add(
                'miniumHours',
                null,
                [
                    'label' => 'Mínim hores',
                ]
            )
            ->add(
                'displacement',
                null,
                [
                    'label' => 'Desplaçament',
                ]
            )
            ->add(
                'serviceDescription',
                null,
                [
                    'label' => 'Descripció servei',
                ]
            )
//            ->add(
//                'height',
//                null,
//                array(
//                    'label' => 'Alçada',
//                )
//            )
//            ->add(
//                'distance',
//                null,
//                array(
//                    'label' => 'Distància',
//                )
//            )
//            ->add(
//                'weight',
//                null,
//                array(
//                    'label' => 'Pes',
//                )
//            )
            ->add(
                'place',
                null,
                [
                    'label' => 'Lloc',
                ]
            )
//            ->add(
//                'utensils',
//                null,
//                array(
//                    'label' => 'Utensilis',
//                )
//            )
            ->add(
                'observations',
                null,
                [
                    'label' => 'Observacions',
                ]
            )
            ->add(
                'requestDate',
                DateFilter::class,
                [
                    'label' => 'Data petició',
                    'field_type' => DatePickerType::class,
                ]
            )
            ->add(
                'serviceDate',
                DateFilter::class,
                [
                    'label' => 'Data servei',
                    'field_type' => DatePickerType::class,
                ]
            )
        ;
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
        if (!$this->acs->isGranted(UserRolesEnum::ROLE_ADMIN)) {
            $queryBuilder
                ->andWhere($queryBuilder->getRootAliases()[0].'.enterprise = :enterprise')
                ->setParameter('enterprise', $this->getUserLogedEnterprise())
            ;
        }

        return $queryBuilder;
    }

    protected function configureListFields(ListMapper $listMapper)
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
            ->add(
                'onlyDeliveryNote',
                null,
                [
                    'template' => 'admin/cells/list__cell_sale_request_delivery_note.html.twig',
                    'label' => 'Albarán',
                ]
            )
            ->add(
                'requestDate',
                null,
                [
                    'label' => 'Data petició',
                    'format' => 'd/m/y',
                ]
            )
            ->add(
                'service',
                null,
                [
                    'label' => 'Tonatge',
                ]
            )
            ->add(
                'serviceDate',
                null,
                [
                    'label' => 'Data servei',
                    'format' => 'd/m/y',
                ]
            )
            ->add(
                'serviceTime',
                null,
                [
                    'label' => 'Hora servei',
                ]
            )
            ->add(
                'partner',
                null,
                [
                    'label' => 'Tercer',
                ]
            )
            ->add(
                'vehicle',
                null,
                [
                    'label' => 'Vehicle',
                ]
            )
            ->add(
                'operator',
                null,
                [
                    'label' => 'Operari',
                ]
            )
            ->add(
                'status',
                null,
                [
                    'label' => 'Estat',
                    'header_class' => 'text-center',
                    'row_align' => 'center',
                    'template' => 'admin/cells/list__cell_sale_request_status.html.twig',
                    'editable' => false,
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
                    'label' => 'Accions',
                ]
            )
        ;
    }

    /**
     * @param SaleRequest $object
     *
     * @throws Exception
     */
    public function prePersist($object)
    {
        $object->setAttendedBy($this->getUser());
        $object->setEnterprise($this->getUserLogedEnterprise());
        $object->setRequestTime(new DateTime());

        if (null == $object->getInvoiceTo()) {
            $object->setInvoiceTo($object->getPartner());
        }
    }
}
