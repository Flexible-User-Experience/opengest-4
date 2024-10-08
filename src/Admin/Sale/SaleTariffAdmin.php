<?php

namespace App\Admin\Sale;

use App\Admin\AbstractBaseAdmin;
use App\Entity\Partner\PartnerBuildingSite;
use App\Entity\Sale\SaleServiceTariff;
use App\Entity\Sale\SaleTariff;
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
use Sonata\DoctrineORMAdminBundle\Filter\DateFilter;
use Sonata\DoctrineORMAdminBundle\Filter\ModelFilter;
use Sonata\Form\Type\DatePickerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;

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
    protected $classnameLabel = 'Tarifa';

    /**
     * Methods.
     */
    public function generateBaseRoutePattern(bool $isChildAdmin = false): string
    {
        return 'vendes/tarifa';
    }

    protected function configureDefaultSortValues(array &$sortValues): void
    {
        $sortValues[DatagridInterface::SORT_ORDER] = 'DESC';
        $sortValues[DatagridInterface::SORT_BY] = 'year';
    }

    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        parent::configureRoutes($collection);
        $collection
            ->add('getJsonSaleTariffById', $this->getRouterIdParameter().'/get-json-sale-tariff-by-id')
            ->remove('delete')
        ;
    }

    public function configureExportFields(): array
    {
        return [
            'year',
            'date',
            'saleServiceTariff',
            'partner.code',
            'partner.name',
            'partnerBuildingSite',
            'priceHour',
            'miniumHours',
            'miniumHolidayHours',
            'displacement',
            'increaseForHolidays',
            'increaseForHolidaysPercentage',
            'enabled',
        ];
    }

    /**
     * @throws Exception
     */
    protected function configureFormFields(FormMapper $formMapper): void
    {
        $this->setTemplate('edit', 'admin/sale-tariff/edit.html.twig');
        $formMapper
            ->with('admin.with.general', $this->getFormMdSuccessBoxArray(2))
            ->add(
                'year',
                ChoiceType::class,
                [
                    'label' => 'admin.label.year',
                    'choices' => $this->ycm->getYearRange(),
                    'placeholder' => 'Selecciona un año',
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
                    'placeholder' => '---seleccione una opción---',
                    'query_builder' => $this->rm->getSaleServiceTariffRepository()->getEnabledSortedByNameQB(),
                ]
            )
            ->end()
            ->with('admin.label.partner', $this->getFormMdSuccessBoxArray(4))
            ->add(
                'partner',
                ModelAutocompleteType::class,
                [
                    'property' => 'name',
                    'label' => 'admin.label.partner',
                    'required' => false,
                    'callback' => $this->partnerModelAutocompleteCallback(),
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
                null,
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
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
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
            ->add(
                'partnerBuildingSite',
                ModelFilter::class,
                [
                    'label' => 'admin.label.partner_building_site',
                    'field_type' => ModelAutocompleteType::class,
                    'field_options' => [
                            'property' => 'name',
                        ],
                ]
            )
            ->add(
                'saleServiceTariff',
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

    public function configureQuery(ProxyQueryInterface $query): ProxyQueryInterface
    {
        $queryBuilder = parent::configureQuery($query);
        $queryBuilder
            ->join($queryBuilder->getRootAliases()[0].'.enterprise', 'e')
            ->andWhere($queryBuilder->getRootAliases()[0].'.enterprise = :enterprise')
            ->setParameter('enterprise', $this->getUserLogedEnterprise())
            ->orderBy($queryBuilder->getRootAliases()[0].'.year', 'DESC')
            ->addOrderBy($queryBuilder->getRootAliases()[0].'.tonnage', 'DESC')
        ;

        return $queryBuilder;
    }

    protected function configureListFields(ListMapper $listMapper): void
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
                    'associated_property' => 'description',
                    'sortable' => true,
                ]
            )
            ->add(
                'partner',
                null,
                [
                    'label' => 'admin.label.partner',
                    'admin_code' => 'app.admin.partner',
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
    public function prePersist($object): void
    {
        $object->setEnterprise($this->getUserLogedEnterprise());
    }
}
