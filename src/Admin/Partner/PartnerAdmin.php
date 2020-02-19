<?php

namespace App\Admin\Partner;

use App\Admin\AbstractBaseAdmin;
use App\Entity\Setting\City;
use App\Entity\Enterprise\EnterpriseTransferAccount;
use App\Entity\Partner\Partner;
use App\Entity\Partner\PartnerClass;
use App\Entity\Partner\PartnerType;
use Doctrine\ORM\QueryBuilder;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

/**
 * Class PartnerAdmin.
 *
 * @category Admin
 */
class PartnerAdmin extends AbstractBaseAdmin
{
    protected $classnameLabel = 'Tercers';
    protected $baseRoutePattern = 'tercers/tercer';
    protected $datagridValues = array(
        '_sort_by' => 'name',
        '_sort_order' => 'asc',
    );

    /**
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);
        $collection
            ->add('getJsonPartnerById', $this->getRouterIdParameter().'/get-json-partner-by-id')
            ->add('getPartnerContactsById', $this->getRouterIdParameter().'/get-partner-contacts-by-id')
            ->add('getJsonDeliveryNotesById', $this->getRouterIdParameter().'/get-json-delivery-notes-by-id')
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('General', $this->getFormMdSuccessBoxArray(4))
            ->add(
                'cifNif',
                null,
                array(
                    'label' => 'CIF/NIF',
                    'required' => true,
                )
            )
            ->add(
                'name',
                null,
                array(
                    'label' => 'Nom',
                )
            )
            ->add(
                'class',
                EntityType::class,
                array(
                    'class' => PartnerClass::class,
                    'label' => 'Classe',
                    'required' => true,
                    'query_builder' => $this->rm->getPartnerClassRepository()->getEnabledSortedByNameQB(),
                )
            )
            ->add(
                'type',
                EntityType::class,
                array(
                    'class' => PartnerType::class,
                    'label' => 'Tipus',
                    'required' => true,
                    'query_builder' => $this->rm->getPartnerTypeRepository()->getEnabledSortedByNameQB(),
                )
            )
            ->add(
                'transferAccount',
                EntityType::class,
                array(
                    'class' => EnterpriseTransferAccount::class,
                    'label' => 'Compte bancari empresa',
                    'required' => true,
                    'query_builder' => $this->rm->getEnterpriseTransferAccountRepository()->getFilteredByEnterpriseEnabledSortedByNameQB($this->getUserLogedEnterprise()),
                )
            )
            ->add(
                'notes',
                null,
                array(
                    'label' => 'Notes',
                    'attr' => array(
                        'style' => 'resize: vertical',
                        'rows' => 7,
                    ),
                )
            )
            ->end()
            ->with('Contacte', $this->getFormMdSuccessBoxArray(4))
            ->add(
                'mainAddress',
                null,
                array(
                    'label' => 'Adreça principal',
                    'required' => true,
                )
            )
            ->add(
                'mainCity',
                EntityType::class,
                array(
                    'class' => City::class,
                    'label' => 'Ciutat principal',
                    'required' => true,
                    'query_builder' => $this->rm->getCityRepository()->getCitiesSortedByNameQB(),
                )
            )
            ->add(
                'secondaryAddress',
                null,
                array(
                    'label' => 'Adreça secundària',
                )
            )
            ->add(
                'secondaryCity',
                EntityType::class,
                array(
                    'class' => City::class,
                    'label' => 'Ciutat secundària',
                    'required' => false,
                    'query_builder' => $this->rm->getCityRepository()->getCitiesSortedByNameQB(),
                )
            )
            ->add(
                'phoneNumber1',
                null,
                array(
                    'label' => 'Telèfon 1',
                )
            )
            ->add(
                'phoneNumber2',
                null,
                array(
                    'label' => 'Telèfon 2',
                )
            )
            ->add(
                'phoneNumber3',
                null,
                array(
                    'label' => 'Telèfon 3',
                )
            )
            ->add(
                'phoneNumber4',
                null,
                array(
                    'label' => 'Telèfon 4',
                )
            )
            ->add(
                'phoneNumber5',
                null,
                array(
                    'label' => 'Telèfon 5',
                )
            )
            ->add(
                'faxNumber1',
                null,
                array(
                    'label' => 'Fax 1',
                )
            )
            ->add(
                'faxNumber2',
                null,
                array(
                    'label' => 'Fax 2',
                )
            )
            ->add(
                'email',
                null,
                array(
                    'label' => 'Email',
                )
            )
            ->add(
                'www',
                null,
                array(
                    'label' => 'Pàgina web',
                )
            )
            ->end()
            ->with('Controls', $this->getFormMdSuccessBoxArray(4))
            ->add(
                'discount',
                null,
                array(
                    'label' => 'Descompte',
                )
            )
            ->add(
                'code',
                null,
                array(
                    'label' => 'Codi',
                )
            )
            ->add(
                'providerReference',
                null,
                array(
                    'label' => 'Referència proveïdor',
                )
            )
            ->add(
                'reference',
                null,
                array(
                    'label' => 'Referència',
                )
            )
            ->add(
                'ivaTaxFree',
                null,
                array(
                    'label' => 'Exent IVA',
                )
            )
            ->add(
                'iban',
                null,
                array(
                    'label' => 'IBAN',
                )
            )
            ->add(
                'swift',
                null,
                array(
                    'label' => 'SWIFT',
                )
            )
            ->add(
                'bankCode',
                null,
                array(
                    'label' => 'Codi bancari',
                )
            )
            ->add(
                'officeNumber',
                null,
                array(
                    'label' => 'Número oficina',
                )
            )
            ->add(
                'controlDigit',
                null,
                array(
                    'label' => 'Dígit control',
                )
            )
            ->add(
                'accountNumber',
                null,
                array(
                    'label' => 'Número compte',
                )
            )
            ->add(
                'enabled',
                CheckboxType::class,
                array(
                    'label' => 'Actiu',
                )
            )
            ->end()
        ;
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add(
                'cifNif',
                null,
                array(
                    'label' => 'CIF/NIF',
                )
            )
            ->add(
                'name',
                null,
                array(
                    'label' => 'Nom',
                )
            )
            ->add(
                'class',
                null,
                array(
                    'label' => 'Classe',
                )
            )
            ->add(
                'type',
                null,
                array(
                    'label' => 'Tipus',
                )
            )
            ->add(
                'enabled',
                null,
                array(
                    'label' => 'Actiu',
                )
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
            ->setParameter('enterprise', $this->getUserLogedEnterprise())
            ->orderBy('e.name', 'ASC')
            ->addOrderBy($queryBuilder->getRootAliases()[0].'.name', 'ASC')
        ;

        return $queryBuilder;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        unset($this->listModes['mosaic']);
        $listMapper
            ->add(
                'cifNif',
                null,
                array(
                    'label' => 'CIF/NIF',
                )
            )
            ->add(
                'name',
                null,
                array(
                    'label' => 'Nom',
                    'editable' => true,
                )
            )
            ->add(
                'class',
                null,
                array(
                    'label' => 'Classe',
                )
            )
            ->add(
                'type',
                null,
                array(
                    'label' => 'Tipus',
                )
            )
            ->add(
                'phoneNumber1',
                null,
                array(
                    'label' => 'Telèfon 1',
                    'editable' => true,
                )
            )
            ->add(
                'Email',
                null,
                array(
                    'label' => 'Email',
                    'editable' => true,
                )
            )
            ->add(
                'enabled',
                null,
                array(
                    'label' => 'Actiu',
                    'editable' => true,
                )
            )
            ->add(
                '_action',
                'actions',
                array(
                    'actions' => array(
                        'show' => array('template' => '::Admin/Buttons/list__action_show_button.html.twig'),
                        'edit' => array('template' => '::Admin/Buttons/list__action_edit_button.html.twig'),
                        'delete' => array('template' => '::Admin/Buttons/list__action_delete_button.html.twig'),
                    ),
                    'label' => 'admin.actions',
                )
            )
        ;
    }

    /**
     * @param Partner $object
     */
    public function prePersist($object)
    {
        $object->setEnterprise($this->getUserLogedEnterprise());
    }
}
