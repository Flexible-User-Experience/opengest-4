<?php

namespace App\Admin\Sale;

use App\Admin\AbstractBaseAdmin;
use App\Entity\Enterprise\ActivityLine;
use Exception;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

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
    protected $classnameLabel = 'admin.label.tonnages';

    /**
     * @var string
     */
    protected $baseRoutePattern = 'vendes/serveis_tarifa';

    /**
     * @var array
     */
    protected $datagridValues = [
        '_sort_by' => 'description',
        '_sort_order' => 'ASC',
    ];

    /**
     * Methods.
     */
    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        parent::configureRoutes($collection);
        $collection
            ->add('getJsonSaleTariffById', $this->getRouterIdParameter().'/get-json-sale-tariff-by-id'.'/{partnerId}')
            ->remove('delete')
        ;
    }

    /**
     * @throws Exception
     */
    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->with('admin.with.general', $this->getFormMdSuccessBoxArray(4))
            ->add(
                'description',
                null,
                [
                    'label' => 'admin.label.description',
                ]
            )
            ->add(
                'activityLine',
                EntityType::class,
                [
                    'class' => ActivityLine::class,
                    'label' => 'admin.label.activity_line',
                    'required' => false,
                    'query_builder' => $this->rm->getActivityLineRepository()->getEnabledSortedByNameQB(),
                ]
            )
            ->end()
            ->with('admin.with.controls', $this->getFormMdSuccessBoxArray(6))
            ->add(
                'enabled',
                CheckboxType::class,
                [
                    'label' => 'admin.label.enabled_male',
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
                'description',
                null,
                [
                    'label' => 'admin.label.description',
                ]
            )
            ->add(
                'activityLine',
                null,
                [
                    'label' => 'admin.label.activity_line',
                    ],
                EntityType::class,
                [
                    'class' => ActivityLine::class,
                    'query_builder' => $this->rm->getActivityLineRepository()->getEnabledSortedByNameQB(),
                ]
            )
        ;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add(
                'description',
                null,
                [
                    'label' => 'admin.label.description',
                    'editable' => true,
                ]
            )
            ->add(
                'activityLine',
                EntityType::class,
                [
                    'class' => ActivityLine::class,
                    'label' => 'admin.label.activity_line',
                ]
            )
            ->add(
                'enabled',
                null,
                [
                    'label' => 'admin.label.enabled_male',
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
                    ],
                    'label' => 'admin.actions',
                ]
            )
        ;
    }
}
