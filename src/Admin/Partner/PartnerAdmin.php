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
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

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
    protected $baseRoutePattern = 'tercers/tercer';

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
                    'label' => 'Nombre',
                ]
            )
            ->add(
                'class',
                EntityType::class,
                [
                    'class' => PartnerClass::class,
                    'label' => 'Clase',
                    'required' => true,
                    'query_builder' => $this->rm->getPartnerClassRepository()->getEnabledSortedByNameQB(),
                ]
            )
            ->add(
                'type',
                EntityType::class,
                [
                    'class' => PartnerType::class,
                    'label' => 'Tipo',
                    'required' => true,
                    'query_builder' => $this->rm->getPartnerTypeRepository()->getEnabledSortedByNameQB(),
                ]
            )
            ->add(
                'transferAccount',
                EntityType::class,
                [
                    'class' => EnterpriseTransferAccount::class,
                    'label' => 'Cuenta bancaria',
                    'required' => true,
                    'query_builder' => $this->rm->getEnterpriseTransferAccountRepository()->getFilteredByEnterpriseEnabledSortedByNameQB($this->getUserLogedEnterprise()),
                ]
            )
            ->add(
                'notes',
                null,
                [
                    'label' => 'Notas',
                    'attr' => [
                        'style' => 'resize: vertical',
                        'rows' => 7,
                    ],
                ]
            )
            ->end()
            ->with('Contacto', $this->getFormMdSuccessBoxArray(4))
            ->add(
                'mainAddress',
                null,
                [
                    'label' => 'Dirección principal',
                    'required' => true,
                ]
            )
            ->add(
                'mainCity',
                EntityType::class,
                [
                    'class' => City::class,
                    'label' => 'Ciudad principal',
                    'required' => true,
                    'query_builder' => $this->rm->getCityRepository()->getCitiesSortedByNameQB(),
                ]
            )
            ->add(
                'secondaryAddress',
                null,
                [
                    'label' => 'Dirección secundària',
                ]
            )
            ->add(
                'secondaryCity',
                EntityType::class,
                [
                    'class' => City::class,
                    'label' => 'Ciudad secundària',
                    'required' => false,
                    'query_builder' => $this->rm->getCityRepository()->getCitiesSortedByNameQB(),
                ]
            )
            ->add(
                'phoneNumber1',
                null,
                [
                    'label' => 'Teléfono 1',
                ]
            )
            ->add(
                'phoneNumber2',
                null,
                [
                    'label' => 'Teléfono 2',
                ]
            )
            ->add(
                'phoneNumber3',
                null,
                [
                    'label' => 'Teléfono 3',
                ]
            )
            ->add(
                'phoneNumber4',
                null,
                [
                    'label' => 'Teléfono 4',
                ]
            )
            ->add(
                'phoneNumber5',
                null,
                [
                    'label' => 'Teléfono 5',
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
                    'label' => 'Página web',
                ]
            )
            ->end()
            ->with('Controls', $this->getFormMdSuccessBoxArray(4))
            ->add(
                'discount',
                null,
                [
                    'label' => 'Descuento',
                ]
            )
            ->add(
                'code',
                null,
                [
                    'label' => 'Código',
                ]
            )
            ->add(
                'providerReference',
                null,
                [
                    'label' => 'Referencia proveedor',
                ]
            )
            ->add(
                'reference',
                null,
                [
                    'label' => 'Referencia',
                ]
            )
            ->add(
                'ivaTaxFree',
                null,
                [
                    'label' => 'Exento IVA',
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
                    'label' => 'Código bancario',
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
                    'label' => 'Dígito control',
                ]
            )
            ->add(
                'accountNumber',
                null,
                [
                    'label' => 'Número cuenta',
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
                    'label' => 'Nombre',
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
    }
}
