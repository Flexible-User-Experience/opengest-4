<?php

namespace App\Admin\Setting;

use App\Admin\AbstractBaseAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Sonata\AdminBundle\Route\RouteCollection;
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
    protected $classnameLabel = 'Franjas horaria';

    /**
     * @var string
     */
    protected $baseRoutePattern = 'configuracion/franjas_horarias';

    /**
     * @var array
     */
    protected $datagridValues = array(
        '_sort_by' => 'start',
        '_sort_order' => 'asc',
    );

    /**
     * Methods.
     */

    /**
     * Configure route collection.
     *
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);
        $collection->remove('delete');
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('General', $this->getFormMdSuccessBoxArray(6))
            ->add(
                'description',
                null,
                array(
                    'label' => 'Descripción',
                    'required' => true,
                )
            )
            ->add(
                'start',
                TimeType::class,
                array(
                    'label' => 'Empieza',
                    'required' => true,
                )
            )
            ->add(
                'finish',
                TimeType::class,
                array(
                    'label' => 'Termina',
                    'required' => true,
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
                    'label' => 'Descripción',
                )
            )
            ->add(
                'start',
                null,
                array(
                    'label' => 'Empieza',
                )
            )
            ->add(
                'finish',
                null,
                array(
                    'label' => 'Termina',
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
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add(
                'description',
                null,
                array(
                    'label' => 'Descripción',
                    'editable' => true,
                )
            )
            ->add(
                'start',
                null,
                array(
                    'label' => 'Empieza',
                    'editable' => true,
                )
            )
            ->add(
                'finish',
                null,
                array(
                    'label' => 'Termina',
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
                        'show' => array('template' => 'admin/buttons/list__action_show_button.html.twig'),
                        'edit' => array('template' => 'admin/buttons/list__action_edit_button.html.twig'),
                    ),
                    'label' => 'Accions',
                )
            )
        ;
    }
}
