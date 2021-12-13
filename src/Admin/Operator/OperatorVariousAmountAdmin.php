<?php

namespace App\Admin\Operator;

use App\Admin\AbstractBaseAdmin;
use App\Entity\Operator\Operator;
use Doctrine\ORM\QueryBuilder;
use Exception;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\DoctrineORMAdminBundle\Filter\DateFilter;
use Sonata\Form\Type\DatePickerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

/**
 * Class OperatorVariousAmountAdmin.
 *
 * @category Admin
 *
 * @author Rubèn Hierro <info@rubenhierro.com>
 */
class OperatorVariousAmountAdmin extends AbstractBaseAdmin
{
    /**
     * @var string
     */
    protected $classnameLabel = 'Imports varis';

    /**
     * @var string
     */
    protected $baseRoutePattern = 'operaris/imports-varis';

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

    /**
     * @throws Exception
     */
    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->with('General', $this->getFormMdSuccessBoxArray(4))
            ->add(
                'operator',
                EntityType::class,
                [
                    'label' => 'Operari',
                    'required' => true,
                    'class' => Operator::class,
                    'choice_label' => 'fullName',
                    'query_builder' => $this->rm->getOperatorRepository()->getFilteredByEnterpriseEnabledSortedByNameQB($this->getUserLogedEnterprise()),
                ]
            )
            ->add(
                'date',
                DatePickerType::class,
                [
                    'label' => 'Data',
                    'required' => true,
                    'format' => 'd/M/y',
                    'dp_default_date' => (new \DateTime())->format('d/m/Y'),
                ]
            )
            ->add(
                'units',
                null,
                [
                    'label' => 'Unitats',
                    'required' => false,
                ]
            )
            ->add(
                'description',
                null,
                [
                    'label' => 'Descripció',
                    'required' => false,
                ]
            )
            ->add(
                'priceUnit',
                null,
                [
                    'label' => 'Preu unitat',
                    'required' => false,
                ]
            )
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add(
                'operator',
                null,
                [],
                EntityType::class,
                [
                    'class' => Operator::class,
                    'label' => 'Operari',
                    'query_builder' => $this->rm->getOperatorRepository()->getFilteredByEnterpriseEnabledSortedByNameQB($this->getUserLogedEnterprise()),
                ]
            )
            ->add(
                'date',
                DateFilter::class,
                [
                    'label' => 'Data',
                    'field_type' => DatePickerType::class,
                ]
            )
            ->add(
                'units',
                null,
                [
                    'label' => 'Unitats',
                ]
            )
            ->add(
                'description',
                null,
                [
                    'label' => 'Descripció',
                ]
            )
            ->add(
                'priceUnit',
                null,
                [
                    'label' => 'Preu unitat',
                ]
            )
        ;
    }

    /**
     * @param string $context
     *
     * @return QueryBuilder
     */
    public function createQuery($context = 'list')
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = parent::createQuery($context);
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
                'date',
                'date',
                [
                    'label' => 'Data',
                    'format' => 'd/m/Y',
                    'editable' => false,
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
                'units',
                null,
                [
                    'label' => 'Unitats',
                ]
            )
            ->add(
                'description',
                null,
                [
                    'label' => 'Descripció',
                ]
            )
            ->add(
                'priceUnit',
                null,
                [
                    'label' => 'Preu unitat',
                ]
            )
            ->add(
                '_action',
                'actions',
                [
                    'actions' => [
                        'edit' => ['template' => 'admin/buttons/list__action_edit_button.html.twig'],
                        'delete' => ['template' => 'admin/buttons/list__action_delete_button.html.twig'],
                    ],
                    'label' => 'Accions',
                ]
            )
        ;
    }
}
