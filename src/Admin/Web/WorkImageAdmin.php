<?php

namespace App\Admin\Web;

use App\Admin\AbstractBaseAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\FileType;

/**
 * Class WorkImageAdmin.
 *
 * @category Admin
 */
class WorkImageAdmin extends AbstractBaseAdmin
{
    /**
     * @var string
     */
    protected $classnameLabel = 'Imatge Treball';

    /**
     * @var string
     */
    protected $baseRoutePattern = 'web/imatge-treball';

    /**
     * @var array
     */
    protected $datagridValues = [
        '_sort_by' => 'position',
        '_sort_order' => 'asc',
    ];

    /**
     * Methods.
     */
    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->with('admin.with.image', $this->getFormMdSuccessBoxArray(6))
            ->add(
                'imageFile',
                FileType::class,
                [
                    'label' => 'admin.label.file',
                    'help' => $this->getImageHelperFormMapperWithThumbnail(),
                    'sonata_help' => $this->getImageHelperFormMapperWithThumbnail(),
                    'required' => false,
                ]
            )
            ->add(
                'alt',
                null,
                [
                    'label' => 'admin.label.alt',
                ]
            )
            ->add(
                'position',
                null,
                [
                    'label' => 'admin.label.position',
                ]
            )
            ->add(
                'work',
                null,
                [
                    'attr' => [
                        'hidden' => true,
                    ],
                    'required' => true,
                ]
            )
            ->end()
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add(
                'alt',
                null,
                [
                    'label' => 'admin.label.alt',
                ]
            )
            ->add(
                'position',
                null,
                [
                    'label' => 'admin.label.position',
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

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add(
                'image',
                null,
                [
                    'label' => 'admin.label.image',
                    'template' => 'admin/cells/list__cell_image_field.html.twig',
                ]
            )
            ->add(
                'alt',
                null,
                [
                    'label' => 'admin.label.alt',
                    'editable' => true,
                ]
            )
            ->add(
                'position',
                null,
                [
                    'label' => 'admin.label.position',
                    'editable' => true,
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
                    ],
                    'label' => 'admin.actions',
                ]
            )
        ;
    }
}
