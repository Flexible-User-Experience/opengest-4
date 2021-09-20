<?php

namespace App\Admin\Sale;

use App\Admin\AbstractBaseAdmin;
use App\Entity\Partner\PartnerBuildingSite;
use App\Entity\Sale\SaleServiceTariff;
use App\Entity\Sale\SaleTariff;
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
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * Class SaleTariffAdmin.
 *
 * @category    Admin
 */
class SaleTariffAdmin extends AbstractBaseAdmin
{
    /**
     * @var string
     */
    protected $translationDomain = 'admin';

    /**
     * @var string
     */
    protected $classnameLabel = 'Tarifa';

    /**
     * @var string
     */
    protected $baseRoutePattern = 'vendes/tarifa';

    /**
     * @var array
     */
    protected $datagridValues = [
        '_sort_by' => 'year',
        '_sort_order' => 'DESC',
    ];

    /**
     * Methods.
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);
        $collection
            ->add('getJsonSaleTariffById', $this->getRouterIdParameter().'/get-json-sale-tariff-by-id')
            ->remove('delete')
        ;
    }

    /**
     * @throws Exception
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $this->setTemplate('edit', 'admin/sale-tariff/edit.html.twig');
        $formMapper
            ->with('admin.with.general', $this->getFormMdSuccessBoxArray(3))
            ->add(
                'year',
                ChoiceType::class,
                [
                    'label' => 'admin.label.year',
                    'choices' => $this->ycm->getYearRange(),
                    'placeholder' => 'Selecciona un any',
                    'required' => true,
                ]
            )
            ->add(
                'date',
                DatePickerType::class,
                [
                    'label' => 'admin.label.date',
                    'format' => 'd/M/y',
                    'required' => true,
                    'dp_default_date' => (new DateTime())->format('d/m/Y'),
                ]
            )
//            ->add(
//                'tonnage',
//                null,
//                array(
//                    'label' => 'admin.label.tonnage',
//                    'required' => true,
//                )
//            )
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
            ->with('admin.label.partner', $this->getFormMdSuccessBoxArray(2))
            ->add(
                'partner',
                ModelAutocompleteType::class,
                [
                    'property' => 'name',
                    'label' => 'admin.label.partner',
                    'required' => false,
                    'callback' => function ($admin, $property, $value) {
                        /** @var Admin $admin */
                        $datagrid = $admin->getDatagrid();
                        /** @var QueryBuilder $queryBuilder */
                        $queryBuilder = $datagrid->getQuery();
                        $queryBuilder
                            ->andWhere($queryBuilder->getRootAliases()[0].'.enterprise = :enterprise')
                            ->setParameter('enterprise', $this->getUserLogedEnterprise())
                            ->andWhere($queryBuilder->getRootAliases()[0].'.type = :partnerType')
                            ->setParameter('partnerType', 1)
                        ;
                        $datagrid->setValue($property, null, $value);
                    },
                ],
                [
                    'admin_code' => 'app.admin.partner',
                ]
            )
            ->add(
                'partnerBuildingSite',
                EntityType::class,
                [
                    'label' => 'admin.label.partner_building_site',
                    'required' => false,
                    'class' => PartnerBuildingSite::class,
                    'query_builder' => $this->rm->getPartnerBuildingSiteRepository()->getEnabledSortedByNameQB(),
                ]
            )
            ->end()
            ->with('admin.with.sale_tariff', $this->getFormMdSuccessBoxArray(2))
            ->add(
                'priceHour',
                null,
                [
                    'label' => 'admin.label.price_hour',
                    'required' => false,
                ]
            )
            ->add(
                'miniumHours',
                null,
                [
                    'label' => 'admin.label.minimum_hours',
                    'required' => false,
                ]
            )
            ->add(
                'miniumHolidayHours',
                null,
                [
                    'label' => 'admin.label.minimum_holiday_hours',
                    'required' => false,
                ]
            )
            ->add(
                'displacement',
                null,
                [
                    'label' => 'admin.label.displacement',
                    'required' => false,
                ]
            )
            ->add(
                'increaseForHolidays',
                null,
                [
                    'label' => 'admin.label.increase_for_holidays',
                    'required' => false,
                ]
            )
            ->add(
                'increaseForHolidaysPercentage',
                PercentType::class,
                [
                    'label' => 'admin.label.increase_for_holidays_percentage',
                    'required' => false,
                ]
            )
            ->end()
            ->with('admin.with.controls', $this->getFormMdSuccessBoxArray(2))
            ->add(
                'enabled',
                CheckboxType::class,
                [
                    'label' => 'admin.label.enabled_female',
                    'required' => false,
                ]
            )
            ->end()
        ;

//        $admin = $this;
//        $formMapper->getFormBuilder()->addEventListener(FormEvents::PRE_SUBMIT,
//            function (FormEvent $event) use ($formMapper, $admin) {
//            });
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add(
                'year',
                null,
                [
                    'label' => 'admin.label.year',
                ]
            )
            ->add(
                'date',
                DateFilter::class,
                [
                    'label' => 'admin.label.date',
                    'field_type' => DatePickerType::class,
                ]
            )
            ->add(
                'partner',
                ModelAutocompleteFilter::class,
                [
                    'label' => 'admin.label.partner',
                    'admin_code' => 'partner_admin',
                ],
                null,
                [
                    'property' => 'name',
                ]
            )
            ->add(
                'partnerBuildingSite',
                ModelAutocompleteFilter::class,
                [
                    'label' => 'admin.label.partner_building_site',
                ],
                null,
                [
                    'property' => 'name',
                ]
            )
            ->add(
                'saleServiceTariff',
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
//            ->add(
//                'tonnage',
//                null,
//                array(
//                    'label' => 'admin.label.tonnage',
//                )
//            )
            ->add(
                'priceHour',
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
                'miniumHolidayHours',
                null,
                [
                    'label' => 'admin.label.minimum_holiday_hours',
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
                'increaseForHolidays',
                null,
                [
                    'label' => 'admin.label.increase_for_holidays',
                ]
            )
            ->add(
                'enabled',
                null,
                [
                    'label' => 'admin.label.enabled_female',
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
            ->setParameter('enterprise', $this->getUserLogedEnterprise())
            ->orderBy('e.name', 'ASC')
            ->addOrderBy($queryBuilder->getRootAliases()[0].'.year', 'DESC')
            ->addOrderBy($queryBuilder->getRootAliases()[0].'.tonnage', 'DESC')
        ;

        return $queryBuilder;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add(
                'year',
                null,
                [
                    'label' => 'admin.label.year',
                    'editable' => true,
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
//            ->add(
//                'tonnage',
//                null,
//                array(
//                    'label' => 'admin.label.tonnage',
//                    'editable' => true,
//                )
//            )
            ->add(
                'saleServiceTariff',
                null,
                [
                    'label' => 'admin.label.sale_serivce_tariff',
                    'sortable' => true,
                ]
            )
            ->add(
                'partner',
                null,
                [
                    'label' => 'admin.label.partner',
                    'admin_code' => 'partner_admin',
                    'sortable' => true,
                ]
            )
            ->add(
                'partnerBuildingSite',
                null,
                [
                    'label' => 'admin.label.partner_building_site',
                    'sortable' => true,
                ]
            )
            ->add(
                'priceHour',
                'string',
                [
                    'label' => 'admin.label.price_hour',
                    'editable' => true,
                ]
            )
            ->add(
                'miniumHours',
                'string',
                [
                    'label' => 'admin.label.minimum_hours',
                    'editable' => true,
                ]
            )
            ->add(
                'miniumHolidayHours',
                'string',
                [
                    'label' => 'admin.label.minimum_holiday_hours',
                    'editable' => true,
                ]
            )
            ->add(
                'displacement',
                'string',
                [
                    'label' => 'admin.label.displacement',
                    'editable' => true,
                ]
            )
            ->add(
                'increaseForHolidays',
                'string',
                [
                    'label' => 'admin.label.increase_for_holidays',
                    'editable' => true,
                ]
            )
            ->add(
                'increaseForHolidaysPercentage',
                PercentType::class,
                [
                    'label' => 'admin.label.increase_for_holidays_percentage',
                    'editable' => true, //todo view as percentage, not as unitary
                ]
            )
            ->add(
                'enabled',
                null,
                [
                    'label' => 'admin.label.enabled_female',
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
     * @param SaleTariff $object
     */
    public function prePersist($object)
    {
        $object->setEnterprise($this->getUserLogedEnterprise());
    }
}
