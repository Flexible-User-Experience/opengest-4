<?php

namespace App\Admin\Operator;

use App\Admin\AbstractBaseAdmin;
use App\Entity\Operator\Operator;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\DoctrineORMAdminBundle\Filter\DateRangeFilter;
use Sonata\DoctrineORMAdminBundle\Filter\ModelFilter;
use Sonata\Form\Type\CollectionType;
use Sonata\Form\Type\DateRangePickerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

/**
 * Class OperatorWorkRegisterHeaderAdmin.
 *
 * @category Admin
 *
 * @author Jordi Sort <jordi.sort@mirmit.com>
 */
class OperatorWorkRegisterHeaderAdmin extends AbstractBaseAdmin
{
    /**
     * @var string
     */
    protected $classnameLabel = 'operator_work_register';

    /**
     * @var string
     */
    protected $baseRoutePattern = 'operaris/partes-trabajo-cabecera';

    /**
     * @var array
     */
    protected $datagridValues = [
        '_sort_by' => 'date',
        '_sort_order' => 'desc',
    ];

    /**
     * Methods.
     */
    public function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection
            ->add('batch')
            ->add('getJsonOperatorWorkRegisterTotalsByHourType', 'getJsonOperatorWorkRegisters')
            ->add('createTimeSummary', 'create-time-summary')
        ;
    }

    /**
     * @param array $actions
     */
    public function configureBatchActions($actions): array
    {
        $newActions['selectOption'] = [
            'label' => 'admin.action.select_option',
            'ask_confirmation' => false,
        ];
        if ($this->hasRoute('edit') && $this->hasAccess('edit')) {
            $newActions['generateWorkRegisterReportPdf'] = [
                'label' => 'admin.action.generate_work_register_report',
                'ask_confirmation' => false,
            ];
            $newActions['generateTimeSummary'] = [
                'label' => 'admin.action.generate_time_summary',
                'ask_confirmation' => false,
            ];
        }

        return array_merge($newActions, $actions);
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        if ($this->id($this->getSubject())) { // is edit mode, disable on new subjects
            $formMapper
                ->with('Parte de trabajo', $this->getFormMdSuccessBoxArray(3))
                ->add(
                    'operator',
                    EntityType::class,
                    [
                        'label' => 'admin.label.operator',
                        'required' => true,
                        'disabled' => true,
                        'class' => Operator::class,
                        'choice_label' => 'fullName',
                        'query_builder' => $this->rm->getOperatorRepository()->getFilteredByEnterpriseEnabledSortedByNameQB($this->getUserLogedEnterprise()),
                    ]
                )
                ->add(
                    'date',
                    DateType::class,
                    [
                        'label' => 'admin.label.date',
                        'required' => true,
                        'disabled' => true,
                        'widget' => 'single_text',
                    ]
                )
                ->add(
                    'totalAmount',
                    MoneyType::class,
                    [
                        'label' => 'Total (€)',
                        'disabled' => true,
                    ]
                )
                ->add(
                    'hours',
                    NumberType::class,
                    [
                        'label' => 'admin.label.hours',
                        'disabled' => true,
                    ]
                )
                ->end()
                ->with('Líneas', $this->getFormMdSuccessBoxArray(12))
                ->add(
                    'operatorWorkRegisters',
                    CollectionType::class,
                    [
                        'required' => false,
                        'error_bubbling' => true,
                        'label' => false,
                        'btn_add' => false,
                        'type_options' => ['delete' => false],
                    ],
                    [
                        'edit' => 'inline',
                        'inline' => 'table',
                    ]
                )
                ->end();
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
                    ]
                )
                ->add(
                    'date',
                    DateType::class,
                    [
                        'label' => 'admin.label.date',
                        'required' => true,
                        'widget' => 'single_text',
                    ]
                )
                ->end();
        }
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add(
                'operator',
                ModelFilter::class,
                [
                    'label' => 'admin.label.operator',
                    'field_type' => ModelAutocompleteType::class,
                    'field_options' => [
                            'property' => ['name', 'surname1', 'surname2'],
                        ],
                    'show_filter' => true,
                ]
            )
            ->add(
                'date',
                DateRangeFilter::class,
                [
                    'label' => 'admin.label.date',
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

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add(
                'operator',
                null,
                [
                    'label' => 'admin.label.operator',
                ]
            )
            ->add(
                'date',
                null,
                [
                    'label' => 'admin.label.date',
                    'format' => 'd/m/y',
                ]
            )
            ->add(
                'totalAmount',
                null,
                [
                    'label' => 'Total (€)',
                ]
            )
            ->add(
                'hours',
                null,
                [
                    'label' => 'admin.label.hours',
                ]
            )
            ->add(
                '_action',
                'actions',
                [
                    'actions' => [
                        'edit' => ['template' => 'admin/buttons/list__action_edit_operator_work_register_button.html.twig'],
                    ],
                    'label' => 'admin.actions',
                ]
            )
        ;
    }
}
