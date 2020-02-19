<?php

namespace App\Admin\Web;

use App\Admin\AbstractBaseAdmin;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Sonata\AdminBundle\Route\RouteCollection;

/**
 * Class ServiceAdmin.
 *
 * @category Admin
 */
class ServiceAdmin extends AbstractBaseAdmin
{
    /**
     * @var string
     */
    protected $translationDomain = 'admin';

    /**
     * @var string
     */
    protected $classnameLabel = 'Servei';

    /**
     * @var string
     */
    protected $baseRoutePattern = 'web/servei';

    /**
     * @var array
     */
    protected $datagridValues = array(
        '_sort_by' => 'position',
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
            ->with('admin.with.service', $this->getFormMdSuccessBoxArray(6))
            ->add(
                'name',
                null,
                array(
                    'label' => 'admin.label.name',
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
                'vehicleCategory',
                null,
                array(
                    'label' => 'admin.label.vehicle_category',
                    'required' => false,
                    'query_builder' => $this->rm->getVehicleCategoryRepository()->findEnabledSortedByNameQB(),
                )
            )
            ->add(
                'position',
                null,
                array(
                    'label' => 'admin.label.position',
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
                'vehicleCategory',
                null,
                array(
                    'label' => 'admin.label.vehicle_category',
                ),
                null,
                array(
                    'query_builder' => $this->rm->getVehicleCategoryRepository()->findEnabledSortedByNameQB(),
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
                'fakeMainImage',
                null,
                array(
                    'label' => 'admin.label.image',
                    'template' => 'admin/cells/list__cell_main_image_field.html.twig',
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
                'vehicleCategory',
                null,
                array(
                    'label' => 'admin.label.vehicle_category',
                    'editable' => false,
                    'associated_property' => 'name',
                    'sortable' => true,
                    'sort_field_mapping' => array('fieldName' => 'name'),
                    'sort_parent_association_mappings' => array(array('fieldName' => 'vehicleCategory')),
                )
            )
            ->add(
                'position',
                null,
                array(
                    'label' => 'admin.label.position',
                    'editable' => true,
                )
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
