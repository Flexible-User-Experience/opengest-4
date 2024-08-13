<?php

namespace App\Admin\Operator;

use App\Admin\AbstractBaseAdmin;
use App\Enum\OperatorCheckingTypeCategoryEnum;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
    protected $classnameLabel = 'Tipus revisió';

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
    public function generateBaseRoutePattern(bool $isChildAdmin = false): string
    {
        return 'operaris/tipus-revisio';
    }

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
            ->with('admin.with.operator_checking_type', $this->getFormMdSuccessBoxArray(6))
            ->add(
                'category',
                ChoiceType::class,
                [
                    'choices' => OperatorCheckingTypeCategoryEnum::getEnumArray(),
                    'label' => 'admin.label.group',
                ]
            )
            ->add(
                'name',
                null,
                [
                    'label' => 'admin.label.name',
                ]
            )
            ->add(
                'description',
                TextareaType::class,
                [
                    'label' => 'admin.label.description',
                    'attr' => [
                        'style' => 'resize: vertical',
                        'rows' => 7,
                    ],
                ]
            )
            ->end()
            ->with('Controls', $this->getFormMdSuccessBoxArray(6))
            ->add(
                'enabled',
                CheckboxType::class,
                [
                    'label' => 'admin.label.enabled_female',
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
                    'label' => 'admin.label.enabled_female',
                ]
            )
        ;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add(
                'category',
                null,
                [
                    'label' => 'admin.label.group',
                    'template' => 'admin/cells/list__cell_operator_checking_type_category.html.twig',
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
