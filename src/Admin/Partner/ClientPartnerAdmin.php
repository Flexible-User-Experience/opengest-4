<?php

namespace App\Admin\Partner;

use App\Admin\AbstractBaseAdmin;
use App\Entity\Enterprise\EnterpriseTransferAccount;
use App\Entity\Partner\Partner;
use App\Entity\Partner\PartnerClass;
use App\Entity\Partner\PartnerType;
use App\Entity\Setting\City;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\Form\Type\CollectionType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

/**
 * Class PartnerAdmin, filtered only by clients.
 *
 * @category Admin
 */
class ClientPartnerAdmin extends AbstractBaseAdmin
{
    /**
     * @var string
     */
    protected $classnameLabel = 'Clientes';

    /**
     * @var string
     */
    protected $baseRoutePattern = 'clientes/cliente';

    /**
     * @var string
     */
    protected $baseRouteName = 'admin_app_partner_client';

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
    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        parent::configureRoutes($collection);
        $collection
            ->add('getJsonPartnerById', $this->getRouterIdParameter().'/get-json-partner-by-id')
            ->add('getPartnerContactsById', $this->getRouterIdParameter().'/get-partner-contacts-by-id')
            ->add('getJsonDeliveryNotesById', $this->getRouterIdParameter().'/get-json-delivery-notes-by-id')
            ->add('getJsonBuildingSitesById', $this->getRouterIdParameter().'/get-json-building-sites-by-id')
        ;
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->tab('General')
            ->with('General', $this->getFormMdSuccessBoxArray(4))
            ->add(
                'code',
                null,
                [
                    'label' => 'admin.label.code',
                ]
            )
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
//            ->add(
//                'type',
//                EntityType::class,
//                [
//                    'class' => PartnerType::class,
//                    'label' => 'Tipus',
//                    'required' => true,
//                    'query_builder' => $this->rm->getPartnerTypeRepository()->getEnabledSortedByNameQB(),
//                    'data' => $this->rm->getPartnerTypeRepository()->find(1),
//                    'disabled' => true,
//                ]
//            )
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
            ->add(
                'faxNumber1',
                null,
                [
                    'label' => 'admin.label.fax1',
                ]
            )
            ->end()
            ->with('Otros datos', $this->getFormMdSuccessBoxArray(4))
            ->add(
                'reference',
                null,
                [
                    'label' => 'admin.label.reference',
                ]
            )
            ->add(
                'secondaryAddress',
                null,
                [
                    'label' => 'admin.label.secondary_address',
                ]
            )
            ->add(
                'secondaryCity',
                EntityType::class,
                [
                    'class' => City::class,
                    'label' => 'admin.label.secondary_city',
                    'required' => false,
                    'query_builder' => $this->rm->getCityRepository()->getCitiesSortedByNameQB(),
                ]
            )
            ->add(
                'phoneNumber3',
                null,
                [
                    'label' => 'admin.label.phone3',
                ]
            )
//            ->add(
//                'phoneNumber4',
//                null,
//                [
//                    'label' => 'admin.label.phone2',
//                ]
//            )
//            ->add(
//                'phoneNumber5',
//                null,
//                [
//                    'label' => 'admin.label.phone2',
//                ]
//            )
            ->add(
                'faxNumber2',
                null,
                [
                    'label' => 'admin.label.fax2',
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
                'www',
                null,
                [
                    'label' => 'admin.label.web_page',
                ]
            )
            ->end()
            ->with('Datos contables', $this->getFormMdSuccessBoxArray(4))
            ->add(
                'discount',
                null,
                [
                    'label' => 'admin.label.discount',
                ]
            )
            ->end()
            ->with('Datos bancarios', $this->getFormMdSuccessBoxArray(4))
            ->add(
                'transferAccount',
                EntityType::class,
                [
                    'class' => EnterpriseTransferAccount::class,
                    'label' => 'admin.label.transfer_account',
                    'required' => true,
                    'query_builder' => $this->rm->getEnterpriseTransferAccountRepository()->getFilteredByEnterpriseEnabledSortedByNameQB($this->getUserLogedEnterprise()),
                ]
            )
//            ->add(
//                'providerReference',
//                null,
//                [
//                    'label' => 'Referència proveïdor',
//                ]
//            )
//            ->add(
//                'ivaTaxFree',
//                null,
//                [
//                    'label' => 'Exent IVA',
//                ]
//            )
            ->add(
                'iban',
                null,
                [
                    'label' => 'IBAN',
                ]
            )
            ->add(
                'swift',
                null,
                [
                    'label' => 'SWIFT',
                ]
            )
            ->add(
                'bankCode',
                null,
                [
                    'label' => 'admin.label.bank_code',
                ]
            )
            ->add(
                'officeNumber',
                null,
                [
                    'label' => 'admin.label.office_number',
                ]
            )
            ->add(
                'controlDigit',
                null,
                [
                    'label' => 'admin.label.control_digit',
                ]
            )
            ->add(
                'accountNumber',
                null,
                [
                    'label' => 'admin.label.account_number',
                ]
            )
            ->end()
            ->with('Controles', $this->getFormMdSuccessBoxArray(4))
            ->add(
                'enabled',
                CheckboxType::class,
                [
                    'label' => 'admin.label.enabled',
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
            ->tab('Obras y pedidos')
            ->with('Obras', $this->getFormMdSuccessBoxArray(12))
            ->add(
                'buildingSites',
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
            ->with('Pedidos', $this->getFormMdSuccessBoxArray(6))
            ->add(
                'orders',
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
            ->tab('Dias inhábiles')
            ->with('Dias inhábiles', $this->getFormMdSuccessBoxArray(6))
            ->add(
                'partnerUnableDays',
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
        $datagridMapper
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
                    'label' => 'Nom',
                ]
            )
            ->add(
                'class',
                null,
                [
                    'label' => 'Classe',
                ]
            )
            ->add(
                'enabled',
                null,
                [
                    'label' => 'Actiu',
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
            ->andWhere($queryBuilder->getRootAliases()[0].'.type = 1')
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
                    'label' => 'Nom',
                    'editable' => true,
                ]
            )
            ->add(
                'class',
                null,
                [
                    'label' => 'Classe',
                ]
            )
            ->add(
                'phoneNumber1',
                null,
                [
                    'label' => 'Telèfon 1',
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
                    'label' => 'Actiu',
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
                        'delete' => ['template' => 'admin/buttons/list__action_delete_button.html.twig'],
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
        /** @var PartnerType $partnerType */
        $partnerType = $this->rm->getPartnerTypeRepository()->find(1);
        $object->setType($partnerType);
    }
}
