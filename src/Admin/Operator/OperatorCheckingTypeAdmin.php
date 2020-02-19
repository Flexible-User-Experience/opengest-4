<?php

namespace App\Admin\Operator;

use App\Admin\AbstractBaseAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

/**
 * Class OperatorCheckingTypeAdmin.
 *
 * @category Admin
 */
class OperatorCheckingTypeAdmin extends AbstractBaseAdmin
{
    /**
     * @var string
     */
    protected $translationDomain = 'admin';

    /**
     * @var string
     */
    protected $classnameLabel = 'Tipus revisió';

    /**
     * @var string
     */
    protected $baseRoutePattern = 'operaris/tipus-revisio';

    /**
     * @var array
     */
    protected $datagridValues = array(
        '_sort_by' => 'name',
        '_sort_order' => 'asc',
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
        $collection->remove('delete');
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('admin.with.operator_checking_type', $this->getFormMdSuccessBoxArray(6))
            ->add(
                'name',
                null,
                array(
                    'label' => 'admin.label.name',
                )
            )
            ->add(
                'description',
                TextareaType::class,
                array(
                    'label' => 'admin.label.description',
                    'attr' => array(
                        'style' => 'resize: vertical',
                        'rows' => 7,
                    ),
                )
            )
            ->end()
            ->with('Controls', $this->getFormMdSuccessBoxArray(6))
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
                'name',
                null,
                array(
                    'label' => 'admin.label.name',
                )
            )
            ->add(
                'description',
                null,
                array(
                    'label' => 'admin.label.description',
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
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add(
                'name',
                null,
                array(
                    'label' => 'admin.label.name',
                    'editable' => true,
                )
            )
            ->add(
                'description',
                null,
                array(
                    'label' => 'admin.label.description',
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
                    ),
                    'label' => 'admin.actions',
                )
            )
        ;
    }
}
