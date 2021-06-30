<?php

namespace App\Admin\Sale;

use App\Admin\AbstractBaseAdmin;
use App\Entity\Operator\Operator;
use App\Entity\Partner\PartnerBuildingSite;
use App\Entity\Sale\SaleRequest;
use App\Entity\Sale\SaleServiceTariff;
use App\Entity\Sale\SaleTariff;
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
    protected $datagridValues = array(
        '_sort_by' => 'requestDate',
        '_sort_order' => 'desc',
    );

    /**
     * Methods.
     */

    /**
     * @param RouteCollection $collection
     */
    public function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->add('pdf', $this->getRouterIdParameter().'/pdf')
            ->add('clone', $this->getRouterIdParameter().'/clone')
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
            $actions['generatepdfs'] = array(
                'label' => 'Imprimir peticions marcades',
                'translation_domain' => 'messages',
                'ask_confirmation' => false,
            );
        }

        return $actions;
    }

    /**
     * @param FormMapper $formMapper
     *
     * @throws Exception
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('Petició', $this->getFormMdSuccessBoxArray(3))
            ->add(
                'status',
                ChoiceType::class,
                array(
                    'choices' => SaleRequestStatusEnum::getEnumArray(),
                    'label' => 'admin.label.status',
                )
            )
            ->add(
                'partner',
                ModelAutocompleteType::class,
                array(
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
                )
            )
            ->add(
                'cifNif',
                TextType::class,
                array(
                    'label' => 'CIF',
                    'required' => false,
                    'mapped' => false,
                    'disabled' => true,
                    'help' => '<i id="cif-nif-icon" class="fa fa-refresh fa-spin fa-fw hidden text-info"></i>',
                )
            )
            ->add(
                'buildingSite',
                EntityType::class,
                array(
                    'class' => PartnerBuildingSite::class,
                    'label' => 'Obra',
                    'required' => false,
                    'query_builder' => $this->rm->getPartnerBuildingSiteRepository()->getEnabledSortedByNameQB(),
                )

            )
            ->add(
                'serviceDate',
                DatePickerType::class,
                array(
                    'label' => 'Data servei',
                    'format' => 'd/M/y',
                    'required' => true,
                )
            )
            ->add(
                'serviceTime',
                TimeType::class,
                array(
                    'label' => 'Hora servei',
                    'required' => false,
                    'minutes' => array(0, 15, 30, 45),
                )
            )
            ->add(
                'endServiceTime',
                TimeType::class,
                array(
                    'label' => 'Fi hora servei',
                    'required' => false,
                    'minutes' => array(0, 15, 30, 45),
                )
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
                array(
                    'class' => SaleServiceTariff::class,
                    'label' => 'Servei',
                    'required' => true,
                    'query_builder' => $this->rm->getSaleServiceTariffRepository()->getEnabledSortedByNameQB(),
                )
            )
            ->add(
                'vehicle',
                EntityType::class,
                array(
                    'class' => Vehicle::class,
                    'label' => 'Vehicle',
                    'required' => false,
                    'query_builder' => $this->rm->getVehicleRepository()->getFilteredByEnterpriseEnabledSortedByNameQB($this->getUserLogedEnterprise()),
                )
            )
            ->add(
                'secondaryVehicle',
                EntityType::class,
                array(
                    'class' => Vehicle::class,
                    'label' => 'Vehicle secundari',
                    'required' => false,
                    'query_builder' => $this->rm->getVehicleRepository()->getFilteredByEnterpriseEnabledSortedByNameQB($this->getUserLogedEnterprise()),
                )
            )
            ->add(
                'operator',
                EntityType::class,
                array(
                    'class' => Operator::class,
                    'label' => 'Operari',
                    'required' => false,
                    'query_builder' => $this->rm->getOperatorRepository()->getFilteredByEnterpriseEnabledSortedByNameQB($this->getUserLogedEnterprise()),
                )
            )
            ->add(
                'serviceDescription',
                null,
                array(
                    'label' => 'Descripció servei',
                    'required' => true,
                    'attr' => array(
                        'style' => 'resize: vertical',
                        'rows' => 7,
                    ),
                )
            )
            ->add(
                'place',
                null,
                array(
                    'label' => 'Lloc',
                    'required' => false,
                    'attr' => array(
                        'style' => 'resize: vertical',
                        'rows' => 3,
                    ),
                )
            )
            ->end()
            ->with('Tarifa', $this->getFormMdSuccessBoxArray(3))
            ->add(
                'selectTariff',
                TextType::class,
                array(
                    'label' => 'Tarifes',
                    'required' => false,
                    'mapped' => false,
                    'disabled' => true,
                )
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
                array(
                    'label' => 'Mínim hores',
                    'required' => false,
                    'help' => '<i id="minium-hours-icon" class="fa fa-refresh fa-spin fa-fw hidden text-info"></i>',
                )
            )
            ->add(
                'hourPrice',
                null,
                array(
                    'label' => 'Preu hora',
                    'required' => false,
                    'help' => '<i id="hour-price-icon" class="fa fa-refresh fa-spin fa-fw hidden text-info"></i>',
                )
            )
            ->add(
                'displacement',
                null,
                array(
                    'label' => 'Desplaçament',
                    'required' => false,
                    'help' => '<i id="displacement-icon" class="fa fa-refresh fa-spin fa-fw hidden text-info"></i>',
                )
            )
            ->end()
            ->with('Contacte', $this->getFormMdSuccessBoxArray(3))
            ->add(
                'selectContactPersonName',
                TextType::class,
                array(
                    'label' => 'Contactes del client',
                    'required' => false,
                    'mapped' => false,
                )
            )
            ->add(
                'contactPersonName',
                TextType::class,
                array(
                    'label' => 'Persona de contacte',
                    'required' => false,
                )
            )
            ->add(
                'contactPersonPhone',
                TextType::class,
                array(
                    'label' => 'Telèfon persona contacte',
                    'required' => false,
                )
            )
            ->add(
                'invoiceTo',
                ModelAutocompleteType::class,
                array(
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
                )
            )
            ->end()
            ->with('Altres', $this->getFormMdSuccessBoxArray(3))
            ->add(
                'requestDate',
                DatePickerType::class,
                array(
                    'label' => 'Data petició',
                    'format' => 'd/M/y',
                    'required' => false,
                    'dp_default_date' => (new DateTime())->format('d/m/Y'),
                )
            )
            ->add(
                'attendedBy',
                EntityType::class,
                array(
                    'label' => 'admin.label.attended_by',
                    'required' => false,
                    'class' => User::class,
                    'disabled' => true,
                    'data' => $this->getUser()
                )
            )
            ->add(
                'observations',
                null,
                array(
                    'label' => 'Observacions',
                    'required' => false,
                    'attr' => array(
                        'style' => 'resize: vertical',
                        'rows' => 2,
                    ),
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
        if ($this->acs->isGranted(UserRolesEnum::ROLE_ADMIN)) {
            $datagridMapper
                ->add(
                    'enterprise',
                    null,
                    array(
                        'label' => 'Empresa',
                    )
                )
            ;
        }
        $datagridMapper
            ->add(
                'attendedBy',
                null,
                array(
                    'label' => 'Atès per',
                )
            )
            ->add(
                'status',
                null,
                array(
                    'label' => 'admin.label.status',
                ),
                ChoiceType::class,
                array(
                    'choices' => SaleRequestStatusEnum::getEnumArray(),
                )
            )
            ->add(
                'partner',
                ModelAutocompleteFilter::class,
                array(
                    'label' => 'Client',
                ),
                null,
                array(
                    'property' => 'name',
                )
            )
            ->add(
                'invoiceTo',
                ModelAutocompleteFilter::class,
                array(
                    'label' => 'Facturar a',
                ),
                null,
                array(
                    'property' => 'name',
                )
            )
            ->add(
                'vehicle',
                null,
                array(),
                EntityType::class,
                array(
                    'class' => Vehicle::class,
                    'label' => 'Vehicle',
                    'query_builder' => $this->rm->getVehicleRepository()->getFilteredByEnterpriseEnabledSortedByNameQB($this->getUserLogedEnterprise()),
                )
            )
            ->add(
                'secondaryVehicle',
                null,
                array(),
                EntityType::class,
                array(
                    'class' => Vehicle::class,
                    'label' => 'Vehicle secundari',
                    'query_builder' => $this->rm->getVehicleRepository()->getFilteredByEnterpriseEnabledSortedByNameQB($this->getUserLogedEnterprise()),
                )
            )
            ->add(
                'operator',
                null,
                array(),
                EntityType::class,
                array(
                    'class' => Operator::class,
                    'label' => 'Operari',
                    'query_builder' => $this->rm->getOperatorRepository()->getFilteredByEnterpriseEnabledSortedByNameQB($this->getUserLogedEnterprise()),
                )
            )
            ->add(
                'tariff',
                null,
                array(),
                EntityType::class,
                array(
                    'class' => SaleTariff::class,
                    'label' => 'Tarifa',
                    'query_builder' => $this->rm->getSaleTariffRepository()->getFilteredByEnterpriseEnabledSortedByNameQB($this->getUserLogedEnterprise()),
                )
            )
            ->add(
                'hourPrice',
                null,
                array(
                    'label' => 'Preu hora',
                )
            )
            ->add(
                'miniumHours',
                null,
                array(
                    'label' => 'Mínim hores',
                )
            )
            ->add(
                'displacement',
                null,
                array(
                    'label' => 'Desplaçament',
                )
            )
            ->add(
                'serviceDescription',
                null,
                array(
                    'label' => 'Descripció servei',
                )
            )
            ->add(
                'height',
                null,
                array(
                    'label' => 'Alçada',
                )
            )
            ->add(
                'distance',
                null,
                array(
                    'label' => 'Distància',
                )
            )
            ->add(
                'weight',
                null,
                array(
                    'label' => 'Pes',
                )
            )
            ->add(
                'place',
                null,
                array(
                    'label' => 'Lloc',
                )
            )
            ->add(
                'utensils',
                null,
                array(
                    'label' => 'Utensilis',
                )
            )
            ->add(
                'observations',
                null,
                array(
                    'label' => 'Observacions',
                )
            )
            ->add(
                'requestDate',
                DateFilter::class,
                array(
                    'label' => 'Data petició',
                    'field_type' => DatePickerType::class,
                )
            )
            ->add(
                'serviceDate',
                DateFilter::class,
                array(
                    'label' => 'Data servei',
                    'field_type' => DatePickerType::class,
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
        if (!$this->acs->isGranted(UserRolesEnum::ROLE_ADMIN)) {
            $queryBuilder
                ->andWhere($queryBuilder->getRootAliases()[0].'.enterprise = :enterprise')
                ->setParameter('enterprise', $this->getUserLogedEnterprise())
            ;
        }

        return $queryBuilder;
    }

    /**
     * @param ListMapper $listMapper
     */
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
                'requestDate',
                null,
                array(
                    'label' => 'Data petició',
                    'format' => 'd/m/y',
                )
            )
            ->add(
                'serviceDate',
                null,
                array(
                    'label' => 'Data servei',
                    'format' => 'd/m/y',
                )
            )
            ->add(
                'serviceTime',
                null,
                array(
                    'label' => 'Hora servei',
                )
            )
            ->add(
                'vehicle',
                null,
                array(
                    'label' => 'Vehicle',
                )
            )
            ->add(
                'service',
                null,
                array(
                    'label' => 'Servei',
                )
            )
            ->add(
                'operator',
                null,
                array(
                    'label' => 'Operari',
                )
            )
            ->add(
                'partner',
                null,
                array(
                    'label' => 'Tercer',
                )
            )
            ->add(
                'status',
                null,
                array(
                    'label' => 'Estat',
                    'header_class' => 'text-center',
                    'row_align' => 'center',
                    'template' => 'admin/cells/list__cell_sale_request_status.html.twig',
                    'editable' => false,
                )
            )
            ->add(
                '_action',
                'actions',
                array(
                    'actions' => array(
                        'show' => array('template' => 'admin/buttons/list__action_show_button.html.twig'),
                        'edit' => array('template' => 'admin/buttons/list__action_edit_button.html.twig'),
                        'pdf' => array('template' => 'admin/buttons/list__action_pdf_button.html.twig'),
                        'clone' => array('template' => 'admin/buttons/list__action_clone_button.html.twig'),
                        'delete' => array('template' => 'admin/buttons/list__action_delete_button.html.twig'),
                    ),
                    'label' => 'Accions',
                )
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
