<?php

namespace App\Admin\Operator;

use App\Admin\AbstractBaseAdmin;
use App\Entity\Operator\Operator;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\DoctrineORMAdminBundle\Filter\DateFilter;
use Sonata\DoctrineORMAdminBundle\Filter\ModelAutocompleteFilter;
use Sonata\Form\Type\CollectionType;
use Sonata\Form\Type\DatePickerType;
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
    protected $translationDomain = 'admin';

    /**
     * @var string
     */
    protected $classnameLabel = 'Partes de trabajo';

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
    protected function configureFormFields(FormMapper $formMapper)
    {
        if ($this->id($this->getSubject())) { // is edit mode, disable on new subjects
            $formMapper
                ->with('Parte de trabajo', $this->getFormMdSuccessBoxArray(6))
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
                        'label' => 'admin.label.total',
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
                ->add(
                    'operatorWorkRegisters',
                    CollectionType::class,
                    [
                        'required' => false,
                        'error_bubbling' => true,
                        'label' => false,
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

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add(
                'operator',
                ModelAutocompleteFilter::class,
                [
                    'label' => 'admin.label.operator',
                ],
                null,
                [
                    'property' => 'name',
                ]
            )
            ->add(
                'date',
                DateFilter::class,
                [
                    'label' => 'admin.label.delivery_note_date',
                    'field_type' => DatePickerType::class,
                ]
            )
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
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
                    'label' => 'admin.label.total',
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
                        'show' => ['template' => 'admin/buttons/list__action_show_button.html.twig'],
                        'edit' => ['template' => 'admin/buttons/list__action_edit_button.html.twig'],
                    ],
                    'label' => 'admin.actions',
                ]
            )
        ;
    }
}
