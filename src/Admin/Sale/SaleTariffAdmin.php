<?php

namespace App\Admin\Sale;

use App\Admin\AbstractBaseAdmin;
use App\Entity\Partner\Partner;
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
use Sonata\AdminBundle\Form\Type\ChoiceFieldMaskType;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\DoctrineORMAdminBundle\Filter\DateFilter;
use Sonata\DoctrineORMAdminBundle\Filter\ModelAutocompleteFilter;
use Sonata\Form\Type\DatePickerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
    protected $datagridValues = array(
        '_sort_by' => 'year',
        '_sort_order' => 'DESC',
    );

    /**
     * Methods.
     */

    /**
     * @param RouteCollection $collection
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
     * @param FormMapper $formMapper
     *
     * @throws Exception
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $this->setTemplate('edit', "admin/sale-tariff/edit.html.twig" );
        $formMapper
            ->with('admin.with.general', $this->getFormMdSuccessBoxArray(3))
            ->add(
                'year',
                ChoiceType::class,
                array(
                    'label' => 'admin.label.year',
                    'choices' => $this->ycm->getYearRange(),
                    'placeholder' => 'Selecciona un any',
                    'required' => true,
                )
            )
            ->add(
                'date',
                DatePickerType::class,
                array(
                    'label' => 'admin.label.date',
                    'format' => 'd/M/y',
                    'required' => true,
                    'dp_default_date' => (new DateTime())->format('d/m/Y'),
                )
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
                array(
                    'class' => SaleServiceTariff::class,
                    'label' => 'admin.label.sale_serivce_tariff',
                    'required' => true,
                    'query_builder' => $this->rm->getSaleServiceTariffRepository()->getEnabledSortedByNameQB(),
                )
            )
            ->end()
            ->with('admin.label.partner', $this->getFormMdSuccessBoxArray(2))
            ->add(
                'partner',
                ModelAutocompleteType::class,
                array(
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
                )
            )
//            ->add(
//                'partnerBuildingSite',
//                EntityType::class,
//                array(
//                    'class' => PartnerBuildingSite::class,
//                    'label' => 'Obra',
//                    'required' => false,
//                    'query_builder' => $this->rm->getPartnerBuildingSiteRepository()->getEnabledSortedByNameQB(), //TODO only return those related to client
//                )
//            )
//            ->add(
//                'selectPartnerBuildingSite',
//                TextType::class,
//                array(
//                    'label' => 'Obres del client',
//                    'required' => false,
//                    'mapped' => false,
//                )
//            )
            ->add(
                'partnerBuildingSite',
                EntityType::class,
                array(
                    'label' => 'Obra',
                    'required' => false,
                    'class' => PartnerBuildingSite::class,
//                    'choices' => [],
                    'query_builder' => $this->rm->getPartnerBuildingSiteRepository()->getEnabledSortedByNameQB(), //TODO only return those related to client
//                    'attr' => [
//                        'data-sonata-select2' => 'false'
//                    ]
                )
            )
            ->end()
            ->with('admin.with.sale_tariff', $this->getFormMdSuccessBoxArray(2))
            ->add(
                'priceHour',
                null,
                array(
                    'label' => 'admin.label.price_hour',
                    'required' => false,
                )
            )
            ->add(
                'miniumHours',
                null,
                array(
                    'label' => 'admin.label.minimum_hours',
                    'required' => false,
                )
            )
            ->add(
                'miniumHolidayHours',
                null,
                array(
                    'label' => 'admin.label.minimum_holiday_hours',
                    'required' => false,
                )
            )
            ->add(
                'displacement',
                null,
                array(
                    'label' => 'admin.label.displacement',
                    'required' => false,
                )
            )
            ->add(
                'increaseForHolidays',
                null,
                array(
                    'label' => 'admin.label.increase_for_holidays',
                    'required' => false,
                )
            )
            ->add(
                'increaseForHolidaysPercentage',
                PercentType::class,
                array(
                    'label' => 'admin.label.increase_for_holidays_percentage',
                    'required' => false,
                )
            )
            ->end()
            ->with('admin.with.controls', $this->getFormMdSuccessBoxArray(2))
            ->add(
                'enabled',
                CheckboxType::class,
                array(
                    'label' => 'admin.label.enabled_female',
                    'required' => false,
                )
            )
            ->end()
        ;

//        $admin = $this;
//        $formMapper->getFormBuilder()->addEventListener(FormEvents::PRE_SUBMIT,
//            function (FormEvent $event) use ($formMapper, $admin) {
//            });
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add(
                'year',
                null,
                array(
                    'label' => 'admin.label.year',
                )
            )
            ->add(
                'date',
                DateFilter::class,
                array(
                    'label' => 'Data',
                    'field_type' => DatePickerType::class,
                )
            )
            ->add(
                'partner',
                ModelAutocompleteFilter::class,
                array(
                    'label' => 'admin.label.partner',
                ),
                null,
                array(
                    'property' => 'name',
                )
            )
            ->add(
                'partnerBuildingSite',
                ModelAutocompleteFilter::class,
                array(
                    'label' => 'admin.label.partner_building_site',
                ),
                null,
                array(
                    'property' => 'name',
                )
            )
            ->add(
                'saleServiceTariff',
                null,
                array(
                    'label' => 'admin.label.sale_serivce_tariff'
                ),
                EntityType::class,
                array(
                    'class' => SaleServiceTariff::class,
                    'query_builder' => $this->rm->getSaleServiceTariffRepository()->getEnabledSortedByNameQB(),
                )
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
                array(
                    'label' => 'admin.label.price_hour',
                )
            )
            ->add(
                'miniumHours',
                null,
                array(
                    'label' => 'admin.label.minimum_hours',
                )
            )
            ->add(
                'miniumHolidayHours',
                null,
                array(
                    'label' => 'admin.label.minimum_holiday_hours',
                )
            )
            ->add(
                'displacement',
                null,
                array(
                    'label' => 'admin.label.displacement',
                )
            )
            ->add(
                'increaseForHolidays',
                null,
                array(
                    'label' => 'admin.label.increase_for_holidays',
                )
            )
            ->add(
                'enabled',
                null,
                array(
                    'label' => 'admin.label.enabled_female',
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
            ->addOrderBy($queryBuilder->getRootAliases()[0].'.year', 'DESC')
            ->addOrderBy($queryBuilder->getRootAliases()[0].'.tonnage', 'DESC')
        ;

        return $queryBuilder;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add(
                'year',
                null,
                array(
                    'label' => 'admin.label.year',
                    'editable' => true,
                )
            )
            ->add(
                'date',
                null,
                array(
                    'label' => 'Data',
                    'format' => 'd/m/Y',
                )
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
                array(
                    'label' => 'admin.label.sale_serivce_tariff',
                    'sortable' => true
                )
            )
            ->add(
                'partner',
                null,
                array(
                    'label' => 'admin.label.partner',
                    'sortable' => true
                )
            )
            ->add(
                'partnerBuildingSite',
                null,
                array(
                    'label' => 'admin.label.partner_building_site',
                    'sortable' => true
                )
            )
            ->add(
                'priceHour',
                'string',
                array(
                    'label' => 'admin.label.price_hour',
                    'editable' => true,
                )
            )
            ->add(
                'miniumHours',
                'string',
                array(
                    'label' => 'admin.label.minimum_hours',
                    'editable' => true,
                )
            )
            ->add(
                'miniumHolidayHours',
                'string',
                array(
                    'label' => 'admin.label.minimum_holiday_hours',
                    'editable' => true,
                )
            )
            ->add(
                'displacement',
                'string',
                array(
                    'label' => 'admin.label.displacement',
                    'editable' => true,
                )
            )
            ->add(
                'increaseForHolidays',
                'string',
                array(
                    'label' => 'admin.label.increase_for_holidays',
                    'editable' => true,
                )
            )
            ->add(
                'increaseForHolidaysPercentage',
                PercentType::class,
                array(
                    'label' => 'admin.label.increase_for_holidays_percentage',
                    'editable' => true, //todo view as percentage, not as unitary
                )
            )
            ->add(
                'enabled',
                null,
                array(
                    'label' => 'admin.label.enabled_female',
                    'editable' => true,
                )
            )
            ->add(
                '_action',
                'actions',
                array(
                    'actions' => array(
                        'show' => array('template' => 'admin/buttons/list__action_show_button.html.twig'),
                        'edit' => array('template' => 'admin/buttons/list__action_edit_button.html.twig'),
                        'delete' => array('template' => 'admin/buttons/list__action_delete_button.html.twig'),
                    ),
                    'label' => 'admin.actions',
                )
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
