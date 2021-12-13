<?php

namespace App\Admin\Setting;

use App\Admin\AbstractBaseAdmin;
use App\Enum\TimeRangeTypeEnum;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;

/**
 * Class TimeRangeAdmin.
 *
 * @category Admin
 *
 * @author  Jordi Sort <jordi.sort@mirmit.com>
 */
class TimeRangeAdmin extends AbstractBaseAdmin
{
    /**
     * @var string
     */
    protected $classnameLabel = 'Franjas horarias';

    /**
     * @var string
     */
    protected $baseRoutePattern = 'configuracion/franjas_horarias';

    /**
     * @var array
     */
    protected $datagridValues = [
        '_sort_by' => 'start',
        '_sort_order' => 'asc',
    ];

    /**
     * Methods.
     */

    /**
     * Configure route collection.
     *
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        parent::configureRoutes($collection);
        $collection->remove('delete');
        $collection->remove('edit');
        $collection->remove('create');
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->with('General', $this->getFormMdSuccessBoxArray(6))
            ->add(
                'type',
                ChoiceType::class,
                [
                    'choices' => TimeRangeTypeEnum::getEnumArray(),
                    'label' => 'admin.label.type',
                ]
            )
            ->add(
                'description',
                null,
                [
                    'label' => 'Descripción',
                    'required' => true,
                ]
            )
            ->add(
                'start',
                TimeType::class,
                [
                    'label' => 'admin.label.start',
                    'required' => true,
                ]
            )
            ->add(
                'finish',
                TimeType::class,
                [
                    'label' => 'admin.label.finish',
                    'required' => true,
                ]
            )
            ->end()
            ->with('Controls', $this->getFormMdSuccessBoxArray(6))
            ->add(
                'enabled',
                CheckboxType::class,
                [
                    'label' => 'Activo',
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
                'type',
                null,
                [
                    'label' => 'admin.label.type',
                ],
                ChoiceType::class,
                [
                    'choices' => TimeRangeTypeEnum::getEnumArray(),
                ]
            )
            ->add(
                'description',
                null,
                [
                    'label' => 'Descripción',
                ]
            )
            ->add(
                'start',
                null,
                [
                    'label' => 'Empieza',
                ]
            )
            ->add(
                'finish',
                null,
                [
                    'label' => 'Termina',
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

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add(
                'type',
                null,
                [
                    'label' => 'admin.label.type',
                    'header_class' => 'text-center',
                    'row_align' => 'center',
                    'template' => 'admin/cells/list__cell_time_range_type.html.twig',
                    'editable' => false,
                ]
            )
            ->add(
                'description',
                null,
                [
                    'label' => 'Descripción',
                    'editable' => true,
                ]
            )
            ->add(
                'start',
                null,
                [
                    'label' => 'Empieza',
                    'editable' => true,
                ]
            )
            ->add(
                'finish',
                null,
                [
                    'label' => 'Termina',
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
//            ->add(
//                '_action',
//                'actions',
//                array(
//                    'actions' => array(
//                        'show' => array('template' => 'admin/buttons/list__action_show_button.html.twig'),
//                        'edit' => array('template' => 'admin/buttons/list__action_edit_button.html.twig'),
//                    ),
//                    'label' => 'Accions',
//                )
//            )
        ;
    }
}
