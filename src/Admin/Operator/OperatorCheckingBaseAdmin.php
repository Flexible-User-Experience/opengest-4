<?php

namespace App\Admin\Operator;

use App\Admin\AbstractBaseAdmin;
use App\Entity\Operator\Operator;
use Sonata\AdminBundle\Datagrid\DatagridInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\DoctrineORMAdminBundle\Filter\DateFilter;
use Sonata\DoctrineORMAdminBundle\Filter\DateRangeFilter;
use Sonata\Form\Type\DatePickerType;
use Sonata\Form\Type\DateRangePickerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

/**
 * Class OperatorCheckingBaseAdmin.
 *
 * @category Admin
 *
 * @author   Jordi Sort <jordi.sort@mirmit.com>
 */
class OperatorCheckingBaseAdmin extends AbstractBaseAdmin
{
    /**
     * Methods.
     */
    protected function configureDefaultSortValues(array &$sortValues): void
    {
        $sortValues[DatagridInterface::SORT_ORDER] = 'ASC';
        $sortValues[DatagridInterface::SORT_BY] = 'end';
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        if ($this->getCode() === $this->getRootCode()) {
            $formMapper
                ->with('General', $this->getFormMdSuccessBoxArray(6))
                ->add(
                    'operator',
                    EntityType::class,
                    [
                        'label' => 'admin.label.operator',
                        'required' => true,
                        'class' => Operator::class,
                        'choice_label' => 'fullName',
                        'query_builder' => $this->rm->getOperatorRepository()->getFilteredByEnterpriseEnabledSortedByNameQB($this->getUserLogedEnterprise()),
                        'placeholder' => '--- seleccione una opción ---',
                    ]
                )
            ;
        } else {
            $formMapper
                ->with('General', $this->getFormMdSuccessBoxArray(6))
                ->add(
                    'operator',
                    EntityType::class,
                    [
                        'label' => 'admin.label.operator',
                        'required' => true,
                        'class' => Operator::class,
                        'choice_label' => 'fullName',
                        'query_builder' => $this->rm->getOperatorRepository()->getFilteredByEnterpriseEnabledSortedByNameQB($this->getUserLogedEnterprise()),
                        'attr' => [
                            'hidden' => 'true',
                        ],
                    ]
                )
            ;
        }
        $formMapper
            ->add(
                'type',
                null,
                [
                    'label' => 'admin.with.operator_checking_type',
                    'required' => true,
                    'query_builder' => $this->rm->getOperatorCheckingTypeRepository()->getEnabledSortedByNameQB(),
                ]
            )
            ->add(
                'begin',
                DatePickerType::class,
                [
                    'label' => 'admin.label.expedition_date',
                    'format' => 'd/M/y',
                    'required' => true,
                ]
            )
            ->add(
                'end',
                DatePickerType::class,
                [
                    'label' => 'admin.label.expiry_date',
                    'format' => 'd/M/y',
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
                'operator',
                null,
                [
                    'label' => 'admin.label.operator',
                ]
            )
            ->add(
                'type',
                null,
                [
                    'label' => 'admin.with.operator_checking_type',
                ]
            )
            ->add(
                'begin',
                DateFilter::class,
                [
                    'label' => 'admin.label.expedition_date',
                    'field_type' => DatePickerType::class,
                    'format' => 'd/m/Y',
                    'field_options' => [
                            'widget' => 'single_text',
                            'format' => 'dd/MM/yyyy',
                        ],
                ]
            )
            ->add(
                'end',
                DateRangeFilter::class,
                [
                    'label' => 'admin.label.expiry_date',
                    'field_type' => DateRangePickerType::class,
                    'field_options' => [
                        'field_options_start' => [
                            'label' => 'Desde',
                            'format' => 'dd/MM/yyyy',
                        ],
                        'field_options_end' => [
                            'label' => 'Hasta',
                            'format' => 'dd/MM/yyyy',
                        ],
                    ],
                    'show_filter' => true,
                ]
            )
        ;
    }

    public function configureQuery(ProxyQueryInterface $query): ProxyQueryInterface
    {
        $queryBuilder = parent::configureQuery($query);
        $queryBuilder
            ->join($queryBuilder->getRootAliases()[0].'.operator', 'op')
            ->andWhere('op.enterprise = :enterprise')
            ->andWhere('op.enabled = :enabled')
            ->setParameter('enterprise', $this->getUserLogedEnterprise())
            ->setParameter('enabled', true)
        ;

        return $queryBuilder;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add(
                'status',
                null,
                [
                    'label' => 'admin.label.status',
                    'template' => 'admin/cells/list__cell_operator_checking_status.html.twig',
                    'mapped' => false,
                ]
            )
            ->add(
                'begin',
                'date',
                [
                    'label' => 'admin.label.expedition_date',
                    'format' => 'd/m/Y',
                    'editable' => true,
                ]
            )
            ->add(
                'end',
                'date',
                [
                    'label' => 'admin.label.expiry_date',
                    'format' => 'd/m/Y',
                    'editable' => true,
                ]
            )
            ->add(
                'operator.profilePhotoImage',
                null,
                [
                    'label' => 'admin.label.image',
                    'template' => 'admin/cells/list__cell_operator_profile_image_field.html.twig',
                ]
            )
            ->add(
                'operator',
                null,
                [
                    'label' => 'admin.label.operator',
                    'editable' => false,
                    'associated_property' => 'fullName',
                    'sortable' => true,
                    'sort_field_mapping' => ['fieldName' => 'surname1'],
                    'sort_parent_association_mappings' => [['fieldName' => 'operator']],
                ]
            )
            ->add(
                'type',
                null,
                [
                    'label' => 'admin.with.operator_checking_type',
                    'editable' => false,
                    'associated_property' => 'name',
                    'sortable' => true,
                    'sort_field_mapping' => ['fieldName' => 'name'],
                    'sort_parent_association_mappings' => [['fieldName' => 'type']],
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
                    'label' => 'Acciones',
                ]
            )
        ;
    }
}
