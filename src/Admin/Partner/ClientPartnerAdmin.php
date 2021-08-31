<?php

namespace App\Admin\Partner;

use App\Admin\AbstractBaseAdmin;
use App\Entity\Enterprise\EnterpriseTransferAccount;
use App\Entity\Partner\Partner;
use App\Entity\Partner\PartnerClass;
use App\Entity\Partner\PartnerType;
use App\Entity\Setting\City;
use Doctrine\ORM\QueryBuilder;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
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
    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);
        $collection
            ->add('getJsonPartnerById', $this->getRouterIdParameter().'/get-json-partner-by-id')
            ->add('getPartnerContactsById', $this->getRouterIdParameter().'/get-partner-contacts-by-id')
            ->add('getJsonDeliveryNotesById', $this->getRouterIdParameter().'/get-json-delivery-notes-by-id')
            ->add('getJsonBuildingSitesById', $this->getRouterIdParameter().'/get-json-building-sites-by-id')
        ;
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('General', $this->getFormMdSuccessBoxArray(4))
            ->add(
                'cifNif',
                null,
                [
                    'label' => 'CIF/NIF',
                    'required' => true,
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
                EntityType::class,
                [
                    'class' => PartnerClass::class,
                    'label' => 'Classe',
                    'required' => true,
                    'query_builder' => $this->rm->getPartnerClassRepository()->getEnabledSortedByNameQB(),
                ]
            )
            ->add(
                'type',
                EntityType::class,
                [
                    'class' => PartnerType::class,
                    'label' => 'Tipus',
                    'required' => true,
                    'query_builder' => $this->rm->getPartnerTypeRepository()->getEnabledSortedByNameQB(),
                    'data' => $this->rm->getPartnerTypeRepository()->find(1),
                    'disabled' => true,
                ]
            )
            ->add(
                'transferAccount',
                EntityType::class,
                [
                    'class' => EnterpriseTransferAccount::class,
                    'label' => 'Compte bancari empresa',
                    'required' => true,
                    'query_builder' => $this->rm->getEnterpriseTransferAccountRepository()->getFilteredByEnterpriseEnabledSortedByNameQB($this->getUserLogedEnterprise()),
                ]
            )
            ->add(
                'notes',
                null,
                [
                    'label' => 'Notes',
                    'attr' => [
                        'style' => 'resize: vertical',
                        'rows' => 7,
                    ],
                ]
            )
            ->end()
            ->with('Contacte', $this->getFormMdSuccessBoxArray(4))
            ->add(
                'mainAddress',
                null,
                [
                    'label' => 'Adreça principal',
                    'required' => true,
                ]
            )
            ->add(
                'mainCity',
                EntityType::class,
                [
                    'class' => City::class,
                    'label' => 'Ciutat principal',
                    'required' => true,
                    'query_builder' => $this->rm->getCityRepository()->getCitiesSortedByNameQB(),
                ]
            )
            ->add(
                'secondaryAddress',
                null,
                [
                    'label' => 'Adreça secundària',
                ]
            )
            ->add(
                'secondaryCity',
                EntityType::class,
                [
                    'class' => City::class,
                    'label' => 'Ciutat secundària',
                    'required' => false,
                    'query_builder' => $this->rm->getCityRepository()->getCitiesSortedByNameQB(),
                ]
            )
            ->add(
                'phoneNumber1',
                null,
                [
                    'label' => 'Telèfon 1',
                ]
            )
            ->add(
                'phoneNumber2',
                null,
                [
                    'label' => 'Telèfon 2',
                ]
            )
            ->add(
                'phoneNumber3',
                null,
                [
                    'label' => 'Telèfon 3',
                ]
            )
            ->add(
                'phoneNumber4',
                null,
                [
                    'label' => 'Telèfon 4',
                ]
            )
            ->add(
                'phoneNumber5',
                null,
                [
                    'label' => 'Telèfon 5',
                ]
            )
            ->add(
                'faxNumber1',
                null,
                [
                    'label' => 'Fax 1',
                ]
            )
            ->add(
                'faxNumber2',
                null,
                [
                    'label' => 'Fax 2',
                ]
            )
            ->add(
                'email',
                null,
                [
                    'label' => 'Email',
                ]
            )
            ->add(
                'www',
                null,
                [
                    'label' => 'Pàgina web',
                ]
            )
            ->end()
            ->with('Controls', $this->getFormMdSuccessBoxArray(4))
            ->add(
                'discount',
                null,
                [
                    'label' => 'Descompte',
                ]
            )
            ->add(
                'code',
                null,
                [
                    'label' => 'Codi',
                ]
            )
            ->add(
                'providerReference',
                null,
                [
                    'label' => 'Referència proveïdor',
                ]
            )
            ->add(
                'reference',
                null,
                [
                    'label' => 'Referència',
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
                    'label' => 'Codi bancari',
                ]
            )
            ->add(
                'officeNumber',
                null,
                [
                    'label' => 'Número oficina',
                ]
            )
            ->add(
                'controlDigit',
                null,
                [
                    'label' => 'Dígit control',
                ]
            )
            ->add(
                'accountNumber',
                null,
                [
                    'label' => 'Número compte',
                ]
            )
            ->add(
                'enabled',
                CheckboxType::class,
                [
                    'label' => 'Actiu',
                ]
            )
            ->end()
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
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
            ->join($queryBuilder->getRootAliases()[0].'.enterprise', 'e')
            ->andWhere($queryBuilder->getRootAliases()[0].'.enterprise = :enterprise')
            ->andWhere($queryBuilder->getRootAliases()[0].'.type = 1')
            ->setParameter('enterprise', $this->getUserLogedEnterprise())
            ->orderBy('e.name', 'ASC')
            ->addOrderBy($queryBuilder->getRootAliases()[0].'.name', 'ASC')
        ;

        return $queryBuilder;
    }

    protected function configureListFields(ListMapper $listMapper)
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
    public function prePersist($object)
    {
        $object->setEnterprise($this->getUserLogedEnterprise());
        /** @var PartnerType $partnerType */
        $partnerType = $this->rm->getPartnerTypeRepository()->find(1);
        $object->setType($partnerType);
    }
}
