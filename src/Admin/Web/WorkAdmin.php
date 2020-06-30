<?php

namespace App\Admin\Web;

use App\Admin\AbstractBaseAdmin;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\Form\Type\CollectionType;
use Sonata\Form\Type\DatePickerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Sonata\AdminBundle\Route\RouteCollection;

/**
 * Class WorkAdmin.
 *
 * @category Admin
 */
class WorkAdmin extends AbstractBaseAdmin
{
    /**
     * @var string
     */
    protected $translationDomain = 'admin';

    /**
     * @var string
     */
    protected $classnameLabel = 'Treball';

    /**
     * @var string
     */
    protected $baseRoutePattern = 'web/treball';

    /**
     * @var array
     */
    protected $datagridValues = array(
        '_sort_by' => 'date',
        '_sort_order' => 'desc',
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
            ->with('admin.with.work', $this->getFormMdSuccessBoxArray(6))
            ->add(
                'name',
                null,
                array(
                    'label' => 'admin.label.name',
                )
            )
            ->add(
                'shortDescription',
                null,
                array(
                    'label' => 'admin.label.short_description',
                )
            )
            ->add(
                'description',
                CKEditorType::class,
                array(
                    'label' => 'admin.label.description',
                    'config_name' => 'my_config',
                    'required' => true,
                )
            )
            ->end()
            ->with('admin.with.image', $this->getFormMdSuccessBoxArray(4))
            ->add(
                'mainImageFile',
                FileType::class,
                array(
                    'label' => 'admin.label.file',
                    'help' => $this->getMainImageHelperFormMapperWithThumbnail(),
                    'required' => false,
                )
            )
            ->end()
            ->with('admin.with.controls', $this->getFormMdSuccessBoxArray(2))
            ->add(
                'date',
                DatePickerType::class,
                array(
                    'label' => 'admin.label.date',
                    'format' => 'd/M/y',
                    'required' => true,
                )
            )
            ->add(
                'service',
                null,
                array(
                    'label' => 'admin.label.service',
                    'required' => false,
                    'query_builder' => $this->rm->getServiceRepository()->findEnabledSortedByNameQB(),
                )
            )
            ->add(
                'enabled',
                CheckboxType::class,
                array(
                    'label' => 'admin.label.enabled_male',
                    'required' => false,
                )
            )
            ->end()
        ;
        if ($this->id($this->getSubject())) { // is edit mode, disable on new subjetcs
            $formMapper
                ->with('admin.with.secondary_images', $this->getFormMdSuccessBoxArray(12))
                ->add(
                    'images',
                    CollectionType::class,
                    array(
                        'label' => 'admin.label.files',
                        'required' => true,
                        'cascade_validation' => true,
                        'error_bubbling' => true,
                        'by_reference' => false,
                    ),
                    array(
                        'edit' => 'inline',
                        'inline' => 'table',
                        'sortable' => 'position',
                    )
                )
                ->end()
            ;
        }
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add(
                'date',
                'doctrine_orm_date',
                array(
                    'label' => 'admin.label.date',
                    'field_type' => DatePickerType::class,
                ),
                null,
                array(
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy',
                )
            )
            ->add(
                'name',
                null,
                array(
                    'label' => 'admin.label.name',
                )
            )
            ->add(
                'shortDescription',
                null,
                array(
                    'label' => 'admin.label.short_description',
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
                'service',
                null,
                array(
                    'label' => 'admin.label.service',
                ),
                null,
                array(
                    'query_builder' => $this->rm->getServiceRepository()->findEnabledSortedByNameQB(),
                )
            )
            ->add(
                'enabled',
                null,
                array(
                    'label' => 'admin.label.enabled_male',
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
                'mainImage',
                null,
                array(
                    'label' => 'admin.label.image',
                    'template' => 'admin/cells/list__cell_main_image_field.html.twig',
                )
            )
            ->add(
                'date',
                'date',
                array(
                    'label' => 'admin.label.date',
                    'format' => 'd/m/Y',
                    'editable' => true,
                )
            )
            ->add(
                'name',
                null,
                array(
                    'label' => 'admin.label.name',
                    'editable' => true,
                )
            )
            ->add(
                'shortDescription',
                null,
                array(
                    'label' => 'admin.label.short_description',
                    'editable' => true,
                )
            )
            ->add(
                'service',
                null,
                array(
                    'label' => 'admin.label.service',
                    'editable' => false,
                    'associated_property' => 'name',
                    'sortable' => true,
                    'sort_field_mapping' => array('fieldName' => 'name'),
                    'sort_parent_association_mappings' => array(array('fieldName' => 'service')),                )
            )
            ->add(
                'enabled',
                null,
                array(
                    'label' => 'admin.label.enabled_male',
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
