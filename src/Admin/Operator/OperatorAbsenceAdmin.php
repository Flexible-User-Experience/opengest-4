<?php

namespace App\Admin\Operator;

use App\Admin\AbstractBaseAdmin;
use App\Entity\Operator\Operator;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\DoctrineORMAdminBundle\Filter\DateFilter;
use Sonata\Form\Type\DatePickerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

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
                    'label' => 'Tipus absència',
                    'required' => true,
                    'query_builder' => $this->rm->getOperatorAbsenceTypeRepository()->getEnabledSortedByNameQB(),
                ]
            )
            ->add(
                'begin',
                DatePickerType::class,
                [
                    'label' => 'Data inici',
                    'format' => 'd/M/y',
                    'required' => true,
                ]
            )
            ->add(
                'end',
                DatePickerType::class,
                [
                    'label' => 'Data fi',
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
                    'label' => 'Operari',
                ]
            )
            ->add(
                'type',
                null,
                [
                    'label' => 'Tipus absència',
                ]
            )
            ->add(
                'begin',
                DateFilter::class,
                [
                    'label' => 'Data inici',
                    'field_type' => DatePickerType::class,
                ]
            )
            ->add(
                'end',
                DateFilter::class,
                [
                    'label' => 'Data fi',
                    'field_type' => DatePickerType::class,
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
                    'label' => 'Estat',
                    'template' => 'admin/cells/list__cell_operator_absence_status.html.twig',
                ]
            )
            ->add(
                'begin',
                'date',
                [
                    'label' => 'Data inici',
                    'format' => 'd/m/Y',
                    'editable' => true,
                ]
            )
            ->add(
                'end',
                'date',
                [
                    'label' => 'Data fi',
                    'format' => 'd/m/Y',
                    'editable' => true,
                ]
            )
            ->add(
                'operator.profilePhotoImage',
                null,
                [
                    'label' => 'Imatge',
                    'template' => 'admin/cells/list__cell_operator_profile_image_field.html.twig',
                ]
            )
            ->add(
                'operator',
                null,
                [
                    'label' => 'Operari',
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
                    'label' => 'Tipus absència',
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
                    'label' => 'Accions',
                ]
            )
        ;
    }
}
