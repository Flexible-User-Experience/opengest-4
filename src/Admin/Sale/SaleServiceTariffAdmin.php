<?php

namespace App\Admin\Sale;

use App\Admin\AbstractBaseAdmin;
use App\Entity\Enterprise\ActivityLine;
use App\Entity\Sale\SaleTariff;
use Doctrine\ORM\QueryBuilder;
use Exception;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * Class SaleServiceTariffAdmin.
 *
 * @category    Admin
 */
class SaleServiceTariffAdmin extends AbstractBaseAdmin
{
    /**
     * @var string
     */
    protected $translationDomain = 'admin';

    /**
     * @var string
     */
    protected $classnameLabel = 'Serveis Tarifa';

    /**
     * @var string
     */
    protected $baseRoutePattern = 'vendes/serveis_tarifa';

    /**
     * @var array
     */
    protected $datagridValues = array(
        '_sort_by' => 'description',
        '_sort_order' => 'ASC',
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
                'description',
                null,
                array(
                    'label' => 'Descripció',
                )
            )
            ->add(
                'activityLine',
                EntityType::class,
                array(
                    'class' => ActivityLine::class,
                    'label' => 'admin.label.activity_line',
                    'required' => false,
                    'query_builder' => $this->rm->getActivityLineRepository()->getEnabledSortedByNameQB(),
                )
            )
            ->end()
            ->with('Controls', $this->getFormMdSuccessBoxArray(6))
            ->add(
                'enabled',
                CheckboxType::class,
                array(
                    'label' => 'Actiu',
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
                'description',
                null,
                array(
                    'label' => 'admin.label.description',
                )
            )
            ->add(
                'activityLine',
                null,
                array(
                    'label' => 'admin.label.activity_line',
                    )
                ,
                EntityType::class,
                array(
                    'class' => ActivityLine::class,
                    'query_builder' => $this->rm->getActivityLineRepository()->getEnabledSortedByNameQB()
                )
            )
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add(
                'description',
                null,
                array(
                    'label' => 'admin.label.description',
                    'editable' => true,
                )
            )
            ->add(
                'activityLine',
                EntityType::class,
                array(
                    'class' => ActivityLine::class,
                    'label' => 'admin.label.activity_line',
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
                        'show' => array('template' => 'admin/buttons/list__action_show_button.html.twig'),
                        'edit' => array('template' => 'admin/buttons/list__action_edit_button.html.twig'),
                    ),
                    'label' => 'admin.actions',
                )
            )
        ;
    }
}
