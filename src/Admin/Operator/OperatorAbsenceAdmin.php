<?php

namespace App\Admin\Operator;

use App\Admin\AbstractBaseAdmin;
use App\Entity\Operator\Operator;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\DoctrineORMAdminBundle\Filter\DateRangeFilter;
use Sonata\Form\Type\DatePickerType;
use Sonata\Form\Type\DateRangePickerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

/**
 * Class OperatorAbsenceAdmin.
 *
 * @category Admin
 *
 * @author   Wils Iglesias <wiglesias83@gmail.com>
 */
class OperatorAbsenceAdmin extends AbstractBaseAdmin
{
    /**
     * @var string
     */
    protected $classnameLabel = 'Absències';

    /**
     * @var string
     */
    protected $baseRoutePattern = 'operaris/absencia';

    /**
     * @var array
     */
    protected $datagridValues = [
        '_sort_by' => 'begin',
        '_sort_order' => 'desc',
    ];

    /**
     * Methods.
     */
    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        parent::configureRoutes($collection);
        $collection->remove('delete');
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
                        'data' => $this->getOperatorFromPreviousPage(),
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
                    'label' => 'admin.with.operator_absence_type',
                    'required' => true,
                    'query_builder' => $this->rm->getOperatorAbsenceTypeRepository()->getEnabledSortedByNameQB(),
                ]
            )
            ->add(
                'begin',
                DatePickerType::class,
                [
                    'label' => 'admin.label.start',
                    'format' => 'dd/MM/yyyy',
                    'required' => true,
                ]
            )
            ->add(
                'end',
                DatePickerType::class,
                [
                    'label' => 'admin.label.finish',
                    'format' => 'dd/MM/yyyy',
                    'required' => true,
                ]
            )
            ->add(
                'toPreviousYearCount',
                CheckboxType::class,
                [
                    'label' => 'admin.label.to_previous_year_count',
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
                    'label' => 'admin.with.operator_absence_type',
                ]
            )
            ->add(
                'begin',
                DateRangeFilter::class,
                [
                    'label' => 'admin.label.start',
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
                ]
            )
            ->add(
                'end',
                DateRangeFilter::class,
                [
                    'label' => 'admin.label.end',
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
            ->orderBy($queryBuilder->getRootAliases()[0].'.begin', 'DESC')
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
                    'template' => 'admin/cells/list__cell_operator_absence_status.html.twig',
                ]
            )
            ->add(
                'begin',
                'date',
                [
                    'label' => 'admin.label.start',
                    'format' => 'd/m/Y',
                    'editable' => true,
                ]
            )
            ->add(
                'end',
                'date',
                [
                    'label' => 'admin.label.finish',
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
                EntityType::class,
                [
                    'class' => Operator::class,
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
                    'label' => 'admin.with.operator_absence_type',
                    'editable' => true,
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
                    'label' => 'admin.actions',
                ]
            )
        ;
    }

    private function getOperatorFromPreviousPage(): ?Operator
    {
        $operatorId = $this->getRequest()->query->get('operatorId');
        if ($operatorId) {
            $operator = $this->rm->getOperatorRepository()->find($operatorId);
        } else {
            $operator = $this->getSubject()->getOperator();
        }

        return $operator;
    }
}
