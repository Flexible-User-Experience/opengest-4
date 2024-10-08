<?php

namespace App\Admin\Sale;

use App\Admin\AbstractBaseAdmin;
use App\Enum\SaleItemTypeEnum;
use Exception;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * Class SaleItemAdmin.
 *
 * @category    Admin
 */
class SaleItemAdmin extends AbstractBaseAdmin
{
    /**
     * @var string
     */
    protected $classnameLabel = 'Items';

    /**
     * @var array
     */
    protected $datagridValues = [
        '_sort_by' => 'id',
        '_sort_order' => 'ASC',
    ];

    /**
     * Methods.
     */
    public function generateBaseRoutePattern(bool $isChildAdmin = false): string
    {
        return 'vendes/items';
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
                'unitPrice',
                null,
                [
                    'label' => 'admin.label.unit_price',
                ]
            )
            ->add(
                'type',
                ChoiceType::class,
                [
                    'choices' => SaleItemTypeEnum::getEnumArray(),
                    'label' => 'admin.label.type',
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
                'id',
                null,
                [
                    'label' => 'Id',
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
                'unitPrice',
                null,
                [
                    'label' => 'admin.label.unit_price',
                ]
            )
            ->add(
                'type',
                null,
                [
                    'label' => 'admin.label.type',
                    'field_type' => ChoiceType::class,
                    'field_options' => [
                            'choices' => SaleItemTypeEnum::getEnumArray(),
                        ],
                ],
            )
        ;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add(
                'id',
                null,
                [
                    'label' => 'Id',
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
                'unitPrice',
                null,
                [
                    'label' => 'admin.label.unit_price',
                ]
            )
            ->add(
                'type',
                ChoiceType::class,
                [
                    'choices' => SaleItemTypeEnum::getEnumArray(),
                    'label' => 'admin.label.type',
                ]
            )
            ->add(
                '_action',
                'actions',
                [
                    'actions' => [
                        'edit' => ['template' => 'admin/buttons/list__action_edit_sale_item_button.html.twig'],
                        'delete' => ['template' => 'admin/buttons/list__action_delete_sale_item_button.html.twig'],
                    ],
                    'label' => 'admin.actions',
                ]
            )
        ;
    }
}
