<?php

namespace App\Admin\Web;

use App\Admin\AbstractBaseAdmin;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

/**
 * Class ComplementAdmin.
 *
 * @category Admin
 */
class ComplementAdmin extends AbstractBaseAdmin
{
    /**
     * @var string
     */
    protected $classnameLabel = 'Accesori';

    /**
     * @var string
     */
    protected $baseRoutePattern = 'web/accesori';

    /**
     * @var array
     */
    protected $datagridValues = [
        '_sort_by' => 'name',
        '_sort_order' => 'asc',
    ];

    /**
     * Methods.
     */

    /**
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        parent::configureRoutes($collection);
        $collection->remove('delete');
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->with('admin.with.complement', $this->getFormMdSuccessBoxArray(6))
            ->add(
                'name',
                null,
                [
                    'label' => 'admin.label.name',
                ]
            )
            ->add(
                'shortDescription',
                null,
                [
                    'label' => 'admin.label.short_description',
                ]
            )
            ->add(
                'description',
                CKEditorType::class,
                [
                    'label' => 'admin.label.description',
                    'config_name' => 'my_config',
                    'required' => true,
                ]
            )
            ->end()
            ->with('admin.with.image', $this->getFormMdSuccessBoxArray(4))
            ->add(
                'mainImageFile',
                FileType::class,
                [
                    'label' => 'admin.label.file',
                    'help' => $this->getMainImageHelperFormMapperWithThumbnail(),
                    'help_html' => true,
                    'required' => false,
                ]
            )
            ->end()
            ->with('admin.with.controls', $this->getFormMdSuccessBoxArray(2))
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
                'name',
                null,
                [
                    'label' => 'admin.label.name',
                ]
            )
            ->add(
                'shortDescription',
                null,
                [
                    'label' => 'admin.label.short_description',
                ]
            )
            ->add(
                'description',
                null,
                [
                    'label' => 'admin.label.description',
                ]
            )
            ->add(
                'enabled',
                null,
                [
                    'label' => 'admin.label.enabled_male',
                ]
            )
        ;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add(
                'mainImage',
                null,
                [
                    'label' => 'admin.label.image',
                    'template' => 'admin/cells/list__cell_main_image_field.html.twig',
                ]
            )
            ->add(
                'name',
                null,
                [
                    'label' => 'admin.label.name',
                    'editable' => true,
                ]
            )
            ->add(
                'shortDescription',
                null,
                [
                    'label' => 'admin.label.short_description',
                    'editable' => true,
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
