<?php

namespace App\Admin\Partner;

use App\Admin\AbstractBaseAdmin;
use App\Entity\Enterprise\CollectionDocumentType;
use App\Entity\Enterprise\EnterpriseTransferAccount;
use App\Entity\Partner\Partner;
use App\Entity\Partner\PartnerClass;
use App\Entity\Partner\PartnerType;
use App\Entity\Setting\City;
use App\Entity\Setting\Province;
use App\Enum\IvaEnum;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\Form\Type\CollectionType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * Class PartnerAdmin.
 *
 * @category Admin
 */
class PartnerAdmin extends AbstractBaseAdmin
{
    /**
     * @var string
     */
    protected $classnameLabel = 'Tercers';

    /**
     * @var string
     */
    protected $baseRouteName = 'admin_app_partner_partner';

    /**
     * @var array
     */
    protected $datagridValues = [
        '_sort_by' => 'name',
        '_sort_order' => 'asc',
    ];

    /**
     * Methods.
     */
    public function generateBaseRoutePattern(bool $isChildAdmin = false): string
    {
        return 'tercers/tercer';
    }

    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        parent::configureRoutes($collection);
        $collection
            ->add('getJsonPartnerById', $this->getRouterIdParameter().'/get-json-partner-by-id')
            ->add('getPartnerContactsById', $this->getRouterIdParameter().'/get-partner-contacts-by-id')
            ->add('getJsonDeliveryNotesById', $this->getRouterIdParameter().'/get-json-delivery-notes-by-id')
            ->add('getJsonBuildingSitesById', $this->getRouterIdParameter().'/get-json-building-sites-by-id')
            ->add('getJsonOrdersById', $this->getRouterIdParameter().'/get-json-orders-by-id')
            ->add('getJsonProjectsById', $this->getRouterIdParameter().'/get-json-projects-by-id')
        ;
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->tab('General')
            ->with('General', $this->getFormMdSuccessBoxArray(4))
        ;
        if ($this->id($this->getSubject())) {
            $formMapper
                ->add(
                    'code',
                    null,
                    [
                        'label' => 'admin.label.code',
                        //                    'disabled' => true,
                    ]
                );
        }
        $formMapper
            ->add(
                'reference',
                null,
                [
                    'label' => 'admin.label.commercial_name',
                    'required' => false,
                ]
            )
        ;
        if ($this->id($this->getSubject())) {
            $formMapper
                ->add(
                    'type',
                    EntityType::class,
                    [
                        'class' => PartnerType::class,
                        'label' => 'admin.label.type',
                        'required' => true,
                        'query_builder' => $this->rm->getPartnerTypeRepository()->getEnabledSortedByNameQB(),
                    ]
                );
        } else {
            $formMapper
                ->add(
                    'type',
                    EntityType::class,
                    [
                        'class' => PartnerType::class,
                        'label' => 'admin.label.type',
                        'required' => true,
                        'query_builder' => $this->rm->getPartnerTypeRepository()->getEnabledSortedByNameQB(),
                        'data' => $this->rm->getPartnerTypeRepository()->findOneBy(['id' => 2]),
                    ]
                );
        }
        if ($this->id($this->getSubject())) {
            $formMapper
                ->add(
                    'class',
                    EntityType::class,
                    [
                        'class' => PartnerClass::class,
                        'label' => 'admin.label.class',
                        'required' => true,
                        'query_builder' => $this->rm->getPartnerClassRepository()->getEnabledSortedByNameQB(),
                    ]
                )
            ;
        } else {
            $formMapper
                ->add(
                    'class',
                    EntityType::class,
                    [
                        'class' => PartnerClass::class,
                        'label' => 'admin.label.class',
                        'required' => true,
                        'query_builder' => $this->rm->getPartnerClassRepository()->getEnabledSortedByNameQB(),
                        'data' => $this->rm->getPartnerClassRepository()->findOneBy(['id' => 1]),
                    ]
                )
            ;
        }
        $formMapper
            ->add(
                'notes',
                null,
                [
                    'label' => 'admin.label.notes',
                    'attr' => [
                        'style' => 'resize: vertical',
                        'rows' => 7,
                    ],
                ]
            )
            ->add(
                'enabled',
                CheckboxType::class,
                [
                    'label' => 'admin.label.enabled',
                    'required' => false,
                ]
            )
            ->add(
                'blocked',
                CheckboxType::class,
                [
                    'label' => 'admin.label.blocked',
                    'required' => false,
                ]
            )
            ->end()
            ->with('Datos fiscales', $this->getFormMdSuccessBoxArray(4))
            ->add(
                'name',
                null,
                [
                    'label' => 'admin.label.name',
                ]
            )
            ->add(
                'cifNif',
                null,
                [
                    'label' => 'CIF/NIF',
                    'required' => true,
                ]
            )
            ->add(
                'mainAddress',
                null,
                [
                    'label' => 'admin.label.main_address',
                    'required' => true,
                ]
            )
            ->add(
                'mainCity',
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
            $formMapper
                ->add(
                    'mainCity.province.countryName',
                    null,
                    [
                        'label' => 'admin.label.country_name',
                        'required' => false,
                        'disabled' => true,
                    ]
                )
                ->add(
                    'mainCity.province',
                    EntityType::class,
                    [
                        'class' => Province::class,
                        'label' => 'admin.label.province',
                        'required' => false,
                        'disabled' => true,
                    ]
                )
            ;
        }
        $formMapper
            ->add(
                'phoneNumber1',
                null,
                [
                    'label' => 'admin.label.phone1',
                ]
            )
            ->add(
                'phoneNumber2',
                null,
                [
                    'label' => 'admin.label.phone2',
                ]
            )
            ->end()
            ->with('Otros datos', $this->getFormMdSuccessBoxArray(4))
            ->add(
                'providerReference',
                null,
                [
                    'label' => 'admin.label.reference',
                ]
            )
            ->add(
                'phoneNumber3',
                null,
                [
                    'label' => 'admin.label.phone3',
                ]
            )
            ->add(
                'email',
                null,
                [
                    'label' => 'admin.label.email',
                ]
            )
            ->add(
                'invoiceEmail',
                null,
                [
                    'label' => 'admin.label.invoice_email',
                ]
            )
            ->add(
                'www',
                null,
                [
                    'label' => 'admin.label.web_page',
                ]
            )
            ->add(
                'defaultIva',
                ChoiceType::class,
                [
                    'label' => 'admin.label.default_iva',
                    'choices' => IvaEnum::getReversedEnumArray(),
                    'placeholder' => '--- seleccione una opción ---',
                    'required' => false,
                ]
            )
            ->add(
                'defaultIrpf',
                null,

                [
                    'label' => 'admin.label.default_irpf',
                ]
            )
            ->end()
            ->end()
            ->tab('Pagos y datos contables')
            ->with('Datos de pago', $this->getFormMdSuccessBoxArray(4))
            ->add(
                'collectionDocumentType',
                EntityType::class,
                [
                    'label' => 'admin.label.collection_document_type',
                    'class' => CollectionDocumentType::class,
                    'required' => false,
                    'query_builder' => $this->rm->getCollectionDocumentTypeRepository()->getFilteredByEnterpriseEnabledSortedByNameQB($this->getUserLogedEnterprise()),
                ]
            )
            ->add(
                'transferAccount',
                EntityType::class,
                [
                    'class' => EnterpriseTransferAccount::class,
                    'label' => 'admin.label.enterprise_main_transfer_account',
                    'placeholder' => '---selecciona una opción---',
                    'required' => false,
                    'query_builder' => $this->rm->getEnterpriseTransferAccountRepository()->getFilteredByEnterpriseEnabledSortedByNameQB($this->getUserLogedEnterprise()),
                ]
            )
            ->add(
                'ivaTaxFree',
                null,
                [
                    'label' => 'Exent IVA',
                ]
            )
            ->add(
                'iban',
                null,
                [
                    'label' => 'IBAN Tercero',
                ]
            )
            ->add(
                'swift',
                null,
                [
                    'label' => 'SWIFT Tercero',
                ]
            )
            ->end()
            ->with('Detalles de pago', $this->getFormMdSuccessBoxArray(4))
            ->add(
                'collectionTerm1',
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
            ->add(
                'payDay1',
                null,
                [
                    'label' => 'admin.label.pay_day_1',
                    'required' => false,
                ]
            )
            ->add(
                'payDay2',
                null,
                [
                    'label' => 'admin.label.pay_day_2',
                    'required' => false,
                ]
            )
            ->add(
                'payDay3',
                null,
                [
                    'label' => 'admin.label.pay_day_3',
                    'required' => false,
                ]
            )
            ->end()
            ->with('Datos contables', $this->getFormMdSuccessBoxArray(4))
            ->add(
                'accountingAccount',
                null,
                [
                    'label' => 'admin.label.accounting_account',
                    'required' => false,
                    'attr' => ['pattern' => '[0-9]{10}', 'maxlength' => 10],
                ]
            )
            ->add(
                'costAccountingAccount',
                null,
                [
                    'label' => 'admin.label.cost_accounting_account',
                    'required' => false,
                ]
            )
            ->add(
                'invoiceCopiesNumber',
                null,
                [
                    'label' => 'admin.label.invoice_copies_number',
                    'required' => false,
                ]
            )
            ->end()
            ->end()
            ->tab('Contactos')
            ->with('Contactos', $this->getFormMdSuccessBoxArray(12))
            ->add(
                'contacts',
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
            ->tab('Direcciones de envío')
            ->with('Direcciones de envío', $this->getFormMdSuccessBoxArray(6))
            ->add(
                'partnerDeliveryAddresses',
                CollectionType::class,
                [
                    'required' => false,
//                    'error_bubbling' => true,
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
        $datagridMapper
            ->add(
                'code',
                null,
                [
                    'label' => 'admin.label.code',
                    'show_filter' => true,
                ]
            )
            ->add(
                'cifNif',
                null,
                [
                    'label' => 'CIF/NIF',
                ]
            )
            ->add(
                'name',
                null,
                [
                    'label' => 'Nombre',
                    'show_filter' => true,
                ]
            )
            ->add(
                'class',
                null,
                [
                    'label' => 'Clase',
                ]
            )
            ->add(
                'type',
                null,
                [
                    'label' => 'Tipo',
                ]
            )
            ->add(
                'enabled',
                null,
                [
                    'label' => 'Activo',
                ]
            )
        ;
    }

    public function configureQuery(ProxyQueryInterface $query): ProxyQueryInterface
    {
        $queryBuilder = parent::configureQuery($query);
        $queryBuilder
            ->join($queryBuilder->getRootAliases()[0].'.enterprise', 'e')
            ->andWhere($queryBuilder->getRootAliases()[0].'.enterprise = :enterprise')
            ->setParameter('enterprise', $this->getUserLogedEnterprise())
            ->orderBy('e.name', 'ASC')
            ->addOrderBy($queryBuilder->getRootAliases()[0].'.name', 'ASC')
        ;

        return $queryBuilder;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add(
                'code',
                null,
                [
                    'label' => 'admin.label.code',
                ]
            )
            ->add(
                'cifNif',
                null,
                [
                    'label' => 'CIF/NIF',
                ]
            )
            ->add(
                'name',
                null,
                [
                    'label' => 'Nombre',
                    'editable' => true,
                ]
            )
            ->add(
                'class',
                null,
                [
                    'label' => 'Clase',
                ]
            )
            ->add(
                'type',
                null,
                [
                    'label' => 'Tipo',
                ]
            )
            ->add(
                'phoneNumber1',
                null,
                [
                    'label' => 'Teléfono 1',
                    'editable' => true,
                ]
            )
            ->add(
                'Email',
                null,
                [
                    'label' => 'Email',
                    'editable' => true,
                ]
            )
            ->add(
                'enabled',
                null,
                [
                    'label' => 'Activo',
                    'editable' => true,
                ]
            )
            ->add(
                '_action',
                'actions',
                [
                    'actions' => [
                        'show' => ['template' => 'admin/buttons/list__action_show_button.html.twig'],
                        'edit' => ['template' => 'admin/buttons/list__action_edit_button.html.twig'],
//                        'delete' => ['template' => 'admin/buttons/list__action_delete_button.html.twig'],
                    ],
                    'label' => 'admin.actions',
                ]
            )
        ;
    }

    /**
     * @param Partner $object
     */
    public function prePersist($object): void
    {
        $object->setEnterprise($this->getUserLogedEnterprise());
        $partnerType = $object->getType();
        $lastPartnerCode = $this->rm->getPartnerRepository()->getLastPartnerIdByEnterpriseAndType($this->getUserLogedEnterprise(), $partnerType)->getCode();
        $object->setCode($lastPartnerCode + 1);
    }
}
