<?php

namespace App\Admin\Sale;

use App\Admin\AbstractBaseAdmin;
use App\Entity\Sale\SaleTariff;
use Doctrine\ORM\QueryBuilder;
use Exception;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

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
        $formMapper
            ->with('admin.with.general', $this->getFormMdSuccessBoxArray(4))
            ->add(
                'year',
                ChoiceType::class,
                array(
                    'label' => 'admin.label.year',
                    'choices' => $this->getConfigurationPool()->getContainer()->get('app.year_choices_manager')->getYearRange(),
                    'placeholder' => 'Selecciona un any',
                    'required' => true,
                )
            )
            ->add(
                'tonnage',
                null,
                array(
                    'label' => 'admin.label.tonnage',
                    'required' => true,
                )
            )
            ->end()
            ->with('admin.with.sale_tariff', $this->getFormMdSuccessBoxArray(4))
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
                'tonnage',
                null,
                array(
                    'label' => 'admin.label.tonnage',
                )
            )
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
                'tonnage',
                null,
                array(
                    'label' => 'admin.label.tonnage',
                    'editable' => true,
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
     * @param SaleTariff $object
     */
    public function prePersist($object)
    {
        $object->setEnterprise($this->getUserLogedEnterprise());
    }
}
